// routes/index.js
const express = require('express');
const { getFacultades } = require('../controllers/facultadesController');
const { getEventos } = require('../controllers/eventosController');
//const { getVistaFacultadesEventos } = require('../controllers/vistaController');
const { getVistaFacultadesEventos, createEvento, updateEvento, deleteEvento } = require('../controllers/vistaController');

const router = express.Router();
//-- ----------------------------------------------------------------------------------------------

const multer = require('multer');
const path = require('path');

// Configuración de multer para guardar imágenes en la carpeta img
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, path.join(__dirname, '../img'));
  },
  filename: (req, file, cb) => {
    cb(null, Date.now() + path.extname(file.originalname)); // Nombre único
  }
});

const upload = multer({ storage: storage });

router.post('/upload-image', upload.single('image'), (req, res) => {
  res.json({
    message: 'Imagen subida exitosamente',
    imageUrl: `/img/${req.file.filename}`
  });
});

//-- ----------------------------------------------------------------------------------------------

// Rutas de Facultades
router.get('/facultades', getFacultades);

// Rutas de Eventos
router.get('/eventos', getEventos);

// Rutas de la Vista Facultades-Eventos
//router.get('/vista-facultades-eventos', getVistaFacultadesEventos);


//-- ---------------------------------------------------------
// Ruta para obtener todos los eventos (GET)
router.get('/vista-facultades-eventos', getVistaFacultadesEventos);

// Ruta para crear un nuevo evento (POST)
router.post('/vista-facultades-eventos', createEvento);

// Ruta para actualizar un evento existente (PUT)
router.put('/vista-facultades-eventos/:evento_id', updateEvento);

// Ruta para eliminar un evento existente (DELETE)
router.delete('/vista-facultades-eventos/:evento_id', deleteEvento);

module.exports = router;
