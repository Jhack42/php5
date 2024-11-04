// server/chatRoutes.js
import express from 'express';
import { connectToDB } from './db.js';

const router = express.Router();

router.get('/recent-chats', async (req, res) => {
  const userId = req.session.userId;

  try {
    const connection = await connectToDB();
    const result = await connection.execute(
      `SELECT U.ID AS userId, U.NOMBRE AS nombre, U.IMG AS img,
              M.MENSAJE AS ultimoMensaje, MAX(M.FECHA) AS ultimaFecha
       FROM MENSAJES M
       JOIN USUARIOS U ON (U.ID = M.DESTINATARIO_ID OR U.ID = M.USUARIO_ID)
       WHERE (M.USUARIO_ID = :userId OR M.DESTINATARIO_ID = :userId)
         AND U.ID != :userId
       GROUP BY U.ID, U.NOMBRE, U.IMG, M.MENSAJE
       ORDER BY ultimaFecha DESC`,
      { userId }
    );

    const chats = result.rows.map(row => ({
      userId: row[0],
      nombre: row[1],
      img: row[2],
      ultimoMensaje: row[3],
      ultimaFecha: row[4]
    }));

    res.json(chats);
  } catch (err) {
    console.error('Error al cargar los chats recientes:', err);
    res.status(500).json({ error: 'Error al cargar los chats recientes' });
  }
});

router.get('/search-users', async (req, res) => {
  const searchTerm = req.query.q ? req.query.q.toLowerCase() : '';

  try {
    const connection = await connectToDB();
    const result = await connection.execute(
      `SELECT ID, NOMBRE, IMG FROM USUARIOS WHERE LOWER(NOMBRE) LIKE :searchTerm`,
      { searchTerm: `%${searchTerm}%` }
    );

    res.json(result.rows);
  } catch (err) {
    console.error('Error en la búsqueda:', err);
    res.status(500).json({ error: 'Error en la búsqueda' });
  }
});

export default router;
