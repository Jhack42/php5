from openai import OpenAI

# Configurar el cliente para conectarse al servidor local
client = OpenAI(base_url="http://localhost:1234/v1", api_key="lm-studio")

# Función para hacer que la IA responda en rima
def ask_in_rhyme(text):
    # Hacer una solicitud a la IA para responder en rima
    completion = client.chat.completions.create(
        model="bartowski/Llama-3.2-1B-Instruct-GGUF",
        messages=[
            {"role": "system", "content": "Always answer in rhymes."},
            {"role": "user", "content": text}
        ],
        temperature=0.7,
    )
    
    # Acceder correctamente al contenido de la respuesta
    return completion.choices[0].message.content

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
        
        # Obtener la respuesta de la IA
        response = ask_in_rhyme(user_input)
        
        # Mostrar la respuesta de la IA en un formato legible
        print(f"IA:\n{response}\n")

# Iniciar el chat interactivo
if __name__ == "__main__":
    interactive_chat()
