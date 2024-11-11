<template>
    <div class="flex flex-col h-screen w-screen">
        <nav class="bg-gray-800 text-white text-center flex items-center justify-center" style="height: 30px;">
    <h1 class="text-sm">FACULTADES</h1>
</nav>
        <!-- Contenedor principal que contiene la barra lateral y el contenido -->
        <div class="flex-1 flex bg-blue-200 no-bg">
            <!-- Barra lateral de filtro -->
            <div class="bg-gray-100 p-4" style="width: 250px;">
                <p>Barra lateral de filtro</p>
                <!-- Agrega aquí tus elementos de filtro -->
            </div>

            <!-- Contenedor de contenido principal que ocupa el espacio restante -->
            <div class="flex-1 bg-white p-4">
                <p>Contenido principal aquí</p>
                <!-- Este div ocupará el espacio restante -->
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Blank'
};
</script>

<style scoped>
.no-bg {
    background-color: transparent !important;
}
</style>
