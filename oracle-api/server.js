require('dotenv').config(); // Cargar variables de entorno
const express = require('express');
const oracledb = require('oracledb');
const cors = require('cors');

const app = express();
const port = 3001; // Puedes cambiar el puerto según prefieras

app.use(cors()); // Habilitar CORS para permitir el acceso desde otras máquinas
app.use(express.json()); // Para manejar datos en formato JSON

// Configuración de conexión a Oracle
const dbConfig = {
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  connectString: process.env.DB_CONNECTIONSTRING,
};

// Ruta de ejemplo para consultar datos desde Oracle
app.get('/data', async (req, res) => {
  let connection;

  try {
    connection = await oracledb.getConnection(dbConfig);

    const result = await connection.execute(
      `SELECT * FROM your_table`,  // Reemplaza "your_table" con tu tabla
      []  // Si necesitas parámetros, colócalos aquí
    );

    res.json(result.rows); // Devuelve los datos en formato JSON
  } catch (err) {
    console.error('Error al ejecutar la consulta:', err);
    res.status(500).send('Error al ejecutar la consulta en Oracle');
  } finally {
    if (connection) {
      try {
        await connection.close(); // Cerrar la conexión
      } catch (err) {
        console.error('Error al cerrar la conexión:', err);
      }
    }
  }
});

app.listen(port, '0.0.0.0', () => {
  console.log(`Servidor escuchando en http://localhost:${port}`);
});
