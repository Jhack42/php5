// config/db.js
const oracledb = require('oracledb');
require('dotenv').config();

async function connect() {
  try {
    await oracledb.createPool({
      user: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      connectString: process.env.DB_CONNECTIONSTRING,
    });
    console.log('Conectado a la base de datos Oracle');
  } catch (err) {
    console.error('Error al conectar a la base de datos', err);
  }
}

module.exports = {
  connect,
};
