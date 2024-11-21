<template>
    <div class="min-h-screen bg-gray-100">
      <div class="bg-red-600 text-white p-4">
        <h1 class="text-2xl font-bold">FACULTADES</h1>
      </div>

      <div class="p-4">
        <!-- Facultades -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="faculty in faculties" :key="faculty.id" class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-0">
              <!-- Imagen de facultad -->
              <img
                :src="faculty.imageUrl"
                :alt="`${faculty.name} Edificio`"
                width="400"
                height="200"
                class="w-full h-48 object-cover"
              />
              <div class="p-4">
                <h3 class="font-semibold mb-2">{{ faculty.name }}</h3>
                <ul class="list-disc list-inside text-gray-600">
                  <li v-for="(specialty, index) in faculty.specialties" :key="index">{{ specialty }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  // Definimos las facultades con sus especialidades
  const faculties = [
    {
      id: 'A',
      name: 'Arquitectura, Urbanismo y Artes',
      specialties: ['A1 - Arquitectura'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'C',
      name: 'Ingeniería Civil',
      specialties: ['C1 - Ingeniería Civil'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'E',
      name: 'Ingeniería Económica, Estadística y Ciencias Sociales',
      specialties: ['E1 - Ingeniería Económica', 'E3 - Ingeniería Estadística'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'G',
      name: 'Ingeniería Geológica, Minera y Metalúrgica',
      specialties: ['G1 - Ingeniería Geológica', 'G2 - Ingeniería Metalúrgica', 'G3 - Ingeniería de Minas'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'I',
      name: 'Ingeniería Industrial y Sistemas',
      specialties: ['I1 - Ingeniería Industrial', 'I2 - Ingeniería de Sistemas', 'I3 - Ingeniería de Software'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'L',
      name: 'Ingeniería Eléctrica y Electrónica',
      specialties: ['L1 - Ingeniería Eléctrica', 'L2 - Ingeniería Electrónica', 'L3 - Ingeniería de Telecomunicaciones', 'L4 - Ingeniería de Ciberseguridad'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'M',
      name: 'Ingeniería Mecánica',
      specialties: ['M3 - Ingeniería Mecánica', 'M4 - Ingeniería Mecánica Eléctrica', 'M5 - Ingeniería Naval', 'M6 - Ingeniería Mecatrónica'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'N',
      name: 'Ciencias',
      specialties: ['N1 - Física', 'N2 - Matemáticas', 'N3 - Química', 'N5 - Ingeniería Física', 'N6 - Ciencia de la Computación'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'P',
      name: 'Ingeniería de Petróleo, Gas Natural y Petroquímica',
      specialties: ['P2 - Ingeniería Petroquímica', 'P3 - Ingeniería de Petróleo y Gas Natural'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'Q',
      name: 'Ingeniería Química y Textil',
      specialties: ['Q1 - Ingeniería Química', 'Q2 - Ingeniería Textil'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
    {
      id: 'S',
      name: 'Ingeniería Ambiental',
      specialties: ['S1 - Ingeniería Sanitaria', 'S2 - Ingeniería de Higiene y Seguridad Industrial', 'S3 - Ingeniería Ambiental'],
      imageUrl: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/images%20(1).jfif-AaQF0eiqU1WOAgDryVkxZuL8h3G4v7.jpeg',
    },
  ]
  </script>

  <style scoped>
  .card {
    border: 1px solid #ddd;
    border-radius: 8px;
  }

  .card-content {
    padding: 1rem;
  }

  .list-disc {
    list-style-type: disc;
  }

  h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
  }

  ul {
    margin-top: 0;
  }

  li {
    margin-left: 1.5rem;
    margin-bottom: 0.5rem;
  }

  img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }
  </style>
