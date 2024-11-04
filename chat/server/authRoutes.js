// server/authRoutes.js
import express from 'express';
import bcrypt from 'bcrypt';
import multer from 'multer';
import path from 'path';
import { connectToDB } from './db.js';

const router = express.Router();

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, path.join(process.cwd(), 'uploads'));
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    const originalName = file.originalname.replace(/\s+/g, '_');
    const finalFilename = uniqueSuffix + '-' + originalName;
    console.log('Nombre de archivo generado:', finalFilename);
    cb(null, finalFilename);
  }
});
const upload = multer({ storage: storage });

// Ruta de inicio de sesión
router.post('/login', async (req, res) => {
  const { email, password } = req.body;
  console.log('Datos recibidos para login:', { email, password });

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
        req.session.userId = parseInt(userId, 10);
        req.session.username = username;
        console.log(`Inicio de sesión exitoso para el usuario con ID: ${userId}`);
        res.json({ success: true, message: 'Login exitoso', userId });
      } else {
        console.log('Contraseña incorrecta');
        res.status(401).json({ success: false, message: 'Contraseña incorrecta' });
      }
    } else {
      console.log('Usuario no encontrado');
      res.status(404).json({ success: false, message: 'Usuario no encontrado' });
    }
  } catch (err) {
    console.error('Error en login:', err);
    res.status(500).json({ success: false, message: 'Error en el servidor' });
  }
});

// Ruta para obtener datos de perfil
router.get('/profile', async (req, res) => {
  const userId = req.session.userId;
  if (!userId) {
    console.log('No autenticado - Falta userId en sesión');
    return res.status(401).json({ success: false, message: 'No autenticado' });
  }

  try {
    const connection = await connectToDB();
    const result = await connection.execute(
      `SELECT NOMBRE, CORREO, IMG FROM USUARIOS WHERE ID = :userId`,
      { userId }
    );

    if (result.rows.length > 0) {
      const [name, email, img] = result.rows[0];
      console.log(`Perfil cargado correctamente para el usuario con ID: ${userId}`);
      res.json({ success: true, name, email, img });
    } else {
      console.log('Usuario no encontrado al cargar el perfil');
      res.status(404).json({ success: false, message: 'Usuario no encontrado' });
    }
  } catch (err) {
    console.error('Error al obtener perfil:', err);
    res.status(500).json({ success: false, message: 'Error al obtener perfil' });
  }
});

router.post('/update-profile', upload.single('profileImage'), async (req, res) => {
  const { userId, username, email, password } = req.body;
  const profileImage = req.file ? req.file.filename : null;

  console.log('Datos recibidos para la actualización de perfil:', { userId, username, email, profileImage });

  // Validación de campos obligatorios
  if (!userId || !username || !email) {
    console.log("Datos de entrada faltantes:", { userId, username, email });
    return res.status(400).json({ success: false, message: 'Nombre, correo y userId son obligatorios' });
  }

  try {
    const connection = await connectToDB();
    let updateQuery = 'UPDATE USUARIOS SET NOMBRE = :username, CORREO = :email';
    const binds = { userId: parseInt(userId, 10), username, email };

    if (password) {
      updateQuery += ', CONTRASEÑA = :password';
      binds.password = await bcrypt.hash(password, 10);
    }

    if (profileImage) {
      updateQuery += ', IMG = :profileImage';
      binds.profileImage = profileImage;
      console.log('Imagen actualizada para el usuario con ID:', userId, 'Nombre de imagen:', profileImage);
    }

    updateQuery += ' WHERE ID = :userId';

    console.log('Datos para actualización en la base de datos:', binds);

    const result = await connection.execute(updateQuery, binds, { autoCommit: true });
    console.log('Resultado de la actualización en la base de datos:', result);

    if (result.rowsAffected === 0) {
      console.log('No se pudo actualizar el perfil - Ninguna fila afectada');
      return res.status(400).json({ success: false, message: 'No se pudo actualizar el perfil' });
    }

    console.log('Perfil actualizado correctamente para el usuario con ID:', userId);
    res.json({ success: true, message: 'Perfil actualizado' });
  } catch (err) {
    console.error('Error al actualizar perfil:', err);
    res.status(500).json({ success: false, message: 'Error al actualizar perfil' });
  }
});

export default router;
