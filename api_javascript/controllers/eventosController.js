// controllers/eventosController.js
const oracledb = require('oracledb');

async function getEventos(req, res) {
  let connection;
  try {
    connection = await oracledb.getConnection();
    const result = await connection.execute(`SELECT * FROM PHP5.EVENTOS`);
    res.json(result.rows);
  } catch (err) {
    res.status(500).send('Error en la consulta: ' + err.message);
  } finally {
    if (connection) {
      try {
        await connection.close();
      } catch (err) {
        console.error('Error al cerrar la conexión', err);
      }
    }
  }
}

module.exports = { getEventos };
