document.addEventListener('DOMContentLoaded', function() {
  const menuBtn = document.querySelector('.dropdown-universidad-btn');
  const dropdownContent = document.querySelector('.dropdown-universidad-content');
  
  // Menú desplegable
  if (menuBtn && dropdownContent) {
      menuBtn.addEventListener('click', function () {
          dropdownContent.classList.toggle('show');
      });
  }

  // Otros scripts aquí...
});
