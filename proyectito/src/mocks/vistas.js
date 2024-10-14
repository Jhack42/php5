// Emulación de la respuesta de la API de vistas
export const vistasMock = [
    {
      contenedor_id: 1,
      contenedor_name: "Contendores",
      api_contenidos: [null, null, null, null, null, null, null, null, null, null], // Array para almacenar los contenidos
      contenedor_imagenes: [null, null, null, null, null, null, null, null, null, null], // Array para las imágenes
      botones: [null, null, null, null, null, null, null, null, null, null], // Array para los botones
    },
    // Agrega más vistas si es necesario
  ];
  
  // Función que simula la llamada a la API
  export const getVistasMock = () => {
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve(vistasMock);
      }, 1000); // Simula un retraso de 1 segundo
    });
  };
  