"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const cors_1 = __importDefault(require("cors"));
const axios_1 = __importDefault(require("axios")); // Para hacer peticiones a otras APIs
// Crear la aplicación Express
const app = (0, express_1.default)();
const port = 3000;
const BASE_URL = process.env.BASE_URL || "http://127.0.0.1:3000";
// Habilitar CORS
app.use((0, cors_1.default)());
// API inicial que devuelve la estructura base del HTML en JSON
app.get("/api/proyecto/:id", (req, res) => {
    const projectId = req.params.id;
    const htmlStructure = {
        tag: "body",
        children: [
            {
                tag: "h1",
                api_css: `/api/css/h1-${projectId}`,
                content: `Título del proyecto ${projectId}`,
            },
            {
                tag: "div",
                api_css: `/api/css/div-${projectId}`,
                children: [
                    { tag: "p", content: `Descripción del proyecto ${projectId}` },
                    {
                        tag: "a",
                        attributes: { href: "https://www.youtube.com/watch?v=TyGGNK1xWn8" },
                        content: "Enlace a más detalles",
                    },
                ],
            },
            {
                tag: "footer",
                api_css: `/api/css/footer-${projectId}`,
                content: `Este es el pie de página del proyecto ${projectId}`,
            },
        ],
    };
    res.json(htmlStructure);
});
// API para el <h1>
app.get("/api/h1/:projectId", (req, res) => {
    const projectId = req.params.projectId;
    res.json({
        tag: "h1",
        content: `Título del proyecto ${projectId}`,
    });
});
// API para el <div>
app.get("/api/div/:projectId", (req, res) => {
    const projectId = req.params.projectId;
    res.json({
        tag: "div",
        children: [
            { tag: "p", content: `Descripción del proyecto ${projectId}` },
            {
                tag: "a",
                attributes: { href: "https://www.youtube.com/watch?v=TyGGNK1xWn8" },
                content: "Enlace a más detalles",
            },
        ],
    });
});
// API para el <footer>
app.get("/api/footer/:projectId", (req, res) => {
    const projectId = req.params.projectId;
    res.json({
        tag: "footer",
        content: `Este es el pie de página del proyecto ${projectId}`,
    });
});
// API para el CSS del <h1>
app.get("/api/css/h1-:projectId", (req, res) => {
    res.json({
        styles: {
            color: "blue",
            "font-size": "24px",
            "text-align": "center",
        },
    });
});
// API para el CSS del <div>
app.get("/api/css/div-:projectId", (req, res) => {
    res.json({
        styles: {
            "background-color": "lightgrey",
            padding: "10px",
            "border-radius": "5px",
        },
    });
});
// API para el CSS del <footer>
app.get("/api/css/footer-:projectId", (req, res) => {
    res.json({
        styles: {
            "background-color": "darkgrey",
            color: "white",
            padding: "20px",
            "text-align": "center",
        },
    });
});
// Función para crear el HTML desde JSON
async function buildHtmlFromJson(apiUrl) {
    try {
        const response = await axios_1.default.get(apiUrl);
        const data = response.data;
        let html = await createElementFromJson(data);
        return `<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Proyecto ${data.tag}</title>
        <style>
            body { font-family: Arial, sans-serif; }
        </style>
    </head>
    <body>
        ${html}
    </body>
    </html>`;
    }
    catch (error) {
        console.error(error);
        return "<p>Error al generar el HTML.</p>";
    }
}
// Función para construir el HTML de cada elemento
async function createElementFromJson(json) {
    let html = `<${json.tag}`;
    // Agregar atributos como clase, id, etc.
    if (json.attributes) {
        for (const [key, value] of Object.entries(json.attributes)) {
            html += ` ${key}="${value}"`;
        }
    }
    // Aplicar el CSS en línea si está disponible
    if (json.api_css) {
        const inlineCss = await applyCssFromApi(json.api_css);
        html += ` style="${inlineCss}"`; // Agregar el CSS como estilos en línea
    }
    html += '>';
    // Agregar contenido al elemento
    if (json.content) {
        html += json.content;
    }
    // Si tiene hijos, procesarlos recursivamente
    if (json.children) {
        for (const child of json.children) {
            html += await createElementFromJson(child);
        }
    }
    html += `</${json.tag}>`;
    return html;
}
// Función para obtener el CSS desde la API
async function applyCssFromApi(apiCssUrl) {
    try {
        const response = await axios_1.default.get(`${BASE_URL}${apiCssUrl}`);
        const cssData = response.data?.styles;
        if (!cssData || typeof cssData !== 'object') {
            console.error('Formato de CSS inválido recibido de la API');
            return '';
        }
        // Convertir los estilos a formato de string para estilos en línea
        return Object.entries(cssData)
            .map(([property, value]) => `${property}: ${value};`)
            .join(' ');
    }
    catch (error) {
        if (error instanceof Error) {
            console.error('Error al obtener CSS de la API:', error.message);
        }
        else {
            console.error('Error desconocido al obtener CSS de la API');
        }
        return '';
    }
}
// Nueva API que devuelve el HTML construido
app.get("/api_html/proyecto/:id", async (req, res) => {
    const projectId = req.params.id;
    const apiUrl = `http://127.0.0.1:3000/api/proyecto/${projectId}`;
    const html = await buildHtmlFromJson(apiUrl);
    res.send(html);
});
// Iniciar el servidor
app.listen(port, () => {
    console.log(`Servidor corriendo en http://localhost:${port}`);
});
