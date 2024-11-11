const express = require('express');
const path = require('path');
const { connect } = require('./config/db');
const routes = require('./routes'); // Importa todas las rutas desde el archivo index.js
const userRoutes = require('./routes/sqlite_routes');
const app = express();
app.use(express.json());

// Servir archivos estáticos desde la carpeta 'public'
app.use(express.static(path.join(__dirname, 'public')));

// Usar las rutas unificadas
app.use('/api', routes);

// Usar rutas
app.use('/api_sqlite', userRoutes);

// Iniciar el servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor corriendo en el puerto ${PORT}`);
});

// Conectar a la base de datos
connect();
                                                                                                                        