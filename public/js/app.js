document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll('.sidebar button');
    const content = document.getElementById('content');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // Eliminar la clase 'active' de todos los botones
            buttons.forEach(btn => btn.classList.remove('active'));

            // Agregar la clase 'active' al botón clickeado
            button.classList.add('active');

            // Obtener la página a cargar del atributo data-page
            const page = button.getAttribute('data-page');

            // Hacer la solicitud para obtener el contenido con la ruta completa
            fetch(`/php5/app/Views/${page}.php`)
                .then(response => response.text())
                .then(data => {
                    // Cargar el contenido en el contenedor
                    content.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error al cargar el contenido:', error);
                    content.innerHTML = '<p>Hubo un error al cargar la página.</p>';
                });
        });
    });
});
