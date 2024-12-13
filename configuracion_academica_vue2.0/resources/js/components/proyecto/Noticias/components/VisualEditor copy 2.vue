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
          // Intentar parsear el contenido como JSON
          const savedData = JSON.parse(props.modelValue);
          editor.setComponents(savedData.html);
          editor.setStyle(savedData.css);
        } catch (e) {
          // Si no es JSON válido, asumimos que es solo HTML
          editor.setComponents(props.modelValue);
        }
      }

      // Manejar cambios en el editor
      editor.on('change:changesCount', () => {
        const html = editor.getHtml();
        const css = editor.getCss();

        // Guardar tanto HTML como CSS en un objeto JSON
        const content = JSON.stringify({
          html: html,
          css: css
        });

        emit('update:modelValue', content);
      });
    };

    // Observar cambios en el modelValue
    watch(() => props.modelValue, (newValue) => {
      if (editor && newValue) {
        try {
          const savedData = JSON.parse(newValue);
          if (savedData.html !== editor.getHtml() || savedData.css !== editor.getCss()) {
            editor.setComponents(savedData.html);
            editor.setStyle(savedData.css);
          }
        } catch (e) {
          // Si no es JSON válido, tratarlo como HTML simple
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
