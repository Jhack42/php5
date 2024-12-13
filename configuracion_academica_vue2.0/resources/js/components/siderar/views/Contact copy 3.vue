<template>
    <div class="iframe-container">
        <iframe src="http://127.0.0.1:8000/calendar" class="iframe-content" frameborder="0"></iframe>
    </div>
</template>

<script>
export default {
    name: 'Contact'
}
</script>

<style scoped>
.iframe-container {
    width: 100%;
    height: 100%;
    position: relative;
}

.iframe-content {
    width: 100%;
    height: 100%;
    border: none;
    margin: 0;
    padding: 0;
    display: block;
}
</style>
