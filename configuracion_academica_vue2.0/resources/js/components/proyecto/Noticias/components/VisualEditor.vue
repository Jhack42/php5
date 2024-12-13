<template>
    <ThemeDetector>
      <div class="grapesjs-editor">
        <div id="gjs" ref="editor"></div>
      </div>
    </ThemeDetector>
</template>

<script>
import 'grapesjs/dist/css/grapes.min.css'
import grapesjs from 'grapesjs'
import gjsPresetWebpage from 'grapesjs-preset-webpage'
import ThemeDetector from '../../../../components/ThemeDetector.vue'
import { onMounted, onBeforeUnmount, watch } from 'vue'

export default {
  name: 'VisualEditor',

  components: {
    ThemeDetector
  },

  props: {
    modelValue: {
      type: String,
      default: ''
    }
  },

  emits: ['update:modelValue'],

  setup(props, { emit }) {
    let editor = null;

    // Función para generar HTML tradicional
    const generateTraditionalHTML = (html, css) => {
      return `<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hola Mundo Cuadrado</title>
    <style>
        ${css}
    </style>
</head>
<body>
    ${html}
</body>
</html>`;
    };

    // Función para extraer HTML y CSS del formato tradicional
    const parseTraditionalHTML = (content) => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(content, 'text/html');

      // Obtener el CSS
      const styleTag = doc.querySelector('style');
      const css = styleTag ? styleTag.innerHTML : '';

      // Obtener el HTML del body
      const bodyContent = doc.body ? doc.body.innerHTML : '';

      return { html: bodyContent.trim(), css: css.trim() };
    };

    const initEditor = () => {
      editor = grapesjs.init({
        container: '#gjs',
        height: '500px',
        fromElement: false,
        storageManager: false,
        plugins: [gjsPresetWebpage],
        pluginsOpts: {
          gjsPresetWebpage: {}
        },
        panels: {
          defaults: [
            {
              id: 'basic-actions',
              el: '.panel__basic-actions',
              buttons: [
                {
                  id: 'visibility',
                  active: true,
                  className: 'btn-toggle-borders',
                  label: '<i class="fas fa-border-none"></i>',
                  command: 'sw-visibility'
                },
                {
                  id: 'preview',
                  className: 'btn-preview',
                  label: '<i class="fas fa-eye"></i>',
                  command: 'preview'
                },
                {
                  id: 'undo',
                  className: 'btn-undo',
                  label: '<i class="fas fa-undo"></i>',
                  command: 'undo'
                },
                {
                  id: 'redo',
                  className: 'btn-redo',
                  label: '<i class="fas fa-redo"></i>',
                  command: 'redo'
                }
              ]
            }
          ]
        },
        blockManager: {
          appendTo: '#blocks',
          blocks: [
            {
              id: 'section',
              label: 'Sección',
              category: 'Basic',
              content: `<section class="section">
                        <h2>Nueva Sección</h2>
                        <p>Contenido de la sección</p>
                      </section>`
            },
            {
              id: 'text',
              label: 'Texto',
              category: 'Basic',
              content: '<p>Inserta tu texto aquí</p>'
            },
            {
              id: 'image',
              label: 'Imagen',
              category: 'Basic',
              content: { type: 'image' }
            },
            {
              id: 'button',
              label: 'Botón',
              category: 'Basic',
              content: '<button class="button">Click me</button>'
            }
          ]
        },
        canvas: {
          styles: [
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
          ]
        }
      });

      // Cargar el contenido inicial
      if (props.modelValue) {
        try {
          const { html, css } = parseTraditionalHTML(props.modelValue);
          editor.setComponents(html);
          editor.setStyle(css);
        } catch (e) {
          // Si hay un error al parsear, intentamos cargar como HTML simple
          editor.setComponents(props.modelValue);
        }
      }

      // Manejar cambios en el editor
      editor.on('change:changesCount', () => {
        const html = editor.getHtml();
        const css = editor.getCss();
        const traditionalHTML = generateTraditionalHTML(html, css);
        emit('update:modelValue', traditionalHTML);
      });
    };

    // Observar cambios en el modelValue
    watch(() => props.modelValue, (newValue) => {
      if (editor && newValue) {
        try {
          const { html, css } = parseTraditionalHTML(newValue);
          const currentHtml = editor.getHtml();
          const currentCss = editor.getCss();

          if (html !== currentHtml || css !== currentCss) {
            editor.setComponents(html);
            editor.setStyle(css);
          }
        } catch (e) {
          // Si hay un error al parsear, intentamos cargar como HTML simple
          if (newValue !== editor.getHtml()) {
            editor.setComponents(newValue);
          }
        }
      }
    });

    onMounted(() => {
      initEditor();
    });

    onBeforeUnmount(() => {
      if (editor) {
        editor.destroy();
      }
    });

    return {};
  }
}
</script>

  <style lang="scss" src="../../../../../scss/editor.scss"></style>
