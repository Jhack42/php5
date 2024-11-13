# Instalar H2O y Shiny si no están instalados
if(!require(h2o)){
  install.packages("h2o")
}
if(!require(shiny)){
  install.packages("shiny")
}
if(!require(shinyjs)){
  install.packages("shinyjs")
}
library(h2o)
library(shiny)
library(shinyjs)

# Iniciar H2O
h2o.init()

# Cargar el archivo en un dataframe de R primero
df <- read.csv("C:/xampp_php5/htdocs/php5/py/r/entregable02/inmuebles_bogota.csv")

# Limpiar filas con valores vacíos
df <- df[complete.cases(df), ]

# Función para limpiar columnas numéricas
clean_numeric_columns <- function(df) {
  cleaned_columns <- c()
  
  for (column in colnames(df)) {
    if (is.character(df[[column]]) && any(grepl("[0-9]", df[[column]]))) {
      if (column != "Descripcion") {
        df[[column]] <- gsub("[\\$,\\.]", "", df[[column]])  # Eliminar símbolos
        df[[column]] <- as.numeric(df[[column]])             # Convertir a numérico
        cleaned_columns <- c(cleaned_columns, column)
      }
    }
  }
  return(list(df = df, cleaned_columns = cleaned_columns))
}

# Aplicar la función de limpieza
result <- clean_numeric_columns(df)
df <- result$df

# Convertir variables categóricas en factores para que H2O las maneje correctamente
df$Tipo <- as.factor(df$Tipo)
df$Barrio <- as.factor(df$Barrio)
df$UPZ <- as.factor(df$UPZ)

# Cargar el archivo limpio a H2O
data <- as.h2o(df)

# Dividir los datos en entrenamiento y prueba (80%-20%)
splits <- h2o.splitFrame(data, ratios = 0.8, seed = 1234)
train <- splits[[1]]
test <- splits[[2]]

# Definir las características (X) y la variable objetivo (Y)
y <- "Valor"
x <- setdiff(names(data), y)

# Crear y entrenar una red neuronal profunda en H2O
modelo_nn_profundo <- h2o.deeplearning(
  x = x,
  y = y,
  training_frame = train,
  validation_frame = test,
  activation = "RectifierWithDropout",
  hidden = c(128, 128, 64, 64, 32),  # Número de neuronas en cada capa oculta
  input_dropout_ratio = 0.2,
  epochs = 30,
  seed = 1234
)

# Evaluación del modelo
perf <- h2o.performance(modelo_nn_profundo, newdata = test)
print(perf)

# Gráfico de pérdida
plot(modelo_nn_profundo, timestep = "epochs", metric = "rmse")

# Interfaz de usuario (UI)
ui <- fluidPage(
  useShinyjs(),
  titlePanel("Predicción de Precio de Inmuebles en Bogotá"),
  sidebarLayout(
    sidebarPanel(
      selectInput("tipo", "Seleccione el tipo de propiedad:", choices = levels(df$Tipo)),
      numericInput("habitaciones", "Ingrese el número de habitaciones:", value = 3, min = 1, max = 10),
      numericInput("banos", "Ingrese el número de baños:", value = 2, min = 1, max = 10),
      numericInput("area", "Ingrese el área en m2:", value = 100, min = 20, max = 500),
      selectInput("barrio", "Seleccione el barrio:", choices = levels(df$Barrio)),
      actionButton("predecir", "Realizar Predicción")
    ),
    mainPanel(
      h3("Predicción de Precio"),
      textOutput("resultado")
    )
  )
)

# Lógica del servidor (Server)
server <- function(input, output) {
  observeEvent(input$predecir, {
    # Crear un nuevo data frame con la selección del usuario
    nueva_propiedad <- data.frame(
      Tipo = factor(input$tipo, levels = levels(df$Tipo)),
      Habitaciones = input$habitaciones,
      Baños = input$banos,
      Área = input$area,
      Barrio = factor(input$barrio, levels = levels(df$Barrio))
    )
    
    # Convertir a formato H2O y hacer la predicción
    nueva_propiedad_h2o <- as.h2o(nueva_propiedad)
    prediccion <- h2o.predict(modelo_nn_profundo, nueva_propiedad_h2o)
    
    # Mostrar el resultado en la interfaz
    output$resultado <- renderText({
      paste("El precio estimado es: $", format(round(prediccion[1, 1]), big.mark = ","))
    })
  })
}

# Ejecutar la aplicación Shiny
shinyApp(ui = ui, server = server)

# Detener H2O al cerrar la aplicación
onStop(function() { h2o.shutdown(prompt = FALSE) })
