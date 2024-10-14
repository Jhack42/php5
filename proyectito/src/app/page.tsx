"use client";
import { redirect } from "next/navigation"; // Importa la función redirect

export default function Home() {
  // Redirigir automáticamente a "/vista1"
  redirect("/vista1");

  return null; // No hay necesidad de renderizar nada ya que redirigimos
}
