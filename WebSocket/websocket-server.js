import { Server } from "socket.io";
import oracledb from "oracledb";
import dotenv from "dotenv";

dotenv.config();

const io = new Server(3001, {
  cors: {
    origin: "http://localhost:3000", // Cambia esto si Next.js está en otro puerto
    methods: ["GET", "POST"],
  },
  connectionStateRecovery: {
    maxDisconnectionDuration: 2 * 60 * 1000,
    useFindBestOffset: true,
  },
});

// Conectar a Oracle
let dbConnection;
async function connectToDB() {
  if (dbConnection) return dbConnection;

  try {
    dbConnection = await oracledb.getConnection({
      user: process.env.DATABASE_USER,
      password: process.env.DATABASE_PASSWORD,
      connectString: process.env.DATABASE_CONNECTION_STRING,
    });
    console.log("Conectado a la base de datos Oracle");
    return dbConnection;
  } catch (err) {
    console.error("Error al conectar a la base de datos:", err);
  }
}

// Middleware de autenticación para Socket.IO
io.use((socket, next) => {
  const userId = socket.handshake.auth.userId;
  if (userId) {
    socket.userId = userId;
    next();
  } else {
    next(new Error("No autenticado"));
  }
});

io.on("connection", (socket) => {
  console.log(`Usuario conectado: ${socket.userId}`);

  socket.on("loadChat", async (recipientId) => {
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

      const messages = result.rows.map((row) => ({
        content: row[0],
        senderId: row[1],
        timestamp: row[2],
      }));

      socket.emit("chat history", messages);
    } catch (e) {
      console.error("Error al cargar el historial de mensajes:", e);
    }
  });

  socket.on("chat message", async ({ content, recipientId }) => {
    const senderId = socket.userId;
    try {
      const connection = await connectToDB();
      await connection.execute(
        `INSERT INTO MENSAJES (USUARIO_ID, DESTINATARIO_ID, MENSAJE, FECHA, ESTADO) 
         VALUES (:senderId, :recipientId, :content, SYSDATE, 'enviado')`,
        { senderId, recipientId, content },
        { autoCommit: true }
      );

      socket.emit("chat message", content, senderId, new Date());
      const recipientSocket = Array.from(io.sockets.sockets.values()).find(
        (s) => s.userId === recipientId
      );
      if (recipientSocket) {
        recipientSocket.emit("chat message", content, senderId, new Date());
      }
    } catch (e) {
      console.error("Error al enviar mensaje:", e);
    }
  });

  socket.on("disconnect", () => {
    console.log(`Usuario desconectado: ${socket.userId}`);
  });
});

console.log("Servidor WebSocket en ejecución en el puerto 3001");
