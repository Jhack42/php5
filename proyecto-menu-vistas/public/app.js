document.addEventListener('DOMContentLoaded', function() {
    const menuButtons = document.querySelectorAll('#menu button');
    const contentDiv = document.getElementById('content');
  
    // Función para cargar una vista
    function loadView(view) {
      // Llamada a la API para obtener el contenido de la vista
      fetch(`/api/vista/${view}`)
        .then(response => response.text())
        .then(html => {
          contentDiv.innerHTML = html;
          // Guardar la vista actual en una cookie
          document.cookie = `currentView=${view}; path=/`;
        })
        .catch(error => console.error('Error al cargar la vista:', error));
    }
  
    // Evento de clic en los botones del menú
    menuButtons.forEach(button => {
      button.addEventListener('click', function() {
        const view = this.getAttribute('data-view');
        loadView(view);
      });
    });
  
    // Verificar si ya hay una vista guardada en las cookies
    function getCookie(name) {
      const value = `; ${document.cookie}`;
      const parts = value.split(`; ${name}=`);
      if (parts.length === 2) return parts.pop().split(';').shift();
    }
  
    const currentView = getCookie('currentView');
    if (currentView) {
      loadView(currentView);
    } else {
      // Cargar una vista por defecto si no hay nada en las cookies
      loadView('home');
    }
  });
  