const express = require('express');
const path = require('path');
const app = express();
const port = 3000;

// Servir archivos estáticos (HTML, CSS, JS) desde la carpeta public
app.use(express.static('public'));

// Ruta para servir el video con el reproductor por defecto del navegador
app.get('/api/video/:video', (req, res) => {
  const videoPath = path.join(__dirname, 'videos', req.params.video);
  res.sendFile(videoPath);
});

// Ruta para servir el video con la interfaz personalizada
app.get('/api/player/:video', (req, res) => {
  const videoPath = req.params.video;
  res.sendFile(path.join(__dirname, 'public', 'reproductor.html'), {
    headers: {
      'X-Video-Path': videoPath  // Pasamos el nombre del video como encabezado
    }
  });
});

app.listen(port, () => {
  console.log(`Servidor escuchando en http://localhost:${port}`);
});
