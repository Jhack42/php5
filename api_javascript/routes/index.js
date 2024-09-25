const express = require('express');
const { getFacultades } = require('../controllers/facultadesController');
const { getEventos } = require('../controllers/eventosController');
const { getVistaFacultadesEventos, createEvento, updateEvento, deleteEvento } = require('../controllers/vistaController');
const { getImage } = require('../controllers/imageController'); // Importar el controlador de imágenes

const router = express.Router();

// Rutas para las imágenes
router.get('/img/:imageName', getImage); // Ruta para servir las imágenes

// Rutas de Facultades
router.get('/facultades', getFacultades);

// Rutas de Eventos
router.get('/eventos', getEventos);

// Rutas de la Vista Facultades-Eventos
router.get('/vista-facultades-eventos', getVistaFacultadesEventos);

// Ruta para crear un nuevo evento (POST)
router.post('/vista-facultades-eventos', createEvento);

// Ruta para actualizar un evento existente (PUT)
router.put('/vista-facultades-eventos/:evento_id', updateEvento);

// Ruta para eliminar un evento existente (DELETE)
router.delete('/vista-facultades-eventos/:evento_id', deleteEvento);

module.exports = router;
