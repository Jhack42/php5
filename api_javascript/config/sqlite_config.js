// config/database.js
require('dotenv').config();
const sqlite3 = require('sqlite3').verbose();

const connectDB = () => {
    return new sqlite3.Database(process.env.ARCHIVO_SQLITE, (err) => {
        if (err) {
            console.error('Error al conectar a la base de datos:', err.message);
        } else {
            console.log('Conexión exitosa a la base de datos SQLite');
        }
    });
};

module.exports = connectDB;
