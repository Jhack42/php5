import re  # Para manejar expresiones regulares
import requests
import json
import urllib.parse
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
import time

# Configurar el cliente para conectarse al servidor local de OpenAI
def ask_in_rhyme(text):
    url = "http://localhost:1234/v1/chat/completions"
    data = {
        "model": "llama-3.2-2b-instruct",
        "messages": [
            {"role": "system", "content": "Always answer in rhymes."},
            {"role": "user", "content": text}
        ],
        "temperature": 0.7,
        "max_tokens": 500,
        "stream": True
    }
    headers = {"Content-Type": "application/json"}

    response = requests.post(url, headers=headers, data=json.dumps(data), stream=True)

    if response.status_code == 200:
        full_response = ""
        for line in response.iter_lines():
            if line:
                line_content = line.decode("utf-8").strip()

                if line_content == "data: [DONE]":
                    break

                if line_content.startswith("data:"):
                    json_data = json.loads(line_content[5:].strip())

                    if "choices" in json_data and json_data["choices"]:
                        content = json_data["choices"][0]["delta"].get("content", "")
                        full_response += content

        return full_response
    else:
        return f"Error {response.status_code}: {response.text}"

# Inicializar el controlador de Chrome con Selenium usando un perfil personalizado o limpio
def init_driver():
    chrome_options = Options()

    # Especifica el directorio del perfil de Chrome que contiene tus sesiones activas
    chrome_options.add_argument(r"user-data-dir=C:\Users\soporte\AppData\Local\Google\Chrome\User Data")
    chrome_options.add_argument(r"profile-directory=Default")  # Cambia si usas otro perfil

    # Opciones para evitar errores de WebGL, audio, y GPU
    chrome_options.add_argument("--disable-webgl")
    chrome_options.add_argument("--mute-audio")
    chrome_options.add_argument("--disable-gpu")

    # Usar otro puerto para depuración remota
    chrome_options.add_argument("--remote-debugging-port=9223")

    # Ruta a ChromeDriver
    service = Service('C:/chromedriver-win64/chromedriver.exe')
    driver = webdriver.Chrome(service=service, options=chrome_options)

    return driver

# Función para limpiar la búsqueda eliminando frases comunes y dejando solo lo importante
def clean_search_query(user_input):
    phrases_to_remove = [
        "quiero buscar", "busca", "quiero ver", "en youtube", "videos de", "videos en youtube de", "youtube"
    ]
    search_query = user_input.lower()
    for phrase in phrases_to_remove:
        search_query = search_query.replace(phrase, "")
    return search_query.strip()

# Función para buscar en YouTube y reproducir un video por índice o nombre
def search_and_play_youtube(driver, search_query, video_index=None, video_name=None):
    print(f"Buscando '{search_query}' en YouTube...")
    encoded_search_query = urllib.parse.quote(search_query)
    search_url = f"https://www.youtube.com/results?search_query={encoded_search_query}"
    driver.get(search_url)
    time.sleep(3)

    videos = driver.find_elements(By.ID, 'video-title')

    # Reproducción por índice (ej. "el cuarto video")
    if video_index and len(videos) >= video_index:
        print(f"Reproduciendo el video {video_index}...")
        videos[video_index - 1].click()
    # Reproducción por nombre de video (ej. "reproduce el video que dice...")
    elif video_name:
        for video in videos:
            if video_name.lower() in video.get_attribute('title').lower():
                print(f"Reproduciendo el video: {video.get_attribute('title')}...")
                video.click()
                break
        else:
            print(f"No se encontró un video con el nombre '{video_name}'.")

# Función para manejar intenciones y abrir YouTube o realizar búsquedas
def handle_intentions(user_input, driver):
    # Detectar si el usuario quiere abrir YouTube
    if re.search(r"(abre|pon|entrar|abre la|quiero ver|entra a).*youtube", user_input.lower()):
        print("Abriendo YouTube...")
        driver.get("https://www.youtube.com")
        return True

    # Detectar si el usuario quiere buscar algo en YouTube
    if re.search(r"(quiero buscar|busca|vusca|quiero ver|muestra|muestrame).*en youtube", user_input.lower()):
        search_query = clean_search_query(user_input)
        if search_query:
            search_and_play_youtube(driver, search_query)
            return True

    # Reproducción directa de video por número de índice (ej. "abre el cuarto video")
    match = re.search(r"reproduce el (\d+)[a-z]* video", user_input.lower())
    if match:
        video_index = int(match.group(1))  # Captura el número del video
        search_query = clean_search_query(user_input)
        search_and_play_youtube(driver, search_query, video_index=video_index)
        return True

    # Reproducción directa de video por nombre (ej. "reproduce el video que dice...")
    match = re.search(r"reproduce el video que dice (.*)", user_input.lower()) or re.search(r"reproduce (.*)", user_input.lower())
    if match:
        video_name = match.group(1).strip()
        search_query = clean_search_query(user_input)
        search_and_play_youtube(driver, search_query, video_name=video_name)
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

        # Manejar intenciones (abrir YouTube o buscar en YouTube)
        if handle_intentions(user_input, driver):
            continue  # Si se abrió YouTube o se hizo una búsqueda, no necesitamos procesar la IA

        # Si no es una intención de YouTube, la IA responde
        response = ask_in_rhyme(user_input)
        print(f"IA:\n{response}\n")

# Iniciar el chat interactivo
if __name__ == "__main__":
    interactive_chat()
