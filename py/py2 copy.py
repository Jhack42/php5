import requests
import json

# Definir la URL del servidor
url = "http://127.0.0.1:1234/v1/chat/completions"

# Datos que se enviarán en la solicitud POST
data = {
    "model": "llama-3.2-2b-instruct",  # Cambia a la versión correcta del modelo si es necesario
    "messages": [
        {"role": "system", "content": "Always answer in rhymes."},
        {"role": "user", "content": "hola soy jhack"}
    ],
    "temperature": 0.7,
    "max_tokens": 500,  # Asegúrate de usar el número adecuado de tokens
    "stream": True  # Indica que deseas la respuesta en fragmentos
}

# Encabezados de la solicitud
headers = {
    "Content-Type": "application/json"
}

# Hacer la solicitud POST
response = requests.post(url, headers=headers, data=json.dumps(data), stream=True)

# Verificar si la solicitud fue exitosa
if response.status_code == 200:
    full_response = ""  # Variable para almacenar la respuesta completa

    # Procesar la respuesta en caso de transmisión (stream)
    for line in response.iter_lines():
        if line:
            # Decodificar la línea y limpiar
            line_content = line.decode("utf-8").strip()

            # Ignorar líneas vacías o el mensaje de finalización
            if line_content == "data: [DONE]":
                break

            if line_content.startswith("data:"):
                try:
                    json_data = json.loads(line_content[5:].strip())
                    
                    # Verificar si la respuesta contiene datos válidos
                    if "choices" in json_data and json_data["choices"]:
                        content = json_data["choices"][0]["delta"].get("content", "")
                        full_response += content  # Acumular el fragmento
                except json.JSONDecodeError:
                    # Omitir cualquier error de decodificación de JSON
                    continue

    print("Respuesta completa del asistente:")
    print(full_response)
else:
    print(f"Error {response.status_code}: {response.text}")
