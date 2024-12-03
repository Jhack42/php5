import requests

# URL base de la API
BASE_URL = "http://127.0.0.1:8000/omesa/categorias"

# Test para crear una nueva categoría (POST)
def test_create_category():
    print("Iniciando prueba de creación de categoría...")  # Confirmar que el script está corriendo

    # Datos de la nueva categoría
    new_category = {
        "V_TITULO": "Nueva Categoria de Test"
    }

    try:
        # Enviamos la solicitud POST para crear la nueva categoría
        response = requests.post(BASE_URL, json=new_category)

        # Verificamos si la creación fue exitosa
        if response.status_code == 201:
            created_category = response.json()
            # Verificamos si el título de la categoría coincide
            if created_category["v_titulo"] == new_category["V_TITULO"]:
                print(f"Categoría '{new_category['V_TITULO']}' creada correctamente.")
            else:
                print("El título de la categoría no coincide con el enviado.")
        else:
            print(f"No se pudo crear la categoría. Status code: {response.status_code}")
            try:
                # Imprimimos el mensaje de error de la API
                error_message = response.json()
                print(f"Error: {error_message.get('error', 'No se proporcionó un mensaje de error.')}")
            except ValueError:
                # Si no se puede parsear la respuesta como JSON
                print("No se pudo obtener un mensaje de error claro.")

    except requests.exceptions.RequestException as e:
        # Si hay un problema con la conexión o con la API
        print(f"Hubo un problema al hacer la solicitud: {e}")


# Llamar a la función para ejecutar la prueba
if __name__ == "__main__":
    test_create_category()
