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
  
  # Botón para estandarizar los datos
  actionButton("standardize_data", "Estandarizar Datos"),
  
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
  # Procesamiento de datos optimizado
  # Procesamiento de datos optimizado
  # Procesamiento de datos optimizado
# Procesamiento de datos optimizado con registros en consola
  # Procesamiento de datos optimizado con registros en consola
  observeEvent(input$process_data, {
    req(data())
    df <- data()
    
    # Eliminar filas con valores vacíos en cualquier columna
    df <- df[complete.cases(df), ]
    print("Filas con datos vacíos eliminadas.")
    
    # Función para limpiar las columnas numéricas con caracteres especiales
    clean_numeric_columns <- function(df) {
      cleaned_columns <- c()
      
      for (column in colnames(df)) {
        if (is.character(df[[column]]) && any(grepl("[0-9]", df[[column]]))) {
          # Verificar que no sea la columna Descripcion
          if (column != "Descripcion") {
            # Verificar la columna antes de limpiarla
            print(paste("Procesando columna:", column))
            
            # Limpiar el símbolo de dólar y los puntos para miles y convertir a numérico
            original_values <- df[[column]]  # Guardar los valores originales antes de limpiarlos
            
            # Eliminar $ y . (caracteres especiales)
            df[[column]] <- gsub("[\\$,\\.]", "", df[[column]])
            
            # Verificar si el valor cambiado es realmente diferente
            if (all(df[[column]] == original_values)) {
              print(paste("Columna", column, "no requirió limpieza (sin caracteres especiales)."))
            } else {
              print(paste("Columna", column, "limpiada. Nuevos valores:", paste(head(df[[column]]), collapse = ", ")))
            }
            
            # Convertir a numérico
            df[[column]] <- as.numeric(df[[column]])  # Convertir a numérico
            
            # Verificar si se convirtió correctamente
            if (any(is.na(df[[column]]))) {
              print(paste("Columna", column, "contiene NA después de la conversión a numérico."))
            }
            
            # Agregar la columna a la lista de columnas procesadas
            cleaned_columns <- c(cleaned_columns, column)
          } else {
            print(paste("Columna", column, "no se procesó (es una columna de texto)."))
          }
        } else {
          # Si no se limpió, imprimir que no fue procesada
          print(paste("Columna", column, "no contiene números o no necesita limpieza."))
        }
      }
      return(list(df = df, cleaned_columns = cleaned_columns))
    }
    
    #--------------------------------------------------
    # Lógica de estandarización de los datos
    observeEvent(input$standardize_data, {
      req(processed_data())  # Asegurarse de que los datos procesados existan
      
      df <- processed_data()  # Obtener los datos procesados previamente
      
      # Seleccionar solo las columnas numéricas para estandarizar
      numeric_columns <- sapply(df, is.numeric)
      df_numeric <- df[, numeric_columns]  # Filtrar las columnas numéricas
      
      # Estandarizar las columnas numéricas
      df_numeric_scaled <- scale(df_numeric)
      
      # Reemplazar las columnas numéricas originales con las estandarizadas
      df[, numeric_columns] <- df_numeric_scaled
      
      # Actualizar los datos procesados con los datos estandarizados
      processed_data(df)
      
      # Mostrar la vista previa de los datos estandarizados
      output$data_preview <- renderTable(head(df))
      
      print("Datos estandarizados.")
    })
    
    
    
    
    
    # Limpiar las columnas numéricas
    result <- clean_numeric_columns(df)
    df <- result$df
    cleaned_columns <- result$cleaned_columns
    
    # Actualizar la variable processed_data
    processed_data(df)
    
    # Actualizar el selector de columnas
    updateSelectInput(session, "target_column", choices = colnames(df))
    
    # Mostrar una vista previa de los datos procesados
    output$data_preview <- renderTable(head(df))
    
    # Mostrar las columnas limpiadas, opcional
    print(paste("Columnas limpiadas:", paste(cleaned_columns, collapse = ", ")))
    
    # Formatear los números para evitar la notación científica
    options(scipen = 999)  # Evitar la notación científica
    
    # Ejemplo para la columna 'Valor' (si existe)
    if("Valor" %in% colnames(df)) {
      df$Valor <- as.numeric(gsub("[\\$,\\.]", "", df$Valor))  # Limpiar y convertir a numérico
    }
    
    # Actualizar los datos procesados en la vista previa
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
