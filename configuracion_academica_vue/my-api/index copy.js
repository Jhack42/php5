const express = require('express');
const app = express();
const port = 3000;

// Ruta principal que devuelve solo el body
app.get('/', (req, res) => {
    const htmlContent = `
        <body>
            <h1>Este es el contenido dentro del body</h1>
            <p>¡Bienvenido a mi API!</p>
        </body>
    `;
    res.send(htmlContent);  // Responde con el código HTML
});

// Iniciar el servidor
app.listen(port, () => {
    console.log(`Servidor escuchando en http://localhost:${port}`);
});
