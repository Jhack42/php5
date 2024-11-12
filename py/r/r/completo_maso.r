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
  
  # Cargar y mostrar archivo de datos
  fileInput("file", "Cargar archivo CSV"),
  actionButton("process_data", "Procesar y Limpiar Datos"),
  
  # Vista previa de los datos
  h3("Previsualización de Datos"),
  tableOutput("data_preview"),
  
  # Selección de columna objetivo y configuración del modelo
  selectInput("target_column", "Columna Objetivo", choices = NULL),
  textInput("hidden_layers", "Capas de la Red (separadas por comas, ej: 10,10,10)", value = "10,10,10"),
  numericInput("epochs", "Número de Épocas", value = 50, min = 1),
  actionButton("start_training", "Iniciar Entrenamiento"),
  
  # Gráfico de pérdida
  h3("Gráfico de Pérdida en Tiempo Real"),
  plotlyOutput("loss_plot"),
  
  # Información en tiempo real del entrenamiento
  h3("Progreso del Entrenamiento"),
  verbatimTextOutput("epoch_progress"),
  
  # Visualización de la red neuronal
  h3("Visualización de la Red Neuronal"),
  visNetworkOutput("network_visualization"),
  
  # Consola de mensajes
  h3("Consola de Mensajes"),
  verbatimTextOutput("console_output")
)

# Lógica del servidor
server <- function(input, output, session) {
  data <- reactiveVal(NULL)
  processed_data <- reactiveVal(NULL)
  loss_history <- reactiveVal(NULL)
  epoch_progress <- reactiveVal("Época: 0 | Pérdida (RMSE): NA")  # Variable reactiva para mostrar el progreso
  
  # Carga y previsualización de los datos
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
    
    # Convertir columnas numéricas con caracteres especiales a numéricas
    df <- df %>%
      mutate(across(where(is.character), ~as.numeric(gsub("[^0-9.]", "", .))))
    
    processed_data(df)
    updateSelectInput(session, "target_column", choices = colnames(df))
    output$data_preview <- renderTable(head(df))
  })
  
  # Entrenamiento del modelo con H2O
  observeEvent(input$start_training, {
    req(processed_data())
    df <- processed_data()
    
    # Definir la columna objetivo y las variables predictoras
    target <- input$target_column
    predictors <- setdiff(names(df), target)
    
    # Convertir a un marco de datos de H2O
    df_h2o <- as.h2o(df)
    
    # Configurar las capas ocultas de la red
    hidden_layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    
    # Entrenar el modelo de red neuronal con monitoreo de progreso
    model_h2o <- h2o.deeplearning(
      x = predictors,
      y = target,
      training_frame = df_h2o,
      hidden = hidden_layers,
      epochs = input$epochs,
      seed = 1234,
      score_interval = 1,    # Intervalo para capturar la pérdida en cada época
      score_duty_cycle = 1   # Evaluar en cada época
    )
    
    # Captura de la historia de pérdidas
    history <- h2o.scoreHistory(model_h2o)
    loss_values <- history$training_rmse
    
    # Actualización de la historia de pérdida
    loss_history(loss_values)
    
    # Mostrar la actualización de la época actual y pérdida en la consola
    for (i in seq_along(loss_values)) {
      epoch_progress(paste("Época:", i, "| Pérdida (RMSE):", loss_values[i]))
      output$epoch_progress <- renderText(epoch_progress())
      Sys.sleep(0.1)  # Simula el retraso en tiempo real
    }
    
    # Mensaje de éxito en la consola
    output$console_output <- renderText("Entrenamiento completado exitosamente.")
  })
  
  # Gráfico de pérdida en tiempo real
  output$loss_plot <- renderPlotly({
    req(loss_history())
    
    # Crear el gráfico de pérdida usando Plotly
    loss_data <- data.frame(
      epoch = 1:length(loss_history()),
      loss = loss_history()
    )
    
    plot_ly(loss_data, x = ~epoch, y = ~loss, type = "scatter", mode = "lines+markers") %>%
      layout(
        title = "Pérdida durante el Entrenamiento",
        xaxis = list(title = "Épocas"),
        yaxis = list(title = "Pérdida (RMSE)")
      )
  })
  
  # Visualización de la red neuronal
  output$network_visualization <- renderVisNetwork({
    req(input$hidden_layers)
    
    layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    layer_sizes <- c(ncol(processed_data()) - 1, layers, 1)  # Entrada, capas ocultas, salida
    
    # Crear nodos para cada capa con IDs únicos
    nodes <- data.frame(id = integer(), label = character(), x = numeric(), y = numeric(), group = integer())
    y_offset <- 100
    node_id <- 1  # ID único para cada neurona
    
    for (i in seq_along(layer_sizes)) {
      x_pos <- i * 200  # Espaciado horizontal entre capas
      y_positions <- seq(-layer_sizes[i] / 2 * y_offset, layer_sizes[i] / 2 * y_offset, length.out = layer_sizes[i])
      new_nodes <- data.frame(
        id = node_id:(node_id + layer_sizes[i] - 1),
        label = paste("Capa", i, "Neuron", 1:layer_sizes[i]),
        x = rep(x_pos, layer_sizes[i]),
        y = y_positions,
        group = i
      )
      nodes <- rbind(nodes, new_nodes)
      node_id <- node_id + layer_sizes[i]  # Actualizamos el id para la próxima capa
    }
    
    # Crear enlaces entre nodos de capas consecutivas
    links <- data.frame()
    for (i in 1:(length(layer_sizes) - 1)) {
      from_nodes <- nodes$id[nodes$group == i]
      to_nodes <- nodes$id[nodes$group == i + 1]
      
      # Simulación de pesos aleatorios para las conexiones
      simulated_weights <- runif(length(from_nodes) * length(to_nodes), -1, 1)
      
      new_links <- expand.grid(from = from_nodes, to = to_nodes)
      
      # Normalización y ajuste de color y grosor
      new_links$weight <- abs(simulated_weights) * 10  # Ajuste del grosor
      new_links$color <- scales::col_numeric(c("red", "blue"), domain = c(-1, 1))(simulated_weights)  # Gradiente de color
      
      links <- rbind(links, new_links)
    }
    
    # Visualización de la red neuronal con visNetwork
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
