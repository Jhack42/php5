"use client";
import { useEffect, useState } from "react";
import { getVistasMock } from "@/mocks/vistas";  // Asegúrate de que la ruta apunta al archivo correcto

export default function VistasPage() {
  const [vistas, setVistas] = useState([]);

  useEffect(() => {
    async function fetchVistas() {
      try {
        const data = await getVistasMock();
        setVistas(data);
      } catch (error) {
        console.error("Error al obtener las vistas:", error);
      }
    }

    fetchVistas();
  }, []);

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-4xl font-bold text-center mb-8">Vistas</h1>
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        {vistas.map((vista) => (
          <div key={vista.contenedor_id} className="card bg-gray-100 shadow-md rounded-lg p-4">
            <h2 className="text-2xl font-bold mb-4">{vista.contenedor_name}</h2>
            {/* Mostrar los contenidos, imágenes y botones de manera dinámica */}
            {vista.api_contenidos.map((contenido, index) => (
              <p key={index}>Contenido {index + 1}: {contenido ? contenido : "No disponible"}</p>
            ))}
            {vista.contenedor_imagenes.map((imagen, index) => (
              <p key={index}>Imagen {index + 1}: {imagen ? imagen : "No disponible"}</p>
            ))}
            {vista.botones.map((boton, index) => (
              <button key={index} className="mt-2 bg-blue-500 text-white p-2 rounded">
                Botón {index + 1}: {boton ? boton : "No disponible"}
              </button>
            ))}
          </div>
        ))}
      </div>
    </div>
  );
}
