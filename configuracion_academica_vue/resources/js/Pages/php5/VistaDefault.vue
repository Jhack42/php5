<template>
    <div class="container">
        <div class="contenedor-scroll">
            <div v-html="generarContenidoCartas()" class="tarjetas"></div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, onMounted } from 'vue';
import { fetchTarjetas, tarjetas, construirCarta } from '../contenedor';

export default defineComponent({
    name: 'VistaDefault',
    setup() {
        onMounted(() => {
            fetchTarjetas();
        });

        // Genera el contenido de las cartas
        const generarContenidoCartas = () => tarjetas.value.map(tarjeta => construirCarta(tarjeta)).join('');

        return {
            generarContenidoCartas
        };
    }
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200&family=Julius+Sans+One&display=swap');

.container {
    max-width: 1170px;
    margin: 0 auto;
    /*padding: 20px;*/
}

.contenedor-scroll {
    max-height: calc(100vh - 100px); /* Ajusta 100px según el alto del encabezado u otros elementos */
    /* Define la altura máxima del contenedor con scroll */
    overflow-y: auto;
    /* Activa el scroll vertical si el contenido es mayor a la altura */
    overflow-x: hidden;
    /* Oculta el scroll horizontal */
    border: 1px solid #ffffff;
    /* Bordes opcionales */
    /*padding: 10px;*/
    /* Espaciado opcional */
}

.tarjetas {
    column-count: 1;
    /* Por defecto una columna en pantallas pequeñas */
    column-gap: 20px;
}

.card {
    background-color: #DCDBDC;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    text-align: center;
    padding-bottom: 20px;
    display: inline-block;
    margin-bottom: 20px;
    width: 100%;
    /* Asegura que la tarjeta ocupe el ancho completo de la columna */
    break-inside: avoid;
    /* Evita que la tarjeta se corte entre columnas */
}

@media (min-width: 640px) {
    .tarjetas {
        column-count: 2;
    }
}

@media (min-width: 1024px) {
    .tarjetas {
        column-count: 3;
    }
}

@media (min-width: 1280px) {
    .tarjetas {
        column-count: 4;
    }
}

.imagen {
    padding: 0.5rem;
}

.card img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.title-container {
    position: relative;
    display: inline-block;
}

.title-container img {
    position: absolute;
    left: -30px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
}

.card h1 {
    font-family: 'Nunito', sans-serif;
    font-weight: 200;
    font-size: 2em;
    margin: 0 0 5px;
}

.card p {
    font-family: 'Julius Sans One', sans-serif;
    font-size: 0.9em;
    color: #333333;
    margin: 0;
    line-height: 1.5;
}

.card .divider {
    width: 80%;
    height: 1px;
    background-color: #ffffff;
    margin: 10px auto;
}

.card .department {
    font-family: Arial, sans-serif;
    font-size: 0.9em;
    font-style: italic;
    color: #333333;
    margin-top: 10px;
}
</style>
