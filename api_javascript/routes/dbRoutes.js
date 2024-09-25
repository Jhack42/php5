const express = require('express');
const router = express.Router();
const dbController = require('../controllers/controlador_dbConfig');

// Definir la ruta para obtener datos de la base de datos
router.get('/data', dbController.obtenerDatos);

module.exports = router;
