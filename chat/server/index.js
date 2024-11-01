import express from 'express';
import logger from 'morgan';
import { Server } from 'socket.io';
import { createServer } from 'node:http';
import path from 'path';
import oracledb from 'oracledb';
import dotenv from 'dotenv';

dotenv.config(); // Cargar variables de entorno desde .env

const port = process.env.PORT ?? 3000;

const app = express();
const server = createServer(app);
const io = new Server(server, {
  connectionStateRecovery: {
    maxDisconnectionDuration: 2 * 60 * 1000,
    useFindBestOffset: true,
  },
});

// Conectar a Oracle
async function connectToDB() {
  try {
    const connection = await oracledb.getConnection({
        user: process.env.DATABASE_URL.split(':')[1].split('//')[1], // Extraer el usuario
        password: process.env.DATABASE_URL.split(':')[2].split('@')[0], 
        connectString: process.env.DATABASE_URL.split('@')[1],
    });
    console.log('Conectado a la base de datos Oracle');
    return connection;
  } catch (err) {
    console.error('Error al conectar a la base de datos:', err);
  }
}

// Almacenar mensajes en memoria
let messages = [];

io.on('connection', async (socket) => {
  console.log('¡Un usuario se ha conectado!');

  // Enviar historial de mensajes al cliente recién conectado
  socket.emit('chat-history', messages);

  socket.on('disconnect', () => {
    console.log('Un usuario se ha desconectado');
  });

  // Escuchar nuevos mensajes
  socket.on('chat message', async (msg) => {
    const username = socket.handshake.auth.username ?? 'anonymous';
    console.log({ username });

    // Guardar mensaje en memoria
    const newMessage = { content: msg, user: username };
    messages.push(newMessage);

    // Insertar mensaje en la base de datos Oracle
    try {
      const connection = await connectToDB();
      await connection.execute(
        'INSERT INTO messages ( content, usuario) VALUES ( :msg, :username)',
        
        { msg, username },
        { autoCommit: true }
      );
      console.log('Mensaje almacenado en la base de datos');
    } catch (e) {
      console.error('Error al insertar en la base de datos:', e);
    }

    // Emitir el mensaje a todos los clientes
    io.emit('chat message', msg, username);
  });

  if (!socket.recovered) {
    // Recuperar mensajes de la base de datos
    try {
      const connection = await connectToDB();
      const result = await connection.execute(
        `SELECT content, usuario FROM messages`
      );
      result.rows.forEach((row) => {
        socket.emit('chat message', row[0], row[1]);
      });
    } catch (e) {
      console.error('Error al recuperar mensajes:', e);
    }
  }
});

app.use(logger('dev'));

app.get('/', (req, res) => {
  res.sendFile(path.join(process.cwd(), 'client', 'index.html'));
});

server.listen(port, () => {
  console.log(`Servidor en ejecución en el puerto ${port}`);
});
