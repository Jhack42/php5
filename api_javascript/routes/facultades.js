// routes/facultades.js
const express = require('express');
const { getFacultades } = require('../controllers/facultadesController');
const router = express.Router();

router.get('/', getFacultades);

module.exports = router;
