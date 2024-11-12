# Cargar las bibliotecas necesarias
library(shiny)
library(shinyjs)
library(plotly)
library(visNetwork)
library(h2o)

# Inicializa H2O
h2o.init()

# Interfaz de usuario (UI)
ui <- fluidPage(
  useShinyjs(),
  titlePanel("Visualización en Tiempo Real de la Red Neuronal con H2O"),
  
  # Cargar y mostrar archivo de datos
  fileInput("file", "Cargar archivo CSV"),
  actionButton("process_data", "Procesar y Limpiar Datos"),
  
  # Vista previa de los datos
  h3("Previsualización de Datos"),
  tableOutput("data_preview"),
  
  # Configuración del modelo
  selectInput("target_column", "Columna Objetivo", choices = NULL),
  textInput("hidden_layers", "Capas de la Red (ej: 10,10,10)", value = "10,10,10"),
  numericInput("epochs", "Número de Épocas", value = 50, min = 1),
  actionButton("start_training", "Iniciar Entrenamiento"),
  
  # Gráfico de pérdida
  h3("Gráfico de Pérdida en Tiempo Real"),
  plotlyOutput("loss_plot"),
  
  # Progreso del entrenamiento
  h3("Progreso del Entrenamiento"),
  verbatimTextOutput("epoch_progress"),
  
  # Visualización de la red neuronal
  h3("Visualización de la Red Neuronal"),
  visNetworkOutput("network_visualization"),
  
  # Consola de mensajes
  h3("Consola de Mensajes"),
  verbatimTextOutput("console_output"),
  
  # Prueba del modelo de predicción
  titlePanel("Prueba del Modelo de Predicción"),
  fluidRow(
    column(4, uiOutput("predict_columns_ui")),
    column(4, textInput("target_column_input", "Valor predicho para columna objetivo", value = ""))
  ),
  actionButton("predict_button", "Realizar Predicción"),
  verbatimTextOutput("prediction_result")
)

# Lógica del servidor
server <- function(input, output, session) {
  data <- reactiveVal(NULL)
  processed_data <- reactiveVal(NULL)
  loss_history <- reactiveVal(NULL)
  epoch_progress <- reactiveVal("Época: 0 | Pérdida (RMSE): NA")
  model_h2o <- reactiveVal(NULL)
  
  # Carga de datos
  observeEvent(input$file, {
    req(input$file)
    df <- read.csv(input$file$datapath)
    data(df)
    output$data_preview <- renderTable(head(df))
  })
  
  # Procesamiento de datos
  observeEvent(input$process_data, {
    req(data())
    df <- data()
    
    # Limpiar filas con valores vacíos
    df <- df[complete.cases(df), ]
    
    clean_numeric_columns <- function(df) {
      cleaned_columns <- c()
      
      for (column in colnames(df)) {
        if (is.character(df[[column]]) && any(grepl("[0-9]", df[[column]]))) {
          if (column != "Descripcion") {
            original_values <- df[[column]]
            df[[column]] <- gsub("[\\$,\\.]", "", df[[column]])
            df[[column]] <- as.numeric(df[[column]])
            cleaned_columns <- c(cleaned_columns, column)
          }
        }
      }
      return(list(df = df, cleaned_columns = cleaned_columns))
    }
    
    result <- clean_numeric_columns(df)
    df <- result$df
    processed_data(df)
    
    updateSelectInput(session, "target_column", choices = colnames(df))
    output$data_preview <- renderTable(head(df))
  })
  
  # Entrenamiento del modelo
  observeEvent(input$start_training, {
    req(processed_data(), input$target_column, input$hidden_layers, input$epochs)
    df <- processed_data()
    target <- input$target_column
    predictors <- setdiff(names(df), target)
    df_h2o <- as.h2o(df)
    hidden_layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    losses <- c()
    
    # Entrenar modelo
    for (epoch in 1:input$epochs) {
      model_h2o <- h2o.deeplearning(
        x = predictors,
        y = target,
        training_frame = df_h2o,
        hidden = hidden_layers,
        epochs = epoch,
        seed = 1234
      )
      
      # Guardar el RMSE de cada época
      history <- h2o.scoreHistory(model_h2o)
      losses <- c(losses, history$training_rmse[nrow(history)])
    }
    
    loss_history(losses)
    epoch_progress(paste("Entrenamiento completado. Última Pérdida (RMSE):", tail(losses, 1)))
    output$epoch_progress <- renderText(epoch_progress())
    output$console_output <- renderText("Entrenamiento completado exitosamente.")
  })
  
  # Gráfico de pérdida
  output$loss_plot <- renderPlotly({
    req(loss_history())
    
    plot_ly(
      x = seq_along(loss_history()), y = loss_history(), type = "scatter", mode = "lines+markers"
    ) %>% layout(title = "Pérdida durante el Entrenamiento", xaxis = list(title = "Época"), yaxis = list(title = "RMSE"))
  })
  
  # Visualización de la red neuronal
  # Visualización de la red neuronal
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
      new_links$weight <- abs(simulated_weights) * 10
      new_links$color <- scales::col_numeric(c("red", "blue"), domain = c(-1, 1))(simulated_weights)
      
      links <- rbind(links, new_links)
    }
    
    visNetwork(nodes, links, height = "600px", width = "100%") %>%
      visNodes(shape = "dot", scaling = list(min = 10, max = 30)) %>%
      visEdges(arrows = "to", width = "~weight", color = list(color = "~color")) %>%
      visLayout(randomSeed = 123) %>%
      visPhysics(enabled = FALSE) %>%
      visOptions(manipulation = FALSE)
  })  
  # Generar entradas de predicción
  output$predict_columns_ui <- renderUI({
    req(processed_data(), input$target_column)
    df <- processed_data()
    predictors <- setdiff(names(df), input$target_column)
    lapply(predictors, function(col) selectInput(paste0("input_", col), col, choices = unique(df[[col]])))
  })
  
  # Realizar predicción
  observeEvent(input$predict_button, {
    req(model_h2o(), input$target_column)
    df <- processed_data()
    predictors <- setdiff(names(df), input$target_column)
    
    pred_vals <- sapply(predictors, function(col) input[[paste0("input_", col)]])
    predict_data <- as.h2o(as.data.frame(t(pred_vals)))
    colnames(predict_data) <- predictors
    prediction <- h2o.predict(model_h2o(), predict_data)
    predicted_value <- as.vector(prediction)
    updateTextInput(session, "target_column_input", value = predicted_value)
    output$prediction_result <- renderText(paste("Predicción:", predicted_value))
  })
}

# Crear la aplicación
shinyApp(ui = ui, server = server)
