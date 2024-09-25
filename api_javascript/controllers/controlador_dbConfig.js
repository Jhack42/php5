const oracledb = require('oracledb');
const dbConfig = require('../config/dbConfig');

// Controlador para obtener datos desde la base de datos Oracle
const obtenerDatos = async (req, res) => {
    let connection;
    try {
        // Establecer la conexión con la base de datos Oracle
        connection = await oracledb.getConnection(dbConfig);

        // Ejecutar una consulta de ejemplo (ajusta según tus necesidades)
        const result = await connection.execute(`SELECT * FROM your_table_name`);

        // Devolver los resultados en formato JSON
        res.json(result.rows);
    } catch (err) {
        console.error('Error al conectar a Oracle: ', err);
        res.status(500).send('Error al conectarse a Oracle');
    } finally {
        if (connection) {
            try {
                // Cerrar la conexión con la base de datos
                await connection.close();
            } catch (err) {
                console.error('Error al cerrar la conexión: ', err);
            }
        }
    }
};

module.exports = {
    obtenerDatos
};
