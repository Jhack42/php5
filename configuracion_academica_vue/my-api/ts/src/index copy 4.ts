import express from 'express';
import cors from 'cors';
import path from 'path';

// Inicializa la aplicación de Express
const app = express();

// Puerto donde se ejecutará el servidor
const PORT = 3000;

// Habilitar CORS para todas las solicitudes
app.use(cors());


// Middleware para servir archivos estáticos (HTML, CSS, JS)
app.use(express.static(path.join(__dirname, 'public')));

// Ruta para la API que devuelve datos simulados
app.get('/api/data', (req, res) => {
  const data = [
    {
      id: 1,
      name: "Actividades",
      image: "https://i.postimg.cc/zBbGtfPT/eventos.png",
      href: "/vista2" // Aquí añades la ruta para la vista 2
    },

    {
      id: 2,
      name: "Notificaciones",
      image: "https://i.postimg.cc/s21jxXzb/Notificaciones.jpg",
      href: "/"
    }
  ];
  res.json(data); // Devuelve los datos en formato JSON
});

// Ruta para la API que devuelve datos simulados
app.get('/api/data/propuesta', (req, res) => {
  const data = [
    {
      id: 1,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "A",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "U",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "A",
        css: "color: #333333;"
      }
    ],
      name_completo: "",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/faua.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Arquitectura",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 2,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #333333;"
      },
      {
        id: 2,
        letra: "I",
        css: "color: #A2281D;"
      },
      {
        id: 3,
        letra: "C",
        css: "color: #A2281D;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA CIVIL",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fic.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Civil",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 3,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "I",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "E",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "E",
        css: "color: #333333;"
      },
      {
        id: 5,
        letra: "C",
        css: "color: #333333;"
      },
      {
        id: 6,
        letra: "S",
        css: "color: #333333;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA ECONÓMICA, ESTADÍSTICA Y CIENCIAS SOCIALES",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fieecs.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Económica",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Ingeniería Estadística",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 4,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "I",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "G",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "M",
        css: "color: #333333;"
      },
      {
        id: 5,
        letra: "M",
        css: "color: #333333;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA GEOLOGICA, MINERA Y METALÚRGICA",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/figmm.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Geológica",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Ingeniería Metalúrgica",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 3,
          name: "Ingeniería de Minas",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 5,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "I",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "I",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "S",
        css: "color: #333333;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA INDUSTRIAL Y SISTEMAS",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fiis.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Industrial",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Ingeniería de Sistemas",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 3,
          name: "Ingeniería de Software",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 6,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "A",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "U",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "A",
        css: "color: #333333;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA ELÉCTRICA Y ELECTRÓNICA",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fiee.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Eléctrica",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Ingeniería Electrónica",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 3,
          name: "Ingeniería de Telecomunicaciones",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 4,
          name: "Ingeniería de Ciberseguridad",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 7,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "A",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "U",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "A",
        css: "color: #333333;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA MECÁNICA",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fim.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Mecánica",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Ingeniería Mecánica Electrica",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 3,
          name: "Ingeniería Naval",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 4,
          name: "Ingeniería Mecatrónica",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 8,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "A",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "U",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "A",
        css: "color: #333333;"
      }
    ],
      name_completo: "FACULTAD DE CIENCIAS",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fc.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Física",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Matemáticas",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 3,
          name: "Química",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 4,
          name: "Ingeniería Física",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 5,
          name: "Ciencia de la Computación",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 9,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "A",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "U",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "A",
        css: "color: #333333;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA DE PETRÓLEO, GAS NATURAL Y PETROQUÍMICA",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fip.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Petroquímica",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Ingeniería de Petróleo y Gas Natural",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id: 10,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "A",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "U",
        css: "color: #A2281D;"
      },
      {
        id: 4,
        letra: "A",
        css: "color: #333333;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA QUÍMICA Y TEXTIL",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fiqt.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Química",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Ingeniería Textil",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    },
    {
      id:11,
      name_abreviado: [{
        id: 1,
        letra: "F",
        css: "color: #A2281D;"
      },
      {
        id: 2,
        letra: "I",
        css: "color: #333333;"
      },
      {
        id: 3,
        letra: "A",
        css: "color: #A2281D;"
      }
    ],
      name_completo: "FACULTAD DE INGENIERÍA AMBIENTAL",
      imagen_del_card: "https://posgrado.uni.edu.pe/images/2022/03/21/fia.jpg",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Ingeniería Sanitaria",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 2,
          name: "Ingeniería de Higiene y Seguridad Industrial",
          href_especialidad: "/vista2 va a cargar una funcion",
        },
        {
          id: 3,
          name: "Ingeniería Ambiental",
          href_especialidad: "/vista2 va a cargar una funcion",
        }
      ],
    }
  ];
  res.json(data); // Devuelve los datos en formato JSON
});

//----------------------------------------------------------------
// Ruta para la API que devuelve datos simulados
app.get('/api/data/propuesta/a', (req, res) => {
  const data = [
    {
      id: 1,
      name_abreviado: "FAUA",
      name_completo: "FACULTAD DE ARQUITECTURA URBANISMO Y ARTES",
      imagen_del_card: "https://i.postimg.cc/6QCzHP9x/facultad-de-arquitectura.png",
      imagen_logo: "https://i.postimg.cc/vBWkCzdC/birrete.png",
      href_tarjeta: "/vista2",
      especialidades: [
        {
          id: 1,
          name: "Arquitectura",
          href: "/vista2 va a cargar una funcion",
        }
      ],
    }
  ];
  res.json(data); // Devuelve los datos en formato JSON
});
//----------------------------------------------------------------

// Ruta para la API que devuelve datos simulados
app.get('/api/data/tablas', (req, res) => {
  const data = [
    {
      id: 1,
      name_de_la_tabla: "TABLA DE ACTIVIDADES",
      olumna1: "PERIODO",
      olumna2: "COD. ACTI",
      olumna3: "DESCRIPCIÓN",
      olumna4: "FECHA INICIO",
      olumna5: "FECHA FIN",
      olumna6: "ESTADO",
      olumna7: "JERARQUIA",
      olumna12: "OPCIONES"
    },
    {
      id: 1,
      name_de_la_tabla: "TABLA DE ACTIVIDADES",
      olumna1: "PERIODO",
      olumna2: "COD. ACTI",
      olumna3: "DESCRIPCIÓN",
      olumna4: "FECHA INICIO",
      olumna5: "FECHA FIN",
      olumna6: "ESTADO",
      olumna7: "JERARQUIA",
      olumna8: "MEDIO",
      olumna9: "RESPONSABLE",
      olumna10: "PROCESA",
      olumna11: "OBSERVACION",
      olumna12: "OPCIONES"
    }
  ];
  res.json(data); // Devuelve los datos en formato JSON
});

// Ruta para la API que devuelve datos simulados
app.get('/api/data/filtro-checkbox', (req, res) => {
  const data = [
    {
      id: 1,
      name: "Filtro",
      checkbox1: "Universidad",
      checkbox2: "Facultades",
      checkbox3: "Especialidades"
    },
    {
      id: 2,
      name: "Medio",
      checkbox1: "txt",
      checkbox2: "txt",
      checkbox3: "txt"
    },
    {
      id: 3,
      name: "Responsable",
      checkbox1: "txt",
      checkbox2: "txt",
      checkbox3: "txt"
    },
    {
      id: 4,
      name: "Procesa",
      checkbox1: "txt",
      checkbox2: "txt",
      checkbox3: "txt"
    },
  ];
  res.json(data); // Devuelve los datos en formato JSON
});

// Ruta para la API que devuelve datos simulados
app.get('/api/data/para-la-vita1', (req, res) => {
  const data = [
    {
      "id_actividad": 1,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 2,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 3,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 4,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 5,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 6,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 7,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 8,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 9,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 10,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 11,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 12,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 1,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    },
    {
      "id_actividad": 13,
      "nombre_actividad": "Taller de Innovación",
      "jerarquia": {
        "id_jerarquia": 1,
        "nivel": "Universidad"
      },
      "facultad": null,
      "especialidad": null,
      "periodo": "20241",
      "codigo_actividad": "TI",
      "estado": "A",
      "medio": {
        "id_medio": 1,
        "nombre": "Comunicación Interna"
      },
      "responsable": {
        "id_responsable": 2,
        "nombre": "Departamento de Innovación"
      },
      "procesa": {
        "id_procesa": 3,
        "nombre": "Equipo de Procesos"
      },
      "observacion": "Este taller está dirigido a toda la universidad para promover la innovación tecnológica.",
      "fullcalendar": {
        "titulo": "Taller Universitario de Innovación",
        "descripcion": "Reserva de Matricula",
        "fecha_inicio": "2024-02-24T09:00:00",
        "fecha_fin": "2024-07-30T17:00:00",
        "tipo_actividad": "Un Día",
        "todo_el_dia": "N",
        "color": "#FF5733"
      }
    }

  ];
  res.json(data); // Devuelve los datos en formato JSON
});


// Ruta para cargar la vista principal (index.html)
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Ruta para cargar la vista 2 (vista2.html)
app.get('/vista2', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'vista2.html'));
});


// Inicia el servidor
app.listen(PORT, () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
