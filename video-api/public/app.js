document.addEventListener('DOMContentLoaded', () => {
    const videoElement = document.getElementById('videoElement');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const muteBtn = document.getElementById('muteBtn');
    const barraProgreso = document.getElementById('barraProgreso');
    const volumen = document.getElementById('volumen');
    
    // Ruta de video, por ahora usamos una ruta fija para pruebas
    const videoPath = '/api/video/QUE_ES_JSON.mp4';
    
    // Asignar la fuente del video
    videoElement.src = videoPath;

    // Play/Pause
    playPauseBtn.addEventListener('click', () => {
        if (videoElement.paused) {
            videoElement.play();
            playPauseBtn.textContent = 'Pause';
        } else {
            videoElement.pause();
            playPauseBtn.textContent = 'Play';
        }
    });

    // Actualizar barra de progreso
    videoElement.addEventListener('timeupdate', () => {
        const progreso = (videoElement.currentTime / videoElement.duration) * 100;
        barraProgreso.value = progreso;
    });

    // Cambiar el tiempo del video con la barra de progreso
    barraProgreso.addEventListener('input', () => {
        const nuevoTiempo = (barraProgreso.value / 100) * videoElement.duration;
        videoElement.currentTime = nuevoTiempo;
    });

    // Mute/Unmute
    muteBtn.addEventListener('click', () => {
        videoElement.muted = !videoElement.muted;
        muteBtn.textContent = videoElement.muted ? 'Unmute' : 'Mute';
    });

    // Control de volumen
    volumen.addEventListener('input', () => {
        videoElement.volume = volumen.value;
    });

    // Manejo de errores en la carga del video
    videoElement.addEventListener('error', (e) => {
        handleError(e);
    });

    // Manejador de error personalizado
    function handleError(e) {
        let errorMessage = "Ha ocurrido un error.";
        switch (e.target.error.code) {
            case e.target.error.MEDIA_ERR_ABORTED:
                errorMessage = "La carga del video fue interrumpida.";
                break;
            case e.target.error.MEDIA_ERR_NETWORK:
                errorMessage = "Error de red. Verifica tu conexión.";
                break;
            case e.target.error.MEDIA_ERR_DECODE:
                errorMessage = "Error al decodificar el video. El archivo podría estar corrupto o en un formato no compatible.";
                break;
            case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                errorMessage = "El formato del video no es compatible o la fuente no se pudo encontrar.";
                break;
            default:
                errorMessage = "Ocurrió un error desconocido al cargar el video.";
                break;
        }
        console.error("Error al cargar el video:", errorMessage);
        alert(errorMessage);
    }

    // Si el video está listo para reproducir
    videoElement.addEventListener('canplay', () => {
        console.log("El video está listo para reproducir.");
    });

    // Si hay problemas durante la reproducción
    videoElement.addEventListener('stalled', () => {
        alert("La reproducción se ha detenido debido a problemas de conexión.");
    });
    
    videoElement.addEventListener('waiting', () => {
        console.log("El video está cargando...");
    });

    videoElement.addEventListener('playing', () => {
        console.log("El video está reproduciéndose correctamente.");
    });
});
