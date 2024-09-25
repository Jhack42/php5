// routes/vista.js
const express = require('express');
const { getVistaFacultadesEventos } = require('../controllers/vistaController');
const router = express.Router();

router.get('/', getVistaFacultadesEventos);

module.exports = router;
