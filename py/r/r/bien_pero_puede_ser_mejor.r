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

# UI: Interfaz de Usuario
ui <- fluidPage(
  useShinyjs(),
  titlePanel("Visualización de Red Neuronal con H2O"),
  
  # Configuración del modelo de H2O
  fluidRow(
    column(6,
           h3("Configuración del Modelo"),
           textInput("hidden_layers", "Capas ocultas (separadas por comas, e.g., 8,8,8):", value = "8,8,8"),
           numericInput("epochs", "Número de Épocas:", value = 50, min = 1),
           actionButton("train", "Iniciar Entrenamiento")
    )
  ),
  
  # Gráfico de pérdida
  fluidRow(
    column(12,
           h3("Historial de Pérdida"),
           plotlyOutput("lossChart")
    )
  ),
  
  # Visualización de la red neuronal
  fluidRow(
    column(12,
           h3("Estructura de la Red Neuronal"),
           visNetworkOutput("network_plot")
    )
  ),
  
  # Sección de predicción
  fluidRow(
    column(12,
           h3("Predicción"),
           numericInput("a_value", "Valor de A:", value = 5),
           numericInput("b_value", "Valor de B:", value = 5),
           actionButton("predict", "Realizar Predicción"),
           verbatimTextOutput("prediction")
    )
  )
)

# Server: Lógica del Servidor
server <- function(input, output, session) {
  
  # Variables reactivas para el modelo y el historial de pérdidas
  model <- reactiveVal(NULL)
  loss_history <- reactiveVal(c())
  
  observeEvent(input$train, {
    # Prepara los datos de entrenamiento
    a_values <- runif(500, 1, 10)
    b_values <- runif(500, 1, 10)
    c_values <- sqrt(a_values^2 + b_values^2)
    data <- data.frame(a = a_values, b = b_values, c = c_values)
    
    # Convertir a un frame de H2O
    data_h2o <- as.h2o(data)
    
    # Configuración de las capas ocultas
    hidden_layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    
    # Entrena el modelo con H2O usando una red neuronal
    model_h2o <- h2o.deeplearning(
      x = c("a", "b"),
      y = "c",
      training_frame = data_h2o,
      hidden = hidden_layers,
      epochs = input$epochs,
      seed = 1234
    )
    
    # Guardar el modelo en la variable reactiva
    model(model_h2o)
    
    # Guardar el historial de pérdida
    history <- h2o.scoreHistory(model_h2o)
    loss_history(history$training_rmse)
  })
  
  # Gráfico de pérdida
  output$lossChart <- renderPlotly({
    if (is.null(loss_history())) return(NULL)
    
    plot_ly(
      x = seq_along(loss_history()),
      y = loss_history(),
      type = "scatter",
      mode = "lines+markers",
      name = "Pérdida"
    ) %>% layout(
      title = "Pérdida durante el Entrenamiento",
      xaxis = list(title = "Épocas"),
      yaxis = list(title = "Pérdida"),
      showlegend = FALSE
    )
  })
  
  # Visualización de la estructura del modelo con pesos simulados
  output$network_plot <- renderVisNetwork({
    if (is.null(model())) return(NULL)
    
    layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    layer_sizes <- c(2, layers, 1)  # Añadimos 2 para la entrada y 1 para la salida
    
    # Crear nodos para cada capa con IDs únicos
    nodes <- data.frame(id = integer(), label = character(), x = numeric(), y = numeric(), group = integer())
    y_offset <- 100
    node_id <- 1  # Empezamos con un id único
    
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
    
    # Crear enlaces entre nodos de capas consecutivas con pesos simulados
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
  
  # Predicción
  observeEvent(input$predict, {
    if (is.null(model())) {
      output$prediction <- renderText("Entrena el modelo primero.")
      return(NULL)
    }
    
    # Crear un nuevo frame de datos para la predicción
    new_data <- as.h2o(data.frame(a = input$a_value, b = input$b_value))
    prediction <- as.numeric(h2o.predict(model(), new_data)[1,1])
    
    # Mostrar el resultado de la predicción
    output$prediction <- renderText({
      paste("Predicción para los valores A =", input$a_value, "y B =", input$b_value, "es:", round(prediction, 3))
    })
  })
}

# Crear la aplicación
shinyApp(ui = ui, server = server)
