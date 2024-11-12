# Cargar librerías
library(shiny)
library(shinyjs)

# Interfaz de usuario (UI)
ui <- fluidPage(
  useShinyjs(),
  titlePanel("Ejemplo de Dropdown"),
  
  # Barra de selección (dropdown)
  selectInput("opcion", 
              "Selecciona una opción:", 
              choices = c("Opción 1", "Opción 2", "Opción 3"),
              selected = "Opción 1"),
  
  # Espacio para mostrar la opción seleccionada
  textOutput("textoSeleccionado")
)

# Lógica del servidor (Server)
server <- function(input, output) {
  output$textoSeleccionado <- renderText({
    paste("Has seleccionado:", input$opcion)
  })
}

# Ejecutar la aplicación
shinyApp(ui = ui, server = server)
