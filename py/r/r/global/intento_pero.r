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
  verbatimTextOutput("console_output"),
  
  # Sección de predicción
  h3("Predicción con el Modelo Entrenado"),
  uiOutput("prediction_input_ui"),
  actionButton("predict_button", "Realizar Predicción"),
  verbatimTextOutput("prediction_output")
)

# Lógica del servidor
server <- function(input, output, session) {
  data <- reactiveVal(NULL)
  processed_data <- reactiveVal(NULL)
  model <- reactiveVal(NULL)
  
  observeEvent(input$file, {
    req(input$file)
    df <- read.csv(input$file$datapath)
    data(df)
    output$data_preview <- renderTable(head(df))
  })
  
  observeEvent(input$process_data, {
    req(data())
    df <- data()
    df <- df[complete.cases(df), ]
    
    # Intentar convertir columnas no numéricas si es posible
    for (col in names(df)) {
      if (!is.numeric(df[[col]]) && all(grepl("^[0-9]+(\\.[0-9]+)?$", df[[col]]))) {
        df[[col]] <- as.numeric(df[[col]])
      }
    }
    
    processed_data(df)
    updateSelectInput(session, "target_column", choices = colnames(df))
    output$data_preview <- renderTable(head(df))
  })
  
  observeEvent(input$start_training, {
    tryCatch({
      req(processed_data())
      df <- processed_data()
      
      target <- input$target_column
      predictors <- setdiff(names(df), target)
      
      if (is.character(df[[target]])) {
        stop("La columna objetivo no puede ser de tipo String. Conviértela a numérica o categórica.")
      }
      
      df_h2o <- as.h2o(df)
      hidden_layers <- as.numeric(unlist(strsplit(input$hidden_layers, ",")))
      
      model_h2o <- h2o.deeplearning(
        x = predictors,
        y = target,
        training_frame = df_h2o,
        hidden = hidden_layers,
        epochs = input$epochs,
        seed = 1234
      )
      model(model_h2o)
      
      output$console_output <- renderText("Entrenamiento completado exitosamente.")
    }, error = function(e) {
      output$console_output <- renderText(paste("Error en el entrenamiento:", e$message))
    })
  })
  
  output$prediction_input_ui <- renderUI({
    req(processed_data())
    df <- processed_data()
    cols <- names(df)
    cols <- cols[cols != input$target_column]
    
    input_list <- lapply(cols, function(col) {
      if (is.factor(df[[col]])) {
        selectInput(inputId = paste0("pred_", col), label = col, choices = levels(df[[col]]))
      } else if (is.numeric(df[[col]])) {
        numericInput(inputId = paste0("pred_", col), label = col, value = median(df[[col]], na.rm = TRUE))
      } else {
        textInput(inputId = paste0("pred_", col), label = col, value = "Introduce un valor")
      }
    })
    
    do.call(tagList, input_list)
  })
  
  observeEvent(input$predict_button, {
    tryCatch({
      req(model())
      df <- processed_data()
      cols <- names(df)[names(df) != input$target_column]
      
      new_data <- as.data.frame(lapply(cols, function(col) {
        input[[paste0("pred_", col)]]
      }))
      
      colnames(new_data) <- cols
      new_data_h2o <- as.h2o(new_data)
      prediction <- h2o.predict(model(), new_data_h2o)
      
      output$prediction_output <- renderPrint({
        prediction
      })
    }, error = function(e) {
      output$prediction_output <- renderText(paste("Error en la predicción:", e$message))
    })
  })
}

# Crear la aplicación
shinyApp(ui = ui, server = server)
