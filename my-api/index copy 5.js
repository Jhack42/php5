const express = require('express');
const cors = require('cors');
const app = express();
const port = 3000;

// Habilitar CORS para todas las solicitudes
app.use(cors());

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

// API para el <h1>
app.get('/api/h1/:projectId', (req, res) => {
    const projectId = req.params.projectId;

    res.json({
        tag: 'h1',
        content: `Título del proyecto ${projectId}`
    });
});

// API para un <div>
app.get('/api/div/:projectId', (req, res) => {
    const projectId = req.params.projectId;

    res.json({
        tag: 'div',
        children: [
            {
                tag: 'p',
                content: `Descripción del proyecto ${projectId}`
            },
            {
                tag: 'a',
                attributes: {
                    href: 'https://ejemplo.com'
                },
                content: 'Enlace a más detalles'
            }
        ]
    });
});

// API para el <footer>
app.get('/api/footer/:projectId', (req, res) => {
    const projectId = req.params.projectId;

    res.json({
        tag: 'footer',
        content: `Este es el pie de página del proyecto ${projectId}`
    });
});

// API para el CSS del <h1>
app.get('/api/css/h1-:projectId', (req, res) => {
    res.json({
        styles: {
            color: 'blue',
            'font-size': '24px',
            'text-align': 'center'
        }
    });
});

// API para el CSS del <div>
app.get('/api/css/div-:projectId', (req, res) => {
    res.json({
        styles: {
            'background-color': 'lightgrey',
            padding: '10px',
            'border-radius': '5px'
        }
    });
});

// API para el CSS del <footer>
app.get('/api/css/footer-:projectId', (req, res) => {
    res.json({
        styles: {
            'background-color': 'darkgrey',
            color: 'white',
            padding: '20px',
            'text-align': 'center'
        }
    });
});

app.listen(port, () => {
    console.log(`Servidor corriendo en http://localhost:${port}`);
});
