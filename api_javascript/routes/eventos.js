// routes/eventos.js
const express = require('express');
const { getEventos } = require('../controllers/eventosController');
const router = express.Router();

router.get('/', getEventos);

module.exports = router;
