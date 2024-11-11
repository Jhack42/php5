# Instala y carga los paquetes necesarios
if (!requireNamespace("shiny", quietly = TRUE)) install.packages("shiny")
if (!requireNamespace("shinyjs", quietly = TRUE)) install.packages("shinyjs")
if (!requireNamespace("plotly", quietly = TRUE)) install.packages("plotly")
if (!requireNamespace("networkD3", quietly = TRUE)) install.packages("networkD3")
if (!requireNamespace("h2o", quietly = TRUE)) install.packages("h2o")

# Cargar las bibliotecas
library(shiny)
library(shinyjs)
library(plotly)
library(networkD3)
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
           textInput("hidden_layers", "Capas ocultas (separadas por comas, e.g., 10,10):", value = "10,10"),
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
           forceNetworkOutput("network_plot")
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
  
  # Visualización de la estructura del modelo
  output$network_plot <- renderForceNetwork({
    if (is.null(model())) return(NULL)
    
    layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
    layer_sizes <- c(2, layers, 1)  # Añadimos 2 para la entrada y 1 para la salida
    
    # Crear nodos para cada capa
    node_names <- unlist(lapply(seq_along(layer_sizes), function(i) {
      paste("Capa", i, "Neuron", seq_len(layer_sizes[i]))
    }))
    nodes <- data.frame(name = node_names, Group = rep(seq_along(layer_sizes), layer_sizes), stringsAsFactors = FALSE)
    
    # Crear enlaces entre nodos de capas consecutivas para simular la estructura de la red neuronal
    links <- data.frame()
    for (i in 1:(length(layer_sizes) - 1)) {
      from_nodes <- seq(sum(layer_sizes[1:i]) - layer_sizes[i] + 1, sum(layer_sizes[1:i]))
      to_nodes <- seq(sum(layer_sizes[1:(i + 1)]) - layer_sizes[i + 1] + 1, sum(layer_sizes[1:(i + 1)]))
      
      new_links <- expand.grid(source = from_nodes - 1, target = to_nodes - 1)
      new_links$value <- 1  # Valor constante para el grosor de las conexiones
      links <- rbind(links, new_links)
    }
    
    # Visualización de la red neuronal con forceNetwork
    forceNetwork(
      Links = links, Nodes = nodes,
      Source = "source", Target = "target",
      Value = "value", NodeID = "name",
      Group = "Group",
      opacity = 0.8,
      linkDistance = 50,  # Ajuste para espaciar mejor las conexiones
      charge = -400  # Ajuste para expandir la visualización
    )
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
