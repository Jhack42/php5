"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const path_1 = __importDefault(require("path"));
// Inicializa la aplicación de Express
const app = (0, express_1.default)();
// Puerto donde se ejecutará el servidor
const PORT = 3000;
// Middleware para servir archivos estáticos (HTML, CSS, JS)
app.use(express_1.default.static(path_1.default.join(__dirname, 'public')));
// Ruta para la API que devuelve datos simulados
app.get('/api/data', (req, res) => {
    const data = [
        {
            id: 1,
            name: "Actividades",
            image: "https://i.postimg.cc/zBbGtfPT/eventos.png",
            href: "/vista2"
        },
        {
            id: 2,
            name: "Notificaciones",
            image: "https://i.postimg.cc/s21jxXzb/Notificaciones.jpg",
            href: "/"
        }
    ];
    res.json(data);
});
// ** Ruta que causa el problema **
app.get('/api/logic/calculate/:value', (req, res) => {
    const value = req.params.value;
    // Realiza cualquier lógica que necesites con el valor
    res.json({ result: `El valor calculado es ${value}` });
});
// Ruta para cargar la vista principal (index.html)
app.get('/', (req, res) => {
    res.sendFile(path_1.default.join(__dirname, 'public', 'index.html'));
});
// Ruta para cargar la vista 2 (vista2.html)
app.get('/vista2', (req, res) => {
    res.sendFile(path_1.default.join(__dirname, 'public', 'vista2.html'));
});
// Inicia el servidor
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
