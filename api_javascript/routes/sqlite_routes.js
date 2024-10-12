// routes/userRoutes.js
const express = require('express');
const router = express.Router();
const contenedorController = require('../controllers/sqlite_controller');


// Ruta para obtener todos los contenedores
router.get('/contenedores', contenedorController.getContenedores);

// Ruta para obtener un contenedor por su ID
router.get('/contenedores/:id', contenedorController.getContenedorById);

module.exports = router;
