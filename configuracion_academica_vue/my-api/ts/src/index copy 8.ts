import express from 'express';
import logger from 'morgan';
import { Server } from 'socket.io';
import { createServer } from 'node:http';
import path from 'path';
import oracledb from 'oracledb';
import dotenv from 'dotenv';

dotenv.config();

const app = express();
const port = 3000;
const server = createServer(app);
const io = new Server(server, {
  connectionStateRecovery: {
    maxDisconnectionDuration: 2 * 60 * 1000,
  },
});


oracledb.outFormat = oracledb.OUT_FORMAT_OBJECT;

// Configuración de conexión a OracleDB
const dbConfig = {
  user: 'chat',
  password: 'chat',
  connectString: 'localhost:1521/XEPDB1',
};

// Conectar a la base de datos
async function connectToDB() {
  try {
    const connection = await oracledb.getConnection(dbConfig);
    console.log('Conectado a la base de datos Oracle');
    return connection;
  } catch (err) {
    console.error('Error al conectar a la base de datos:', err);
    throw err;
  }
}

// Funciones de chat
async function createChatIndividual(usuarioId1: number, usuarioId2: number) {
  const connection = await connectToDB();
  await connection.execute(
    `INSERT INTO chats (tipo) VALUES ('individual') RETURNING id INTO :id`,
    { id: { dir: oracledb.BIND_OUT, type: oracledb.NUMBER } }
  );
  const chatId = connection.lastRowid;

  // Añadir ambos usuarios al chat
  await connection.execute(
    `INSERT INTO miembros_chat (chat_id, usuario_id) VALUES (:chatId, :usuarioId1), (:chatId, :usuarioId2)`,
    { chatId, usuarioId1, usuarioId2 }
  );
  await connection.close();
  return chatId;
}

async function createChatGrupal(nombre: string, creadorId: number, usuarioIds: number[]) {
  const connection = await connectToDB();
  const result = await connection.execute(
    `INSERT INTO chats (tipo, nombre, creador_id) VALUES ('grupal', :nombre, :creadorId) RETURNING id INTO :id`,
    { nombre, creadorId, id: { dir: oracledb.BIND_OUT, type: oracledb.NUMBER } }
  );
  const chatId = result.outBinds.id[0];

  // Añadir el creador y miembros al chat
  for (const usuarioId of usuarioIds) {
    await connection.execute(
      `INSERT INTO miembros_chat (chat_id, usuario_id, estado_invitacion) VALUES (:chatId, :usuarioId, 'pendiente')`,
      { chatId, usuarioId }
    );
  }
  await connection.close();
  return chatId;
}

// Socket.IO para la gestión de eventos
io.on('connection', (socket) => {
  console.log('Usuario conectado:', socket.id);

  socket.on('crearChatGrupal', async ({ nombre, creadorId, usuarioIds }) => {
    const chatId = await createChatGrupal(nombre, creadorId, usuarioIds);
    io.to(socket.id).emit('chatCreado', { chatId, tipo: 'grupal' });
  });

  socket.on('mensaje', async ({ chatId, usuarioId, mensaje }) => {
    const connection = await connectToDB();
    await connection.execute(
      `INSERT INTO mensajes (chat_id, usuario_id, mensaje, estado) VALUES (:chatId, :usuarioId, :mensaje, 'enviado')`,
      { chatId, usuarioId, mensaje }
    );
    await connection.close();

    io.to(chatId.toString()).emit('nuevoMensaje', { chatId, usuarioId, mensaje });
  });

  socket.on('aceptarInvitacion', async ({ chatId, usuarioId }) => {
    const connection = await connectToDB();
    await connection.execute(
      `UPDATE miembros_chat SET estado_invitacion = 'aceptado' WHERE chat_id = :chatId AND usuario_id = :usuarioId`,
      { chatId, usuarioId }
    );
    await connection.close();
    io.to(chatId.toString()).emit('mensaje', {
      mensaje: `El usuario ${usuarioId} ha aceptado la invitación.`,
    });
  });
});

app.use(logger('dev'));

// Servir el archivo HTML
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});

// Iniciar el servidor
server.listen(port, () => {
  console.log(`Servidor en ejecución en el puerto ${port}`);
});
