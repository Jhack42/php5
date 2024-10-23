import express from 'express';
import path from 'path';

// Inicializa la aplicación de Express
const app = express();

// Puerto donde se ejecutará el servidor
const PORT = 3000;

// Middleware para servir archivos estáticos (HTML, CSS, JS)
app.use(express.static(path.join(__dirname, 'public')));

// Ruta para la API que devuelve datos simulados
app.get('/api/data', (req, res) => {
  const data = [
    {
      id: 1,
      name: "Actividades",
      image: "https://i.postimg.cc/zBbGtfPT/eventos.png",
      href: "/vista2" // Aquí añades la ruta para la vista 2
    },

    {
      id: 2,
      name: "Notificaciones",
      image: "https://i.postimg.cc/s21jxXzb/Notificaciones.jpg",
      href: "/"
    }
  ];
  res.json(data); // Devuelve los datos en formato JSON
});

// Ruta para cargar la vista principal (index.html)
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Ruta para cargar la vista 2 (vista2.html)
app.get('/vista2', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'vista2.html'));
});


// Inicia el servidor
app.listen(PORT, () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
