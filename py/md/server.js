// server.js
const express = require('express');
const path = require('path');

const app = express();
const PORT = 3000;

// Servir los archivos estáticos de la carpeta "public"
app.use(express.static(path.join(__dirname, 'public')));

// Iniciar el servidor en el puerto 3000
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
