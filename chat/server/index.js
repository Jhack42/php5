// server/index.js
import express from 'express';
import logger from 'morgan';
import { Server } from 'socket.io';
import { createServer } from 'node:http';
import path from 'path';
import dotenv from 'dotenv';
import cookieParser from 'cookie-parser';
import session from 'express-session';

import authRoutes from './authRoutes.js';
import chatRoutes from './chatRoutes.js';
import handleSocketConnection from './socketHandlers.js';

dotenv.config();

const port = process.env.PORT ?? 3000;
const app = express();
const server = createServer(app);
const io = new Server(server, {
  cors: {
    origin: "http://localhost:3000",
    methods: ["GET", "POST"],
    credentials: true
  },
  connectionStateRecovery: {
    maxDisconnectionDuration: 2 * 60 * 1000,
    useFindBestOffset: true,
  },
});

// Middlewares
app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cookieParser());
app.use(session({
  secret: 'supersecretkey',
  resave: false,
  saveUninitialized: false,
  cookie: { secure: false, httpOnly: true }
}));

// Rutas
app.use('/api', authRoutes);
app.use('/api', chatRoutes);

// Configuración de Socket.IO
handleSocketConnection(io);

// Redirección a la página de inicio de sesión
app.get('/', (req, res) => {
  res.redirect('/login.html');
});

app.use(express.static(path.join(process.cwd(), 'client')));

server.listen(port, () => {
  console.log(`Servidor en ejecución en el puerto ${port}`);
});
