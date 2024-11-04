import express from 'express';
import logger from 'morgan';
import { Server } from 'socket.io';
import { createServer } from 'node:http';
import path from 'path';
import oracledb from 'oracledb';
import dotenv from 'dotenv';
import bcrypt from 'bcrypt';
import cookieParser from 'cookie-parser';
import session from 'express-session';

dotenv.config();

const port = process.env.PORT ?? 3000;
const app = express();
const server = createServer(app);
const io = new Server(server, {
  connectionStateRecovery: {
    maxDisconnectionDuration: 2 * 60 * 1000,
    useFindBestOffset: true,
  },
});

// Middleware
app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(session({
  secret: 'supersecretkey', // Cambia esto por una clave segura en producción
  resave: false,
  saveUninitialized: false,
  cookie: { secure: false, httpOnly: true }
}));

// Conectar a Oracle (crear una conexión única reutilizable)
let dbConnection;

async function connectToDB() {
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

// Rutas de autenticación
app.post('/login', async (req, res) => {
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

app.post('/register', async (req, res) => {
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

app.post('/logout', (req, res) => {
  req.session.destroy((err) => {
    if (err) {
      return res.status(500).json({ success: false, message: 'Error al cerrar sesión' });
    }
    res.clearCookie('username');
    res.json({ success: true, message: 'Sesión cerrada' });
  });
});

// Middleware para verificar la autenticación
function isAuthenticated(req, res, next) {
  if (req.session.userId) {
    next();
  } else {
    res.status(401).json({ success: false, message: 'No autenticado' });
  }
}

// Middleware de autenticación para Socket.IO
io.use((socket, next) => {
  const userId = socket.handshake.auth.userId;
  if (userId) {
    socket.userId = userId;
    next();
  } else {
    next(new Error('No autenticado'));
  }
});

io.on('connection', (socket) => {
  console.log(`Usuario conectado: ${socket.userId}`);

  // Aquí añades el manejo de eventos para cargar el historial de mensajes y enviar mensajes
  socket.on('loadChat', async (recipientId) => {
    const userId = socket.userId;
    try {
      const connection = await connectToDB();
      const result = await connection.execute(
        `SELECT MENSAJE, USUARIO_ID, FECHA 
         FROM MENSAJES 
         WHERE (USUARIO_ID = :userId AND DESTINATARIO_ID = :recipientId) 
            OR (USUARIO_ID = :recipientId AND DESTINATARIO_ID = :userId) 
         ORDER BY FECHA ASC`,
        { userId, recipientId }
      );

      const messages = result.rows.map(row => ({
        content: row[0],
        senderId: row[1],
        timestamp: row[2]
      }));

      socket.emit('chat history', messages);
    } catch (e) {
      console.error('Error al cargar el historial de mensajes:', e);
    }
  });

  socket.on('chat message', async ({ content, recipientId }) => {
    const senderId = socket.userId;
    try {
      const connection = await connectToDB();

      // Inserción de mensaje en la tabla MENSAJES
      await connection.execute(
        `INSERT INTO MENSAJES (USUARIO_ID, DESTINATARIO_ID, MENSAJE, FECHA, ESTADO) 
         VALUES (:senderId, :recipientId, :content, SYSDATE, 'enviado')`,
        { senderId, recipientId, content },
        { autoCommit: true }
      );

      // Enviar el mensaje de vuelta al remitente y al destinatario
      socket.emit('chat message', content, senderId, new Date());
      const recipientSocket = Array.from(io.sockets.sockets.values()).find(s => s.userId === recipientId);
      if (recipientSocket) {
        recipientSocket.emit('chat message', content, senderId, new Date());
      }
    } catch (e) {
      console.error('Error al enviar mensaje:', e);
    }
  });

  socket.on('disconnect', () => {
    console.log(`Usuario desconectado: ${socket.userId}`);
  });
});


// Servir el archivo HTML para los usuarios autenticados
app.get('/panel', isAuthenticated, (req, res) => {
  res.sendFile(path.join(process.cwd(), 'client', 'chat_panel.html'));
});

// Manejo de conexión de Socket.IO con autenticación basada en sesión
io.use((socket, next) => {
  const username = socket.handshake.auth.username;
  if (username) {
    socket.username = username; // Guardar el username en el socket
    return next();
  }
  return next(new Error('No autenticado'));
});

io.on('connection', async (socket) => {
  console.log(`Usuario conectado: ${socket.username}`);

  const chatId = socket.handshake.auth.chatId ?? 1; // Chat predeterminado

  // Enviar historial de mensajes al cliente conectado
  try {
    const connection = await connectToDB();
    const result = await connection.execute(
      `SELECT MENSAJE, USUARIO_ID, FECHA, ESTADO 
       FROM MENSAJES 
       WHERE CHAT_ID = :chatId 
       ORDER BY FECHA ASC`,
      { chatId }
    );

    result.rows.forEach((row) => {
      const [mensaje, usuarioId, fecha, estado] = row;
      socket.emit('chat message', mensaje, usuarioId, fecha, estado);
    });
  } catch (e) {
    console.error('Error al recuperar mensajes:', e);
  }

  // Escuchar nuevos mensajes
  socket.on('chat message', async (msg) => {
    const userId = socket.handshake.auth.userId;
    if (!userId) {
      console.log('Usuario no autenticado');
      return;
    }

    try {
      const connection = await connectToDB();
      await connection.execute(
        `INSERT INTO MENSAJES (CHAT_ID, USUARIO_ID, MENSAJE, ESTADO) 
         VALUES (:chatId, :userId, :msg, 'enviando')`,
        { chatId, userId, msg },
        { autoCommit: true }
      );
      console.log('Mensaje almacenado en la base de datos');
    } catch (e) {
      console.error('Error al insertar en la base de datos:', e);
    }

    io.emit('chat message', msg, userId, new Date(), 'enviando');
  });

  socket.on('disconnect', () => {
    console.log(`Usuario desconectado: ${socket.username}`);
  });
});

// Ruta de búsqueda de usuarios en el servidor
app.get('/api/search-users', async (req, res) => {
  const searchTerm = req.query.q ? req.query.q.toLowerCase() : '';
  
  try {
    const connection = await connectToDB();
    const result = await connection.execute(
      `SELECT ID, NOMBRE, IMG FROM USUARIOS WHERE LOWER(NOMBRE) LIKE :searchTerm`,
      { searchTerm: `%${searchTerm}%` }
    );

    res.json(result.rows); // Devuelve la lista de usuarios encontrados
  } catch (err) {
    console.error('Error en la búsqueda:', err);
    res.status(500).json({ error: 'Error en la búsqueda' });
  }
});


io.on('connection', (socket) => {
  console.log(`Usuario conectado: ${socket.userId}`);

  // Cargar historial de mensajes entre los usuarios
  socket.on('loadChat', async (recipientId) => {
    const userId = socket.userId;
    try {
      const connection = await connectToDB();
      const result = await connection.execute(
        `SELECT MENSAJE, USUARIO_ID, FECHA 
         FROM MENSAJES 
         WHERE (USUARIO_ID = :userId AND DESTINATARIO_ID = :recipientId) 
            OR (USUARIO_ID = :recipientId AND DESTINATARIO_ID = :userId) 
         ORDER BY FECHA ASC`,
        { userId, recipientId }
      );

      result.rows.forEach((row) => {
        const [mensaje, usuarioId, fecha] = row;
        socket.emit('chat message', mensaje, usuarioId, fecha);
      });
    } catch (e) {
      console.error('Error al cargar el historial de mensajes:', e);
    }
  });

  // Manejo de envío de mensajes
  socket.on('chat message', async ({ message, recipientId }) => {
    const senderId = socket.userId;
    try {
      const connection = await connectToDB();
      await connection.execute(
        `INSERT INTO MENSAJES (USUARIO_ID, DESTINATARIO_ID, MENSAJE, FECHA, ESTADO) 
         VALUES (:senderId, :recipientId, :message, SYSDATE, 'enviado')`,
        { senderId, recipientId, message },
        { autoCommit: true }
      );

      // Emitir el mensaje de vuelta al remitente
      socket.emit('chat message', message, senderId, new Date());

      // Enviar el mensaje al destinatario si está conectado
      const recipientSocket = Array.from(io.sockets.sockets.values()).find(
        (s) => s.userId === recipientId
      );
      if (recipientSocket) {
        recipientSocket.emit('chat message', message, senderId, new Date());
      }

      // Actualizar la lista de chats recientes para ambos usuarios
      socket.emit('update chat list', recipientId);
      if (recipientSocket) {
        recipientSocket.emit('update chat list', senderId);
      }
    } catch (e) {
      console.error('Error al enviar mensaje:', e);
    }
  });

  socket.on('disconnect', () => {
    console.log(`Usuario desconectado: ${socket.userId}`);
  });
});




app.get('/', (req, res) => {
  res.redirect('/login.html');
});

app.use(express.static(path.join(process.cwd(), 'client')));

server.listen(port, () => {
  console.log(`Servidor en ejecución en el puerto ${port}`);
});
