document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('nav.sidebar button');
    const content = document.getElementById('content');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const page = button.getAttribute('data-page');
            loadPage(page);
        });
    });

    function loadPage(page: string | null) {
        if (content) {
            content.innerHTML = `<h1>${page}</h1><p>Contenido de la página ${page}</p>`;
        }
    }
});
