import requests

BASE_URL = "http://127.0.0.1:8000/api/supcategoria"
CATEGORIA_URL = "http://127.0.0.1:8000/api/categoria"

def check_categoria_exists(n_id_categoria):
    """Verifica si la categoría existe."""
    response = requests.get(f"{CATEGORIA_URL}/{n_id_categoria}")
    return response.status_code == 200

def test_create():
    """Prueba la creación de una nueva subcategoría."""
    n_id_categoria = 10
    v_descripcion = "Subcategoría de prueba desde Python"

    if not check_categoria_exists(n_id_categoria):
        print(f"La categoría {n_id_categoria} no existe.")
        return

    data = {"n_id_categoria": n_id_categoria, "v_descripcion": v_descripcion}
    response = requests.post(f"{BASE_URL}/nuevo", json=data)

    if response.status_code == 201:
        print("Subcategoría creada correctamente:", response.json())
    else:
        print("Error al crear la subcategoría:", response.status_code, response.text)

def test_update():
    """Prueba la actualización de una subcategoría."""
    id_subcategoria = 1
    data = {"v_descripcion": "Subcategoría actualizada desde Python"}
    response = requests.put(f"{BASE_URL}/editar/{id_subcategoria}", json=data)

    if response.status_code == 200:
        print("Subcategoría actualizada correctamente:", response.json())
    elif response.status_code == 404:
        print(f"La subcategoría {id_subcategoria} no fue encontrada.")
    else:
        print("Error al actualizar la subcategoría:", response.status_code, response.text)

def test_delete():
    """Prueba la eliminación de una subcategoría."""
    id_subcategoria = 1
    response = requests.put(f"{BASE_URL}/eliminar/{id_subcategoria}")

    if response.status_code == 200:
        print("Subcategoría eliminada correctamente:", response.json())
    elif response.status_code == 404:
        print(f"La subcategoría {id_subcategoria} no fue encontrada.")
    else:
        print("Error al eliminar la subcategoría:", response.status_code, response.text)

if __name__ == "__main__":
    test_create()
    test_update()
    test_delete()
