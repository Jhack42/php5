const nodemailer = require('nodemailer');

// Configura el transporte
let transporter = nodemailer.createTransport({
    host: 'localhost',
    port: 2525,  // El puerto que configuraste en smtp.ini
    ignoreTLS: true,  // Ignora TLS para conexiones locales
});


// Define el correo
let mailOptions = {
    from: '"Jhack" <cacereshilasacajhack@gmail.com>',
    to: 'cacereshilasacajhack@gmail.com',
    subject: 'Prueba de relay SMTP',
    text: 'Este correo fue enviado a través de Haraka como relay SMTP',
};

// Envía el correo
transporter.sendMail(mailOptions, (error, info) => {
    if (error) {
        return console.log('Error al enviar: ', error);
    }
    console.log('Mensaje enviado: %s', info.messageId);
});
