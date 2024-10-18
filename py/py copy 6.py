import subprocess
from openai import OpenAI
import urllib.parse
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
import time

# Configurar el cliente para conectarse al servidor local
client = OpenAI(base_url="http://localhost:1234/v1", api_key="lm-studio")

# Inicializar el controlador de Chrome con Selenium usando un perfil de usuario
def init_driver():
    # Configurar las opciones de Chrome
    chrome_options = Options()
    
    # Usar el perfil de usuario existente
    chrome_options.add_argument(r"user-data-dir=C:\Users\Jhack Alberth\AppData\Local\Google\Chrome\User Data")
    chrome_options.add_argument("profile-directory=Default")  # Cambia a tu perfil específico si es necesario

    # Deshabilitar verificaciones que pueden causar errores
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-gpu")  # Necesario en Windows
    chrome_options.add_argument("--remote-debugging-port=9222")  # Habilitar puerto de depuración

    # Asegúrate de que la ruta a ChromeDriver sea la correcta
    service = Service('C:/chromedriver-win64/chromedriver.exe')
    driver = webdriver.Chrome(service=service, options=chrome_options)
    
    return driver

# El resto del código sigue igual...

# Función para manejar intenciones y abrir YouTube o realizar búsquedas
def handle_intentions(user_input, driver):
    youtube_open_intentions = [
        "quiero abrir youtube", "abre youtube", "abre el youtube",
        "abre mi youtube", "abre la página de youtube", "pon youtube"
    ]
    youtube_search_intentions = [
        "quiero buscar", "busca", "quiero ver", "ver en youtube"
    ]

    for intention in youtube_open_intentions:
        if intention in user_input.lower():
            print("Abriendo YouTube...")
            driver.get("https://www.youtube.com")
            return True
    
    for intention in youtube_search_intentions:
        if any(phrase in user_input.lower() for phrase in youtube_search_intentions):
            search_query = clean_search_query(user_input)
            if search_query:
                search_and_play_youtube(driver, search_query)
                return True

    if "reproduce el cuarto video" in user_input.lower():
        search_and_play_youtube(driver, "", video_index=4)
        return True

    return False

# Bucle interactivo para que el usuario escriba y la IA responda
def interactive_chat():
    driver = init_driver()  # Iniciar el controlador de Selenium
    print("Chat interactivo con la IA (presiona Ctrl+C para salir)\n")
    while True:
        user_input = input("Tú: ")
        if not user_input:
            print("Terminando la conversación...")
            break
        
        if handle_intentions(user_input, driver):
            continue  # Si se abrió YouTube o se hizo una búsqueda, no necesitamos procesar la IA

        response = ask_in_rhyme(user_input)
        print(f"IA:\n{response}\n")

# Iniciar el chat interactivo
if __name__ == "__main__":
    interactive_chat()
