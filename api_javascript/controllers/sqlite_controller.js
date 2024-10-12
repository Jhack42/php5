
// controllers/contenedorController.js
const contenedorModel = require('../models/sqlite_model');

// Controlador para obtener todos los datos de la vista "vista_contenedores2"
const getContenedores = (req, res) => {
    contenedorModel.getAllContenedores((err, contenedores) => {
        if (err) {
            return res.status(500).json({ error: 'Error al obtener los contenedores' });
        }
        res.json(contenedores);
    });
};

// Controlador para obtener un contenedor por su ID
const getContenedorById = (req, res) => {
    const id = req.params.id;
    contenedorModel.getContenedorById(id, (err, contenedor) => {
        if (err) {
            return res.status(500).json({ error: 'Error al obtener el contenedor' });
        }
        if (!contenedor) {
            return res.status(404).json({ error: 'Contenedor no encontrado' });
        }
        res.json(contenedor);
    });
};

module.exports = {
    getContenedores,
    getContenedorById
};
