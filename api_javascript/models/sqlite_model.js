
const connectDB = require('../config/sqlite_config');

// models/contenedorModel.js


// Consulta para obtener todos los datos de la vista "vista_contenedores2"
const getAllContenedores = (callback) => {
    const db = connectDB();
    const query = `SELECT * FROM vista_contenedores2`;

    db.all(query, (err, rows) => {
        if (err) {
            callback(err, null);
        } else {
            callback(null, rows);
        }
    });

    db.close((err) => {
        if (err) {
            console.error('Error al cerrar la base de datos:', err.message);
        }
    });
};

// Consulta para obtener un contenedor específico por su ID
const getContenedorById = (id, callback) => {
    const db = connectDB();
    const query = `SELECT * FROM vista_contenedores2 WHERE contenedor_id = ?`;

    db.get(query, [id], (err, row) => {
        if (err) {
            callback(err, null);
        } else {
            callback(null, row);
        }
    });

    db.close((err) => {
        if (err) {
            console.error('Error al cerrar la base de datos:', err.message);
        }
    });
};

module.exports = {
    getAllContenedores,
    getContenedorById
};
