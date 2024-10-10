const nodemailer = require('nodemailer');

// Configura el transporte
let transporter = nodemailer.createTransport({
    host: 'smtp.gmail.com',
    port: 587,
    secure: false,
    auth: {
        user: 'cacereshilasacajhack@gmail.com',
        pass: 'ifll vvfj cxxt kfpf' // Tu contraseña de aplicación de Gmail
    }
});

// Define el correo
let mailOptions = {
    from: '"Jhack" <cacereshilasacajhack@gmail.com>',
    to: 'cacereshilasacajhack@gmail.com', // Cambia esto por una dirección real
    subject: 'Asunto del correo',
    text: 'Contenido del correo en texto plano',
    html: '<p>Contenido del correo en HTML</p>'
};

// Envía el correo
transporter.sendMail(mailOptions, (error, info) => {
    if (error) {
        return console.log('Error al enviar: ', error);
    }
    console.log('Mensaje enviado: %s', info.messageId);
});
