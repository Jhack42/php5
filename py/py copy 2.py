import subprocess
from openai import OpenAI

# Configurar el cliente para conectarse al servidor local
client = OpenAI(base_url="http://localhost:1234/v1", api_key="lm-studio")

# Función para hacer que la IA responda en rima
def ask_in_rhyme(text):
    completion = client.chat.completions.create(
        model="bartowski/Llama-3.2-1B-Instruct-GGUF",
        messages=[
            {"role": "system", "content": "Always answer in rhymes."},
            {"role": "user", "content": text}
        ],
        temperature=0.7,
    )
    return completion.choices[0].message.content

# Función para manejar intenciones y abrir YouTube si se detecta
def handle_intentions(user_input):
    # Lista de posibles formas en las que el usuario podría pedir abrir YouTube
    youtube_intentions = [
        "quiero abrir youtube", "abre youtube", "abre el youtube",
        "abre mi youtube", "abre la página de youtube", "pon youtube"
    ]
    
    # Verificar si la entrada del usuario contiene una intención de abrir YouTube
    for intention in youtube_intentions:
        if intention in user_input.lower():
            print("Abriendo YouTube...")
            # Abrir YouTube en el navegador predeterminado
            subprocess.run(["start", "https://www.youtube.com"], shell=True)
            return True
    
    # Si no se detecta ninguna intención de abrir YouTube, devuelve False
    return False

# Bucle interactivo para que el usuario escriba y la IA responda
def interactive_chat():
    print("Chat interactivo con la IA (presiona Ctrl+C para salir)\n")
    while True:
        # Leer el input del usuario
        user_input = input("Tú: ")

        # Si el usuario no ingresa nada, terminamos la conversación
        if not user_input:
            print("Terminando la conversación...")
            break
        
        # Manejar intenciones (abrir YouTube)
        if handle_intentions(user_input):
            continue  # Si se abrió YouTube, no necesitamos procesar la IA

        # Obtener la respuesta de la IA en rima
        response = ask_in_rhyme(user_input)
        
        # Mostrar la respuesta de la IA en un formato legible
        print(f"IA:\n{response}\n")

# Iniciar el chat interactivo
if __name__ == "__main__":
    interactive_chat()
