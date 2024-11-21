const express = require('express');
const cors = require('cors'); // Importa cors
const app = express();
const port = 3000;

// Habilitar CORS para todas las solicitudes
app.use(cors());

// Ruta raíz que muestra un mensaje o página básica
app.get('/', (req, res) => {
    res.send('Bienvenido a mi API! Prueba accediendo a /api/proyecto/:id');
});

// API inicial que devuelve la estructura base del HTML en JSON
app.get('/api/proyecto/:id', (req, res) => {
    const projectId = req.params.id;

    const htmlStructure = {
        tag: 'body',
        children: [
            {
                tag: 'h1',
                api: `/api/h1/${projectId}`,
                api_css: `/api/css/h1-${projectId}`
            },
            {
                tag: 'div',
                api: `/api/div/${projectId}`,
                api_css: `/api/css/div-${projectId}`
            },
            {
                tag: 'footer',
                api: `/api/footer/${projectId}`,
                api_css: `/api/css/footer-${projectId}`
            }
        ]
    };

    res.json(htmlStructure);
});

// Inicia el servidor
app.listen(port, () => {
    console.log(`Servidor corriendo en http://localhost:${port}`);
});
