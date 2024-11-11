
# Git Commands Cheat Sheet


Para subir los cambios de tu rama rama5-ruth a un repositorio remoto en Git, sigue estos pasos:

Asegúrate de que estás en la rama correcta:
```bash
git checkout rama5-ruth
```
Añade los cambios al área de preparación:
```bash
git add .
```
Esto añadirá todos los archivos modificados. Si solo quieres añadir archivos específicos, puedes reemplazar el . con el nombre del archivo.


Confirma los cambios (commitea):
```bash
git commit -m "Descripción de los cambios realizados"
```
Sube la rama al repositorio remoto:
```bash
git push origin rama5-ruth
```
## 2. Actualiza tu rama local con los últimos cambios:
Una vez que estás en tu rama, ejecuta el siguiente comando para traer los cambios más recientes del repositorio remoto:
```bash
git pull origin rama5-ruth
```
## Haz un pull para integrar los cambios remotos:
Esto traerá los cambios del repositorio remoto y aplicará los cambios locales encima de ellos (si es que no hay conflictos).
```bash
git pull origin rama5-ruth --rebase
```
## a. Cambiar a una rama específica

Para cambiar a una rama en Git:

```bash
git checkout <nombre-de-la-rama>
```

Por ejemplo, para cambiar a la rama `rama2-juan`:

```bash
git checkout rama2-juan
```

---

## 2. Verificar los cambios pendientes

Para ver qué archivos han sido modificados o añadidos, usa:

```bash
git status
```

---

## 3. Añadir archivos al área de preparación

Para agregar todos los archivos modificados al área de preparación:

```bash
git add .
```

Para añadir un archivo específico:

```bash
git add <nombre-del-archivo>
```

---

## 4. Confirmar los cambios

Una vez que los archivos están en el área de preparación, confirma los cambios con un mensaje:

```bash
git commit -m "Descripción de los cambios realizados"
```

---

## 5. Subir los cambios al repositorio remoto

Para subir los cambios de una rama al repositorio remoto:

```bash
git push origin <nombre-de-la-rama>
```

Por ejemplo, para subir los cambios en la rama `rama2-juan`:

```bash
git push origin rama2-juan
```

---

## 6. Crear una nueva rama y cambiar a ella

Puedes crear una nueva rama y cambiar a ella en un solo paso:

```bash
git checkout -b <nombre-de-la-nueva-rama>
```

Por ejemplo:

```bash
git checkout -b nueva-rama
```

---

## 7. Fusionar otra rama en la rama actual

Si deseas fusionar los cambios de una rama, como `rama2-juan`, en la rama actual:

1. Primero, asegúrate de estar en la rama que deseas actualizar:

    ```bash
    git checkout <rama-actual>
    ```

2. Luego, fusiona los cambios de la otra rama:

    ```bash
    git merge <nombre-de-la-otra-rama>
    ```

Por ejemplo, para fusionar los cambios de `rama2-juan` en la rama actual:

```bash
git merge rama2-juan
```

---

## 8. Verificar el historial de commits

Para ver el historial de commits en formato simple:

```bash
git log --oneline
```

Para ver el historial de commits con un gráfico que muestra la relación entre ramas:

```bash
git log --oneline --graph --all
```

---

## 9. Eliminar el archivo de bloqueo

Si encuentras un archivo `index.lock` que está bloqueando tus operaciones en Git, puedes eliminarlo con:

```bash
rm -f .git/index.lock
```

---

## 10. Crear una nueva rama

Para crear una nueva rama sin cambiar a ella automáticamente:

```bash
git branch <nombre-de-la-nueva-rama>
```

Luego, puedes cambiar a esa rama con:

```bash
git checkout <nombre-de-la-nueva-rama>
```

---

Este es un resumen básico de algunos de los comandos más comunes de Git. Recuerda que puedes obtener más información sobre cualquier comando de Git usando `git help <comando>`.
