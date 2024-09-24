document.addEventListener('DOMContentLoaded', function() {
    const content = document.getElementById('content');
    const navLinks = document.querySelectorAll('.nav-link');
    const collapsibleLinks = document.querySelectorAll('a[data-toggle="collapse"]');
  
    // Cargar la vista guardada si la hay en localStorage
    const savedView = localStorage.getItem('currentView');
    if (savedView && !savedView.startsWith('submenu')) {
      loadContent(savedView);
    } else {
      // Cargar la vista inicial por defecto si no hay vista guardada
      loadContent('home');
    }
  
    // Añadir evento a los enlaces de la barra lateral
    navLinks.forEach(link => {
      link.addEventListener('click', function(event) {
        event.preventDefault();
        const view = this.getAttribute('data-view');
  
        // Si el enlace es para un submenú, no cargar contenido dinámico
        if (!view) return;
  
        // Guardar la vista actual en localStorage
        localStorage.setItem('currentView', view);
  
        // Quitar la clase 'active' de todos los botones
        navLinks.forEach(l => l.classList.remove('active'));
        // Añadir la clase 'active' al botón clickeado
        this.classList.add('active');
  
        // Cargar el contenido dinámico
        loadContent(view);
      });
    });
  
    // Añadir eventos a los enlaces colapsables para evitar cargar contenido
    collapsibleLinks.forEach(link => {
      link.addEventListener('click', function(event) {
        event.preventDefault(); // Evita que haga otra acción
      });
    });
  
    function loadContent(view) {
      // Ajusta la ruta para que apunte correctamente a los archivos PHP
      fetch(`/php5/app/Views/${view}.php`)
        .then(response => response.text())
        .then(data => {
          content.innerHTML = data;
        })
        .catch(error => {
          content.innerHTML = `<p>Error al cargar la vista: ${view}</p>`;
        });
    }
  });
  