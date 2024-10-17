const express = require('express');
const cors = require('cors'); // Importa cors
const app = express();
const port = 3000;

// Habilitar CORS para todas las solicitudes
app.use(cors());

// Ruta raíz que muestra un mensaje
app.get('/', (req, res) => {
    res.send('Bienvenido a mi API! Prueba accediendo a /api/proyecto/:id para ver los diferentes proyectos.');
});

// API inicial que devuelve la estructura base del HTML en JSON para un proyecto específico
app.get('/api/proyecto/:id', (req, res) => {
    const projectId = req.params.id;
    
    // Estructura diferente dependiendo del ID del proyecto
    let htmlStructure;

    if (projectId === '1') {
        htmlStructure = {
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
    } else if (projectId === '2') {
        // Estructura diferente para el proyecto 2
        htmlStructure = {
            tag: 'body',
            children: [
                {
                    tag: 'h1',
                    api: `/api/h1/${projectId}`,
                    api_css: `/api/css/h1-${projectId}`
                },
                {
                    tag: 'section',
                    api: `/api/section/${projectId}`,
                    api_css: `/api/css/section-${projectId}`
                },
                {
                    tag: 'footer',
                    api: `/api/footer/${projectId}`,
                    api_css: `/api/css/footer-${projectId}`
                }
            ]
        };
    } else {
        res.status(404).json({ error: 'Proyecto no encontrado' });
        return;
    }

    res.json(htmlStructure);
});

// API para el <h1> para ambos proyectos
app.get('/api/h1/:projectId', (req, res) => {
    const projectId = req.params.projectId;

    const title = projectId === '1'
        ? 'Título del proyecto 1'
        : 'Título del proyecto 2';

    res.json({
        tag: 'h1',
        content: title
    });
});

// API para el <div> del proyecto 1
app.get('/api/div/:projectId', (req, res) => {
    const projectId = req.params.projectId;

    if (projectId === '1') {
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
    } else {
        res.status(404).json({ error: 'Div no disponible para este proyecto' });
    }
});

// API para el <section> del proyecto 2
app.get('/api/section/:projectId', (req, res) => {
    const projectId = req.params.projectId;

    if (projectId === '2') {
        res.json({
            tag: 'section',
            children: [
                {
                    tag: 'p',
                    content: `Este es el contenido de la sección del proyecto ${projectId}`
                },
                {
                    tag: 'img',
                    attributes: {
                        src: 'https://via.placeholder.com/150',
                        alt: 'Imagen de ejemplo'
                    }
                }
            ]
        });
    } else {
        res.status(404).json({ error: 'Sección no disponible para este proyecto' });
    }
});

// API para el <footer> para ambos proyectos
app.get('/api/footer/:projectId', (req, res) => {
    const projectId = req.params.projectId;

    res.json({
        tag: 'footer',
        content: `Este es el pie de página del proyecto ${projectId}`
    });
});

// API para el CSS del <h1> para ambos proyectos
app.get('/api/css/h1-:projectId', (req, res) => {
    const projectId = req.params.projectId;

    const styles = projectId === '1'
        ? { color: 'blue', 'font-size': '24px', 'text-align': 'center' }
        : { color: 'green', 'font-size': '28px', 'text-align': 'left' };

    res.json({ styles });
});

// API para el CSS del <div> del proyecto 1
app.get('/api/css/div-:projectId', (req, res) => {
    if (req.params.projectId === '1') {
        res.json({
            styles: {
                'background-color': 'lightgrey',
                padding: '10px',
                'border-radius': '5px'
            }
        });
    } else {
        res.status(404).json({ error: 'CSS no disponible para este proyecto' });
    }
});

// API para el CSS del <section> del proyecto 2
app.get('/api/css/section-:projectId', (req, res) => {
    if (req.params.projectId === '2') {
        res.json({
            styles: {
                'background-color': 'lightblue',
                padding: '15px',
                'border': '2px solid navy'
            }
        });
    } else {
        res.status(404).json({ error: 'CSS no disponible para este proyecto' });
    }
});

// API para el CSS del <footer> para ambos proyectos
app.get('/api/css/footer-:projectId', (req, res) => {
    const projectId = req.params.projectId;

    const styles = projectId === '1'
        ? { 'background-color': 'darkgrey', color: 'white', padding: '20px', 'text-align': 'center' }
        : { 'background-color': 'black', color: 'yellow', padding: '25px', 'text-align': 'right' };

    res.json({ styles });
});

// Iniciar el servidor
app.listen(port, () => {
    console.log(`Servidor corriendo en http://localhost:${port}`);
});
