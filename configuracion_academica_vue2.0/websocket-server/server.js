const express = require('express');
const { createServer } = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

// Crear app Express
const app = express();

// Configurar CORS para Express
app.use(cors({
    origin: [
        'http://localhost:5173',
        'http://127.0.0.1:8000',
        'http://localhost:8000'
    ],
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    credentials: true
}));

// Crear servidor HTTP
const httpServer = createServer(app);

// Configurar Socket.IO con opciones mejoradas
const io = new Server(httpServer, {
    cors: {
        origin: [
            'http://localhost:5173',
            'http://127.0.0.1:8000',
            'http://localhost:8000'
        ],
        methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        credentials: true,
        allowedHeaders: ["Content-Type", "Authorization"]
    },
    allowEIO3: true,
    transports: ['websocket', 'polling'],
    pingTimeout: 60000,
    pingInterval: 25000
});

// Estado para mantener registro de clientes conectados y ediciones activas
const connectedClients = new Set();
const activeEdits = new Map(); // Nuevo: Rastrea ediciones activas

// Manejo de conexiones Socket.IO
io.on('connection', (socket) => {
    console.log('Cliente conectado:', socket.id);
    connectedClients.add(socket.id);

    // Eventos existentes para el carrusel
    socket.on('carousel:update', (data) => {
        console.log('Actualizando carrusel:', data);
        socket.broadcast.emit('carousel:updated', data);
    });

    socket.on('carousel:delete', (id) => {
        console.log('Eliminando item:', id);
        socket.broadcast.emit('carousel:deleted', id);
    });

    socket.on('carousel:reorder', (data) => {
        console.log('Reordenando items:', data);
        socket.broadcast.emit('carousel:reordered', data);
    });

    // Nuevos eventos para edición colaborativa
    socket.on('carousel:editing:start', (data) => {
        console.log('Iniciando edición:', data);
        activeEdits.set(data.itemId, {
            userId: data.userId,
            timestamp: Date.now()
        });
        io.emit('carousel:editing:started', data);
    });

    socket.on('carousel:editing:end', (data) => {
        console.log('Finalizando edición:', data);
        activeEdits.delete(data.itemId);
        io.emit('carousel:editing:ended', data);
    });

    socket.on('carousel:editing:update', (data) => {
        console.log('Actualización colaborativa:', data);
        io.emit('carousel:editing:updated', data);
    });

    // Manejo de errores
    socket.on('error', (error) => {
        console.error('Error en socket:', socket.id, error);
    });

    // Manejo de desconexión
    socket.on('disconnect', () => {
        console.log('Cliente desconectado:', socket.id);
        // Limpiar ediciones activas del usuario desconectado
        for (const [itemId, edit] of activeEdits.entries()) {
            if (edit.userId === socket.id) {
                activeEdits.delete(itemId);
                io.emit('carousel:editing:ended', {
                    itemId,
                    userId: socket.id
                });
            }
        }
        connectedClients.delete(socket.id);
    });
});

// Ruta de estado/health check actualizada
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        connections: connectedClients.size,
        activeEdits: activeEdits.size,
        uptime: process.uptime()
    });
});

// Manejo de errores para Express
app.use((err, req, res, next) => {
    console.error('Error en servidor:', err);
    res.status(500).json({
        error: 'Error interno del servidor',
        message: err.message
    });
});

// Puerto de escucha
const PORT = process.env.PORT || 3000;

// Iniciar servidor
httpServer.listen(PORT, () => {
    console.log(`Servidor WebSocket corriendo en puerto ${PORT}`);
    console.log('Orígenes permitidos:', [
        'http://localhost:5173',
        'http://127.0.0.1:8000',
        'http://localhost:8000'
    ]);
});

// Manejo de errores no capturados
process.on('uncaughtException', (err) => {
    console.error('Error no capturado:', err);
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('Promesa rechazada no manejada:', reason);
});
