// script.js

// Cargar la librería `marked`
const script = document.createElement('script');
script.src = "https://cdn.jsdelivr.net/npm/marked@4.0.16/marked.min.js";
script.onload = () => {
// Contenido en formato Markdown
const markdownContent = `
# Git Commands Cheat Sheet

Para subir los cambios de tu rama \`rama1-jhack\` a un repositorio remoto en Git, sigue estos pasos:

Asegúrate de que estás en la rama correcta:
\`\`\`bash
git checkout rama1-jhack
\`\`\`

Añade los cambios al área de preparación:
\`\`\`bash
git add .
\`\`\`

Esto añadirá todos los archivos modificados. Si solo quieres añadir archivos específicos, puedes reemplazar el \`. \` con el nombre del archivo.

### Ejemplo de Tabla de Comandos

| Comando              | Descripción                               | Ejemplo                   |
|----------------------|-------------------------------------------|---------------------------|
| \`git status\`       | Muestra el estado de los archivos         | \`git status\`            |
| \`git commit\`       | Confirma los cambios                      | \`git commit -m "mensaje"\` |
| \`git push\`         | Sube los cambios al repositorio remoto    | \`git push origin main\`   |
| \`git pull\`         | Trae los cambios desde el repositorio remoto | \`git pull origin main\` |
`;


    // Convierte el Markdown a HTML y muestra el contenido
    document.getElementById('preview').innerHTML = marked.parse(markdownContent);
};

// Añadir el script al documento
document.head.appendChild(script);
