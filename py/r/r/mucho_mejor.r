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
  loss_history <- reactiveVal(c())  # Vector reactivo para almacenar la pérdida en cada época
  
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
    
    # Vector para almacenar la pérdida en cada época
    losses <- c()
    
    # Entrena el modelo con H2O usando una red neuronal y guarda la pérdida en cada época
    for (epoch in 1:input$epochs) {
      model_h2o <- h2o.deeplearning(
        x = c("a", "b"),
        y = "c",
        training_frame = data_h2o,
        hidden = hidden_layers,
        epochs = epoch,  # Incrementa la época en cada iteración
        seed = 1234
      )
      
      # Extraer la pérdida para esta época y agregarla al vector de pérdidas
      history <- h2o.scoreHistory(model_h2o)
      losses <- c(losses, history$training_rmse[nrow(history)])
    }
    
    # Guardar el modelo en la variable reactiva y el historial de pérdidas en `loss_history`
    model(model_h2o)
    loss_history(losses)  # Guardar el historial de pérdidas en la variable reactiva
  })
  
  # Gráfico de pérdida
  output$lossChart <- renderPlotly({
    if (is.null(loss_history())) return(NULL)
    
    # Crear un data frame para manejar los datos de pérdida
    loss_data <- data.frame(
      epoch = 1:length(loss_history()),
      loss = loss_history()
    )
    
    # Crear el gráfico base usando ggplot2
    loss_plot <- ggplot(loss_data, aes(x = epoch, y = loss)) +
      geom_line(color = "dodgerblue", size = 1) +
      geom_point(color = "dodgerblue", size = 2) +
      labs(title = "Pérdida durante el Entrenamiento",
           x = "Épocas",
           y = "Pérdida (RMSE)") +
      theme_minimal() +
      theme(
        plot.title = element_text(hjust = 0.5, size = 14, face = "bold"),
        axis.title = element_text(size = 12),
        axis.text = element_text(size = 10)
      )
    
    # Convertir el gráfico de ggplot2 a un objeto interactivo de plotly
    ggplotly(loss_plot) %>%
      layout(
        title = list(text = "<b>Pérdida durante el Entrenamiento</b>"),
        xaxis = list(
          title = "Épocas",
          tickmode = "array",
          tickvals = loss_data$epoch,  # Asegura que cada época tenga una etiqueta
          ticktext = loss_data$epoch   # Muestra cada número de época
        ),
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
