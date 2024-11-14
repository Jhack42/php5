"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const cors_1 = __importDefault(require("cors")); // Importa cors
const app = (0, express_1.default)();
const port = 3000;
// Habilitar CORS para todas las solicitudes
app.use((0, cors_1.default)());
// Nueva API que devuelve la estructura jerárquica de facultades, especialidades y eventos
app.get('/api/arbol', (req, res) => {
    const data = {
        name: "Universidad",
        evento: ["eventos"],
        facultades: [
            {
                name: "Facultad 1",
                evento: ["eventos"],
                carpeta: [
                    {
                        name: "Especialidad 0",
                        evento: ["eventos"]
                    },
                    {
                        name: "Especialidad 1",
                        evento: ["eventos"]
                    }
                ]
            },
            {
                name: "Facultad 2",
                evento: ["eventos"],
                carpeta: [
                    {
                        name: "Especialidad 0",
                        evento: ["eventos"]
                    },
                    {
                        name: "Especialidad 1",
                        evento: ["eventos"]
                    }
                ]
            }
        ]
    };
    res.json(data);
});
// Iniciar el servidor
app.listen(port, () => {
    console.log(`Servidor corriendo en http://localhost:${port}`);
});
