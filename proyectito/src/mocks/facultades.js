// Emulación de la respuesta de la API de facultades
export const facultadesMock = [
  {
    "contenedor_id": 1,
    "contenedor_name": "Facultades Generales",
    "contenedor_imagen": "/img/0001.jpg" // Ruta relativa a la imagen en la carpeta public
  }
  // Agrega más facultades según sea necesario
];

// Función que simula la llamada a la API
export const getFacultadesMock = () => {
  return new Promise((resolve) => {
    setTimeout(() => {
      resolve(facultadesMock);
    }, 1000); // Simula un retraso de 1 segundo
  });
};
