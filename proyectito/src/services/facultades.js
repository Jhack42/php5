// Función para obtener facultades de la API real
export const getFacultades = async () => {
    const response = await fetch('https://api.realserver.com/facultades');
    if (!response.ok) {
      throw new Error('Error al obtener las facultades');
    }
    return response.json();
  };
  