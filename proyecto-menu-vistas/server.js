const express = require('express');
const cookieParser = require('cookie-parser');
const path = require('path');
const app = express();
const PORT = 3000;

// Middleware para manejar cookies
app.use(cookieParser());

// Servir archivos estáticos
app.use(express.static(path.join(__dirname, 'public')));

// Ruta para las diferentes vistas
app.get('/api/vista/:view', (req, res) => {
  const view = req.params.view;
  const viewPath = path.join(__dirname, 'views', `${view}.html`);
  res.sendFile(viewPath);
});

// Inicio del servidor
app.listen(PORT, () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
