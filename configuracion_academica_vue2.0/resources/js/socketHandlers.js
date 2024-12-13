// socketHandlers.js
import { Server } from 'socket.io';

export default function createCarouselSocketHandler(httpServer) {
    const io = new Server(httpServer, {
        cors: {
            origin: process.env.FRONTEND_URL || "http://localhost:5173",
            methods: ["GET", "POST", "PUT", "DELETE"],
            credentials: true,
            allowedHeaders: ["Content-Type", "Authorization"]
        },
        pingTimeout: 10000,
        pingInterval: 5000,
        transports: ['websocket', 'polling']
    });

    // Middleware de autenticación si es necesario
    io.use((socket, next) => {
        const appName = socket.handshake.query.appName;
        if (appName) {
            socket.appName = appName;
            next();
        } else {
            next(new Error('Autenticación requerida'));
        }
    });

    const carouselNamespace = io.of('/carousel');

    carouselNamespace.on('connection', (socket) => {
        console.log('Cliente conectado al carrusel:', socket.id, 'App:', socket.appName);

        try {
            // Unirse a sala específica
            socket.join('carousel-updates');

            // Manejadores de eventos mejorados con try-catch
            socket.on('carousel:update', async (data) => {
                try {
                    await validateData(data);
                    socket.to('carousel-updates').emit('carousel:updated', data);
                } catch (error) {
                    socket.emit('carousel:error', {
                        message: 'Error al procesar actualización',
                        error: error.message
                    });
                }
            });

            socket.on('carousel:delete', async (itemId) => {
                try {
                    if (!itemId) throw new Error('ID requerido');
                    socket.to('carousel-updates').emit('carousel:deleted', itemId);
                } catch (error) {
                    socket.emit('carousel:error', {
                        message: 'Error al eliminar item',
                        error: error.message
                    });
                }
            });

            socket.on('carousel:reorder', async (newOrder) => {
                try {
                    await validateOrder(newOrder);
                    socket.to('carousel-updates').emit('carousel:reordered', newOrder);
                } catch (error) {
                    socket.emit('carousel:error', {
                        message: 'Error al reordenar',
                        error: error.message
                    });
                }
            });

            socket.on('error', (error) => {
                console.error('Error en socket:', socket.id, error);
            });

            socket.on('disconnect', (reason) => {
                console.log('Cliente desconectado:', socket.id, 'Razón:', reason);
                socket.leave('carousel-updates');
            });

        } catch (error) {
            console.error('Error en configuración de socket:', error);
            socket.disconnect(true);
        }
    });

    return carouselNamespace;
}

// Funciones auxiliares de validación
async function validateData(data) {
    if (!data) throw new Error('Datos inválidos');
    // Implementar validaciones específicas
}

async function validateOrder(order) {
    if (!Array.isArray(order)) throw new Error('Orden debe ser un array');
    // Implementar validaciones específicas
}
