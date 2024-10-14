"use client";

import { useEffect, useState } from "react";
import { getFacultades } from "@/services/api";
import Image from "next/image";

export default function Home() {
  const [facultades, setFacultades] = useState([]);

  useEffect(() => {
    async function fetchFacultades() {
      try {
        const data = await getFacultades();
        setFacultades(data);
      } catch (error) {
        console.error("Error al obtener las facultades:", error);
      }
    }

    fetchFacultades();
  }, []);

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-4xl font-bold text-center text-[#8B0202] mb-8">
        Facultades
      </h1>
      {/* Grid responsivo que adapta el número de columnas según el tamaño de la pantalla */}
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        {facultades.map((facultad) => (
          <Card
            key={facultad.contenedor_id}
            title={facultad.contenedor_name}
            image={facultad.contenedor_imagen}
            footerText="Más información"
          />
        ))}
      </div>
    </div>
  );
}

function Card({ title, image, footerText }) {
  return (
    <div className="bg-[#E8CFA0] shadow-lg rounded-lg overflow-hidden transition-transform hover:scale-105">
      {/* Imagen principal */}
      {image && (
        <div className="relative w-full h-48 md:h-56">
          <Image
            src={image}
            alt={title}
            fill
            className="object-cover"
            sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
          />
        </div>
      )}

      {/* Contenido */}
      <div className="p-4">
        <p className="text-xl md:text-2xl font-bold text-[#8B0202] mb-2">
          {title}
        </p>
      </div>

      {/* Pie de tarjeta */}
      <div className="bg-[#8B0202] text-white p-3 md:p-4 text-center cursor-pointer hover:bg-[#6D0101]">
        {footerText}
      </div>
    </div>
  );
}
