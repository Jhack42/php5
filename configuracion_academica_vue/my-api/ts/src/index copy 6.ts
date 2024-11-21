import express from 'express';
import cors from 'cors';
import axios from 'axios';
import { WebSocketServer } from 'ws';

const app = express();
const PORT = 3000;

app.use(cors());
app.use(express.json());

// Inicializar el servidor WebSocket
const wss = new WebSocketServer({ port: 8080 });

// Configuración del WebSocket
wss.on('connection', (ws) => {
  console.log('Cliente conectado a WebSocket');

  ws.on('message', async (message) => {
    const id = message.toString(); // Asumimos que el cliente envía el ID como mensaje

    try {
      // Realiza la solicitud a la API objetivo
      const response = await axios.post(`http://localhost:${PORT}/api/otro-endpoint/${id}`, {
        data: { mensaje: 'La API puente ha sido llamada a través de WebSocket' }
      });

      // Envía la respuesta al cliente WebSocket
      ws.send(JSON.stringify(response.data));
    } catch (error) {
      console.error('Error al redirigir la solicitud:', error);
      ws.send(JSON.stringify({ error: 'Error al redirigir la solicitud' }));
    }
  });

  ws.on('close', () => {
    console.log('Cliente desconectado de WebSocket');
  });
});
