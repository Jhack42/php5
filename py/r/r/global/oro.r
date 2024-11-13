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
h2o.init()

# Interfaz de usuario (UI)
ui <- fluidPage(
  useShinyjs(),
  titlePanel("Visualización en Tiempo Real de la Red Neuronal con H2O"),
  
  # Tabset para organizar las secciones de entrenamiento y predicción
  tabsetPanel(
    tabPanel("Entrenamiento del Modelo",
             fileInput("file", "Cargar archivo CSV"),
             actionButton("process_data", "Procesar y Limpiar Datos"),
             h3("Previsualización de Datos"),
             tableOutput("data_preview"),
             selectInput("target_column", "Columna Objetivo", choices = NULL),
             textInput("hidden_layers", "Capas de la Red (separadas por comas, ej: 10,10,10)", value = "10,10,10"),
             numericInput("epochs", "Número de Épocas", value = 50, min = 1),
             actionButton("start_training", "Iniciar Entrenamiento"),
             h3("Gráfico de Pérdida en Tiempo Real"),
             plotlyOutput("loss_plot"),
             h3("Progreso del Entrenamiento"),
             verbatimTextOutput("epoch_progress"),
             h3("Visualización de la Red Neuronal"),
             visNetworkOutput("network_visualization"),
             h3("Consola de Mensajes"),
             verbatimTextOutput("console_output")
    ),
    tabPanel("Predicción de Precio de Inmuebles",
             sidebarLayout(
               sidebarPanel(
                 selectInput("tipo", "Seleccione el tipo de propiedad:", choices = NULL),
                 numericInput("habitaciones", "Ingrese el número de habitaciones:", value = 3, min = 1, max = 10),
                 numericInput("banos", "Ingrese el número de baños:", value = 2, min = 1, max = 10),
                 numericInput("area", "Ingrese el área en m2:", value = 100, min = 20, max = 500),
                 selectInput("barrio", "Seleccione el barrio:", choices = NULL),
                 actionButton("predecir", "Realizar Predicción")
               ),
               mainPanel(
                 h3("Predicción de Precio"),
                 textOutput("resultado")
               )
             )
    )
  )
)

# Lógica del servidor
server <- function(input, output, session) {
  data <- reactiveVal(NULL)
  processed_data <- reactiveVal(NULL)
  modelo_nn_profundo <- reactiveVal(NULL)  # Variable reactiva para almacenar el modelo entrenado
  loss_history <- reactiveVal(NULL)
  epoch_progress <- reactiveVal("Época: 0 | Pérdida (RMSE): NA")
  
  # Carga y previsualización de los datos
  observeEvent(input$file, {
    req(input$file)
    df <- read.csv(input$file$datapath)
    data(df)
    output$data_preview <- renderTable(head(df))
    
    # Actualiza opciones de predicción basadas en los datos
    updateSelectInput(session, "tipo", choices = unique(df$Tipo))
    updateSelectInput(session, "barrio", choices = unique(df$Barrio))
  })
  
  # Procesamiento de datos
  observeEvent(input$process_data, {
    req(data())
    df <- data()
    df <- df[complete.cases(df), ]  # Elimina filas con valores NA
    
    # Función para limpiar columnas numéricas
    clean_numeric_columns <- function(df) {
      cleaned_columns <- c()
      
      for (column in colnames(df)) {
        if (is.character(df[[column]]) && any(grepl("[0-9]", df[[column]]))) {
          if (column != "Descripcion") {
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
  
  # Entrenamiento del modelo con H2O
  observeEvent(input$start_training, {
    req(processed_data())
    df <- processed_data()
    df <- df[, sapply(df, function(col) is.numeric(col) || is.factor(col))]  # Elimina columnas no numéricas
    
    # Convertir datos a H2O
    df_h2o <- as.h2o(df)
    target <- input$target_column
    predictors <- setdiff(names(df), target)
    hidden_layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    
    # Inicializar la lista de pérdidas y entrenar el modelo por épocas
    losses <- c()
    for (epoch in 1:input$epochs) {
      model_h2o <- h2o.deeplearning(
        x = predictors,
        y = target,
        training_frame = df_h2o,
        hidden = hidden_layers,
        epochs = epoch,
        seed = 1234
      )
      
      # Extrae la pérdida de la última época y almacena en la lista de pérdidas
      history <- h2o.scoreHistory(model_h2o)
      losses <- c(losses, history$training_rmse[nrow(history)])
    }
    
    # Guardar el modelo entrenado para predicciones futuras
    modelo_nn_profundo(model_h2o)
    
    # Guardar la historia de pérdidas en la variable reactiva
    loss_history(losses)
    
    # Actualiza el progreso de la época y muestra en la interfaz
    for (i in seq_along(losses)) {
      epoch_progress(paste("Época:", i, "| Pérdida (RMSE):", losses[i]))
      output$epoch_progress <- renderText(epoch_progress())
      Sys.sleep(0.1)  # Simula un retraso
    }
    
    # Mensaje de éxito en la consola
    output$console_output <- renderText("Entrenamiento completado exitosamente.")
  })
  
  # Gráfico de pérdida
  output$loss_plot <- renderPlotly({
    req(loss_history())
    
    # Crear un data frame para el historial de pérdidas
    loss_data <- data.frame(
      epoch = 1:length(loss_history()),
      loss = loss_history()
    )
    
    # Generar el gráfico de pérdidas
    plot_ly(loss_data, x = ~epoch, y = ~loss, type = "scatter", mode = "lines+markers", 
            marker = list(color = "dodgerblue")) %>%
      layout(
        title = "Pérdida durante el Entrenamiento",
        xaxis = list(title = "Épocas"),
        yaxis = list(title = "Pérdida (RMSE)")
      )
  })
  
  # Predicción de precios de inmuebles
  observeEvent(input$predecir, {
    req(modelo_nn_profundo())  # Asegura que el modelo esté entrenado y disponible
    
    # Crear un nuevo data frame con las características seleccionadas por el usuario
    nueva_propiedad <- data.frame(
      Tipo = factor(input$tipo, levels = levels(data()$Tipo)),
      Habitaciones = input$habitaciones,
      Baños = input$banos,
      Área = input$area,
      Barrio = factor(input$barrio, levels = levels(data()$Barrio))
    )
    
    # Convertir el data frame a formato H2O para la predicción
    nueva_propiedad_h2o <- as.h2o(nueva_propiedad)
    prediccion <- h2o.predict(modelo_nn_profundo(), nueva_propiedad_h2o)
    
    # Mostrar el resultado de la predicción
    output$resultado <- renderText({
      paste("El precio estimado es: $", format(round(as.numeric(prediccion[1, 1])), big.mark = ","))
    })
  })
  
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
}

# Crear la aplicación
shinyApp(ui = ui, server = server)
