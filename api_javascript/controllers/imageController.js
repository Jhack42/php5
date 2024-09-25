const path = require('path');

// Controlador para obtener una imagen
function getImage(req, res) {
  const imageName = req.params.imageName;
  const imagePath = path.join(__dirname, '../img', imageName);

  // Verificar si la imagen existe y servirla
  res.sendFile(imagePath, (err) => {
    if (err) {
      res.status(404).json({ message: 'Imagen no encontrada' });
    }
  });
}

module.exports = {
  getImage
};
