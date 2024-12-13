// ThemeDetector.vue
<template>
  <div :class="{ 'dark-theme': isDarkMode, 'light-theme': !isDarkMode }">
    <slot></slot>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

const isDarkMode = ref(false)

const applyTheme = () => {
  document.documentElement.classList.toggle('dark', isDarkMode.value)
}

watch(isDarkMode, () => {
  applyTheme()
})

onMounted(() => {
  if (window.matchMedia) {
    // Detectar preferencia inicial
    isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches

    // Escuchar cambios en el tema del sistema
    window.matchMedia('(prefers-color-scheme: dark)')
      .addEventListener('change', event => {
        isDarkMode.value = event.matches
      })
  }

  applyTheme()
})
</script>

<style>
:root {
  /* Variables para tema claro */
  --background-color: #ffffff;
  --text-color: #333333;
}

:root.dark {
  /* Variables para tema oscuro */
  --background-color: #1a1a1a;
  --text-color: #ffffff;
}

.light-theme {
  background-color: var(--background-color);
  color: var(--text-color);
}

.dark-theme {
  background-color: var(--background-color);
  color: var(--text-color);
}
</style>
