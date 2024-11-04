// server/db.js
import oracledb from 'oracledb';
import dotenv from 'dotenv';

dotenv.config();

let dbConnection;

export async function connectToDB() {
  if (dbConnection) return dbConnection;

  try {
    dbConnection = await oracledb.getConnection({
      user: process.env.DATABASE_URL.split(':')[1].split('//')[1],
      password: process.env.DATABASE_URL.split(':')[2].split('@')[0],
      connectString: process.env.DATABASE_URL.split('@')[1],
    });
    console.log('Conectado a la base de datos Oracle');
    return dbConnection;
  } catch (err) {
    console.error('Error al conectar a la base de datos:', err);
  }
}
