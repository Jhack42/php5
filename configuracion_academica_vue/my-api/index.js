const express = require('express');
const cors = require('cors');
const axios = require('axios'); // Para hacer peticiones a otras APIs
const app = express();
const port = 3000;

// Habilitar CORS
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

// API para el <div>
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

// Función para crear el HTML desde JSON
async function buildHtmlFromJson(apiUrl) {
    try {
        const response = await axios.get(apiUrl);
        const data = response.data;

        let html = await createElementFromJson(data);

        return `<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Proyecto ${data.projectId}</title>
            <style>
                body { font-family: Arial, sans-serif; }
            </style>
        </head>
        <body>
            ${html}
        </body>
        </html>`;
    } catch (error) {
        console.error(error);
        return '<p>Error al generar el HTML.</p>';
    }
}

// Función para construir el HTML de cada elemento
async function createElementFromJson(json) {
    let html = `<${json.tag}`;

    if (json.attributes) {
        for (const [key, value] of Object.entries(json.attributes)) {
            html += ` ${key}="${value}"`;
        }
    }

    html += '>';

    if (json.content) {
        html += json.content;
    }

    if (json.children) {
        for (const child of json.children) {
            html += await createElementFromJson(child);
        }
    }

    html += `</${json.tag}>`;

    if (json.api_css) {
        const css = await applyCssFromApi(json.api_css);
        html = `<style>${css}</style>` + html;
    }

    return html;
}

// Función para obtener el CSS desde la API
async function applyCssFromApi(apiCssUrl) {
    try {
        const response = await axios.get(`${BASE_URL}${apiCssUrl}`);
        const cssData = response.data?.styles;
        
        if (!cssData || typeof cssData !== 'object') {
            console.error('Formato de CSS inválido recibido de la API');
            return '';
        }

        return Object.entries(cssData)
            .map(([property, value]) => `${property}: ${value};`)
            .join(' ');
    } catch (error) {
        console.error('Error al obtener CSS de la API:', error.message);
        return '';
    }
}

// Nueva API que devuelve el HTML construido
app.get('/api_html/proyecto/:id', async (req, res) => {
    const projectId = req.params.id;
    const apiUrl = `http://127.0.0.1:3000/api/proyecto/${projectId}`;
    const html = await buildHtmlFromJson(apiUrl);
    res.send(html);
});

// Iniciar el servidor
app.listen(port, () => {
    console.log(`Servidor corriendo en http://localhost:${port}`);
});

const BASE_URL = process.env.BASE_URL || 'http://127.0.0.1:3000';
