# Instala y carga los paquetes necesarios
if (!requireNamespace("shiny", quietly = TRUE)) install.packages("shiny")
if (!requireNamespace("shinyjs", quietly = TRUE)) install.packages("shinyjs")
if (!requireNamespace("plotly", quietly = TRUE)) install.packages("plotly")
if (!requireNamespace("visNetwork", quietly = TRUE)) install.packages("visNetwork")
if (!requireNamespace("h2o", quietly = TRUE)) install.packages("h2o")

# Cargar las bibliotecas
library(shiny)
library(shinyjs)
library(plotly)
library(visNetwork)
library(h2o)

# Inicializa H2O
h2o.init(nthreads = -1, max_mem_size = "2G")

# Cerrar H2O al cerrar la app
onStop(function() {
  h2o.shutdown(prompt = FALSE)
})

# Interfaz de usuario (UI)
ui <- fluidPage(
  useShinyjs(),
  titlePanel("Visualización en Tiempo Real de la Red Neuronal con H2O"),
  
  sidebarLayout(
    sidebarPanel(
      fileInput("file", "Cargar archivo CSV", accept = ".csv"),
      actionButton("process_data", "Procesar y Limpiar Datos"),
      selectInput("target_column", "Columna Objetivo", choices = NULL),
      textInput("hidden_layers", "Capas de la Red (separadas por comas, ej: 10,10,10)", value = "10,10,10"),
      numericInput("epochs", "Número de Épocas", value = 50, min = 1),
      actionButton("start_training", "Iniciar Entrenamiento"),
      hr(),
      h3("Predicción con el Modelo Entrenado"),
      uiOutput("prediction_input_ui"),
      actionButton("predict_button", "Realizar Predicción"),
      verbatimTextOutput("prediction_output")
    ),
    mainPanel(
      h3("Previsualización de Datos"),
      tableOutput("data_preview"),
      h3("Gráfico de Pérdida en Tiempo Real"),
      plotlyOutput("loss_plot"),
      h3("Progreso del Entrenamiento"),
      verbatimTextOutput("epoch_progress"),
      h3("Visualización de la Red Neuronal"),
      visNetworkOutput("network_visualization"),
      h3("Consola de Mensajes"),
      verbatimTextOutput("console_output")
    )
  )
)

# Lógica del servidor
# Lógica del servidor
server <- function(input, output, session) {
  data <- reactiveVal(NULL)
  processed_data <- reactiveVal(NULL)
  model <- reactiveVal(NULL)
  loss_history <- reactiveVal(NULL)
  epoch_progress <- reactiveVal("Época: 0 | Pérdida (RMSE): NA")
  
  observeEvent(input$file, {
    req(input$file)
    df <- read.csv(input$file$datapath, stringsAsFactors = FALSE)
    data(df)
    output$data_preview <- renderTable(head(df))
  })
  
  observeEvent(input$process_data, {
    req(data())
    df <- data()
    df <- df[complete.cases(df), ]  # Eliminar filas con valores faltantes
    
    # Manejo de columnas numéricas como texto
    for (col in colnames(df)) {
      if (is.character(df[[col]]) && all(grepl("^[0-9]+(\\.[0-9]+)?$", df[[col]]))) {
        df[[col]] <- as.numeric(df[[col]])
      }
    }
    
    # Filtrar columnas constantes
    valid_cols <- sapply(df, function(col) length(unique(col)) > 1)
    df <- df[, valid_cols, drop = FALSE]
    
    processed_data(df)
    updateSelectInput(session, "target_column", choices = colnames(df))
    output$data_preview <- renderTable(head(df))
  })
  
  observeEvent(input$start_training, {
    req(processed_data())
    df <- processed_data()
    target <- input$target_column
    predictors <- setdiff(names(df), target)
    
    df_h2o <- as.h2o(df)
    hidden_layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    
    losses <- c()
    
    for (epoch in 1:input$epochs) {
      model_h2o <- h2o.deeplearning(
        x = predictors,
        y = target,
        training_frame = df_h2o,
        hidden = hidden_layers,
        epochs = 1,
        seed = 1234,
        overwrite_with_best_model = FALSE
      )
      
      history <- h2o.scoreHistory(model_h2o)
      losses <- c(losses, history$training_rmse[nrow(history)])
      
      # Actualiza el progreso de la época
      epoch_progress(paste("Época:", epoch, "| Pérdida (RMSE):", round(losses[epoch], 4)))
      output$epoch_progress <- renderText(epoch_progress())
      Sys.sleep(0.1)
    }
    
    loss_history(losses)
    model(model_h2o)
    output$console_output <- renderText("Entrenamiento completado exitosamente.")
  })
  
  # Gráfico de pérdida
  output$loss_plot <- renderPlotly({
    req(loss_history())
    
    loss_data <- data.frame(
      epoch = 1:length(loss_history()),
      loss = loss_history()
    )
    
    plot_ly(loss_data, x = ~epoch, y = ~loss, type = "scatter", mode = "lines+markers",
            marker = list(color = "dodgerblue")) %>%
      layout(
        title = "Pérdida durante el Entrenamiento",
        xaxis = list(title = "Épocas"),
        yaxis = list(title = "Pérdida (RMSE)"),
        yaxis = list(title = "Pérdida (RMSE)", rangemode = "tozero")
      )
  })
  
  
  # Visualización de la red neuronal
  # Visualización de la red neuronal mejorada
  output$network_visualization <- renderVisNetwork({
    req(input$hidden_layers)
    
    layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    layer_sizes <- c(ncol(processed_data()) - 1, layers, 1)
    
    nodes <- data.frame(id = integer(), label = character(), x = numeric(), y = numeric(), group = integer())
    y_offset <- 100
    node_id <- 1
    
    for (i in seq_along(layer_sizes)) {
      x_pos <- i * 200
      y_positions <- seq(-layer_sizes[i] / 2 * y_offset, layer_sizes[i] / 2 * y_offset, length.out = layer_sizes[i])
      new_nodes <- data.frame(
        id = node_id:(node_id + layer_sizes[i] - 1),
        label = paste("Capa", i, "Neuron", 1:layer_sizes[i]),
        x = rep(x_pos, layer_sizes[i]),
        y = y_positions,
        group = i
      )
      nodes <- rbind(nodes, new_nodes)
      node_id <- node_id + layer_sizes[i]
    }
    
    links <- data.frame()
    for (i in 1:(length(layer_sizes) - 1)) {
      from_nodes <- nodes$id[nodes$group == i]
      to_nodes <- nodes$id[nodes$group == i + 1]
      
      simulated_weights <- runif(length(from_nodes) * length(to_nodes), -1, 1)
      
      new_links <- expand.grid(from = from_nodes, to = to_nodes)
      new_links$weight <- abs(simulated_weights) * 5
      new_links$color <- scales::col_numeric(c("red", "blue"), domain = c(-1, 1))(simulated_weights)
      
      links <- rbind(links, new_links)
    }
    
    visNetwork(nodes, links, height = "600px", width = "100%") %>%
      visNodes(shape = "dot", scaling = list(min = 20, max = 30)) %>%
      visEdges(arrows = "to", width = "~weight", color = list(color = "~color")) %>%
      visLayout(randomSeed = 123) %>%
      visPhysics(enabled = FALSE) %>%
      visOptions(manipulation = FALSE)
  })
}


# Crear la aplicación
shinyApp(ui = ui, server = server)

