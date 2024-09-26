const express = require('express');
const path = require('path');
const { connect } = require('./config/db'); // Aquí debes asegurarte que el archivo db.js contiene la función connect()
const routes = require('./routes'); // Importa todas las rutas generales (suponiendo que tienes más rutas aquí)
const dbRoutes = require('./routes/dbRoutes'); // Importa las rutas de la base de datos

const app = express();
app.use(express.json());

// Servir archivos estáticos desde la carpeta 'public'
app.use(express.static(path.join(__dirname, 'public')));

// Usar las rutas generales
app.use('/api', routes);

// Usar las rutas de la base de datos
app.use('/api/db', dbRoutes); // Usar prefijo '/api/db' para las rutas relacionadas con la base de datos

// Iniciar el servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor corriendo en el puerto ${PORT}`);
});

// Conectar a la base de datos
connect();
