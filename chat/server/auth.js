// server/auth.js
import express from 'express';
import bcrypt from 'bcrypt';
import { connectToDB } from './db.js';

const router = express.Router();

router.post('/login', async (req, res) => {
  const { email, password } = req.body;
  try {
    const connection = await connectToDB();
    const result = await connection.execute(
      `SELECT ID, NOMBRE, CONTRASEÑA FROM USUARIOS WHERE CORREO = :email`,
      { email }
    );

    if (result.rows.length > 0) {
      const [userId, username, hashedPassword] = result.rows[0];
      const match = await bcrypt.compare(password, hashedPassword);

      if (match) {
        req.session.userId = userId;
        req.session.username = username;
        res.json({ success: true, message: 'Login exitoso', userId });
      } else {
        res.status(401).json({ success: false, message: 'Contraseña incorrecta' });
      }
    } else {
      res.status(404).json({ success: false, message: 'Usuario no encontrado' });
    }
  } catch (err) {
    console.error('Error en login:', err);
    res.status(500).json({ success: false, message: 'Error en el servidor' });
  }
});

router.post('/register', async (req, res) => {
  const { name, email, password } = req.body;
  const hashedPassword = await bcrypt.hash(password, 10);

  try {
    const connection = await connectToDB();
    await connection.execute(
      `INSERT INTO USUARIOS (NOMBRE, CORREO, CONTRASEÑA) VALUES (:name, :email, :hashedPassword)`,
      { name, email, hashedPassword },
      { autoCommit: true }
    );
    res.json({ success: true, message: 'Registro exitoso' });
  } catch (err) {
    console.error('Error en registro:', err);
    res.status(500).json({ success: false, message: 'Error en el servidor' });
  }
});

router.post('/logout', (req, res) => {
  req.session.destroy((err) => {
    if (err) {
      return res.status(500).json({ success: false, message: 'Error al cerrar sesión' });
    }
    res.clearCookie('username');
    res.json({ success: true, message: 'Sesión cerrada' });
  });
});

// Middleware para verificar autenticación
export function isAuthenticated(req, res, next) {
  if (req.session.userId) {
    next();
  } else {
    res.status(401).json({ success: false, message: 'No autenticado' });
  }
}

export default router;
