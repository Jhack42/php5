const WebSocket = require('ws');

const server = new WebSocket.Server({ port: 8080 });

server.on('connection', (ws) => {
    console.log('Cliente conectado');

    // Enviar un cuerpo HTML básico al cliente
    ws.send(`
        <body style="background-color:lightblue; font-family:Arial, sans-serif; color:#333;">
            <h1>Bienvenido desde WebSocket</h1>
            <p>Este es un <code>&lt;body&gt;</code> enviado desde un servidor WebSocket.</p>
        </body>
    `);

    ws.on('close', () => {
        console.log('Cliente desconectado');
    });
});
