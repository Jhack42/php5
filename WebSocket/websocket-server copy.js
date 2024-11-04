const WebSocket = require("ws");

const wss = new WebSocket.Server({ port: 8080 });
const clients = new Map(); // Mapa para guardar usuarios y sus conexiones WebSocket

wss.on("connection", (ws) => {
  ws.on("message", (message) => {
    const data = JSON.parse(message);

    if (data.type === "login") {
      // Almacenar la conexión con el userId enviado por el cliente
      clients.set(data.userId, ws);
      ws.userId = data.userId;
      console.log(`Usuario conectado: ${data.userId}`);
    } else if (data.type === "offer" || data.type === "answer" || data.type === "candidate") {
      // Enviar mensajes de WebRTC al destinatario especificado
      const targetUser = clients.get(data.target);
      if (targetUser) {
        targetUser.send(JSON.stringify(data));
      }
    } else if (data.type === "message") {
      // Reenviar mensajes de chat a todos los usuarios excepto al remitente
      clients.forEach((client) => {
        if (client !== ws) {
          client.send(JSON.stringify({ userId: ws.userId, message: data.message }));
        }
      });
    }
  });

  // Eliminar el usuario cuando se desconecta
  ws.on("close", () => {
    clients.delete(ws.userId);
    console.log(`Usuario desconectado: ${ws.userId}`);
  });
});

console.log("Servidor WebSocket escuchando en ws://localhost:8080");
