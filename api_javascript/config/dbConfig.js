require('dotenv').config(); // Cargar las variables de entorno desde .env

module.exports = {
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    connectString: process.env.DB_CONNECTIONSTRING
};
