// server/socketHandlers.js
import { connectToDB } from './db.js';
import oracledb from 'oracledb';

export default function handleSocketConnection(io) {
  io.on('connection', (socket) => {
    const userId = socket.handshake.auth.userId;
    
    if (!userId) {
      console.log("Error: Usuario no autenticado. Desconectando...");
      socket.disconnect();
      return;
    }
    
    console.log(`Usuario conectado: ${userId}`);

    // Cargar historial de chat
    socket.on('loadChat', async (recipientId) => {
      console.log(`Cargando historial de chat entre usuario ${userId} y ${recipientId}`);
      try {
        const connection = await connectToDB();
        const chatId = await getOrCreateChatId(connection, userId, recipientId);

        const result = await connection.execute(
          `SELECT MENSAJE, USUARIO_ID, FECHA 
           FROM MENSAJES 
           WHERE CHAT_ID = :chatId
           ORDER BY FECHA ASC`,
          { chatId }
        );

        const messages = result.rows.map(row => ({
          content: row[0],
          senderId: row[1],
          timestamp: row[2]
        }));

        console.log("Historial de mensajes enviado:", messages);
        socket.emit('chat history', messages);
      } catch (error) {
        console.error('Error al cargar historial de chat:', error);
      }
    });

    // Manejar el envío de un mensaje
    socket.on('chat message', async ({ content, recipientId }) => {
      console.log(`Enviando mensaje de ${userId} a ${recipientId}: ${content}`);
      try {
        const connection = await connectToDB();
        const chatId = await getOrCreateChatId(connection, userId, recipientId);

        await connection.execute(
          `INSERT INTO MENSAJES (CHAT_ID, USUARIO_ID, DESTINATARIO_ID, MENSAJE, FECHA, ESTADO)
           VALUES (:chatId, :userId, :recipientId, :content, SYSDATE, 'enviado')`,
          { chatId, userId, recipientId, content },
          { autoCommit: true }
        );

        socket.emit('chat message', content, userId, new Date());
        const recipientSocket = Array.from(io.sockets.sockets.values()).find(s => s.handshake.auth.userId == recipientId);
        if (recipientSocket) {
          recipientSocket.emit('chat message', content, userId, new Date());
        }

        io.to([socket.id, recipientSocket?.id]).emit('update chat list');
      } catch (error) {
        console.error('Error al enviar mensaje:', error);
      }
    });
  });
}

async function getOrCreateChatId(connection, userId, recipientId) {
  const result = await connection.execute(
    `SELECT ID FROM CHATS WHERE TIPO = 'individual' 
     AND EXISTS (SELECT 1 FROM MIEMBROS_CHAT WHERE CHAT_ID = CHATS.ID AND USUARIO_ID = :userId)
     AND EXISTS (SELECT 1 FROM MIEMBROS_CHAT WHERE CHAT_ID = CHATS.ID AND USUARIO_ID = :recipientId)`,
    { userId, recipientId }
  );

  if (result.rows.length > 0) {
    console.log(`Chat existente encontrado: ${result.rows[0][0]}`);
    return result.rows[0][0];
  } else {
    const newChat = await connection.execute(
      `INSERT INTO CHATS (TIPO, NOMBRE, CREADOR_ID) VALUES ('individual', NULL, :userId) RETURNING ID INTO :chatId`,
      { userId, chatId: { type: oracledb.NUMBER, dir: oracledb.BIND_OUT } },
      { autoCommit: true }
    );
    const chatId = newChat.outBinds.chatId[0];
    console.log(`Nuevo chat creado con ID: ${chatId}`);

    await connection.execute(
      `INSERT INTO MIEMBROS_CHAT (CHAT_ID, USUARIO_ID) VALUES (:chatId, :userId), (:chatId, :recipientId)`,
      { chatId, userId, recipientId },
      { autoCommit: true }
    );

    return chatId;
  }
}
