import express from 'express';
import { Request, Response } from 'express';
import cors from 'cors'; // Importa cors

const app = express();
const port = 3000;

// Habilitar CORS para todas las solicitudes
app.use(cors());
// Nueva API que devuelve la estructura jerárquica de facultades, especialidades y eventos
app.get('/api/arbol', (req: Request, res: Response) => {
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
