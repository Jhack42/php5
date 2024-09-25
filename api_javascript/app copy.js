// app.js
const express = require('express');
const { connect } = require('./config/db');
const routes = require('./routes'); // Importa todas las rutas desde el archivo index.js

const app = express();
app.use(express.json());

// Usar las rutas unificadas
app.use('/api', routes);

// Iniciar el servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor corriendo en el puerto ${PORT}`);
});

// Conectar a la base de datos
connect();

