<template>
    <ThemeDetector>
      <div class="visual-editor">
        <div class="editor-toolbar">
          <div class="toolbar-group">
            <button
              class="toolbar-button"
              @click="editor.chain().focus().toggleBold().run()"
              :class="{ 'is-active': editor.isActive('bold') }"
              title="Negrita"
            >
              <i class="fas fa-bold"></i>
            </button>
            <button
              class="toolbar-button"
              @click="editor.chain().focus().toggleItalic().run()"
              :class="{ 'is-active': editor.isActive('italic') }"
              title="Cursiva"
            >
              <i class="fas fa-italic"></i>
            </button>
            <button
              class="toolbar-button"
              @click="editor.chain().focus().toggleUnderline().run()"
              :class="{ 'is-active': editor.isActive('underline') }"
              title="Subrayado"
            >
              <i class="fas fa-underline"></i>
            </button>
          </div>

          <div class="toolbar-group">
            <button
              class="toolbar-button"
              @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
              :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }"
              title="Título 1"
            >
              H1
            </button>
            <button
              class="toolbar-button"
              @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
              :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }"
              title="Título 2"
            >
              H2
            </button>
            <button
              class="toolbar-button"
              @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
              :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }"
              title="Título 3"
            >
              H3
            </button>
          </div>

          <div class="toolbar-group">
            <button
              class="toolbar-button"
              @click="editor.chain().focus().toggleBulletList().run()"
              :class="{ 'is-active': editor.isActive('bulletList') }"
              title="Lista con viñetas"
            >
              <i class="fas fa-list-ul"></i>
            </button>
            <button
              class="toolbar-button"
              @click="editor.chain().focus().toggleOrderedList().run()"
              :class="{ 'is-active': editor.isActive('orderedList') }"
              title="Lista numerada"
            >
              <i class="fas fa-list-ol"></i>
            </button>
          </div>

          <div class="toolbar-group">
            <button
              class="toolbar-button"
              @click="addImage"
              title="Insertar imagen"
            >
              <i class="fas fa-image"></i>
            </button>
            <button
              class="toolbar-button"
              @click="addLink"
              :class="{ 'is-active': editor.isActive('link') }"
              title="Insertar enlace"
            >
              <i class="fas fa-link"></i>
            </button>
          </div>

          <div class="toolbar-group">
            <button
              class="toolbar-button"
              @click="editor.chain().focus().undo().run()"
              title="Deshacer"
            >
              <i class="fas fa-undo"></i>
            </button>
            <button
              class="toolbar-button"
              @click="editor.chain().focus().redo().run()"
              title="Rehacer"
            >
              <i class="fas fa-redo"></i>
            </button>
          </div>
        </div>

        <editor-content :editor="editor" class="editor-content" />
      </div>
    </ThemeDetector>
  </template>

  <script>
  import { Editor, EditorContent } from '@tiptap/vue-3'
  import StarterKit from '@tiptap/starter-kit'
  import Image from '@tiptap/extension-image'
  import Link from '@tiptap/extension-link'
  import Underline from '@tiptap/extension-underline'
  import TextAlign from '@tiptap/extension-text-align'
  import ThemeDetector from '../../../../components/ThemeDetector.vue'

  export default {
    name: 'VisualEditor',

    components: {
      EditorContent,
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
      const editor = new Editor({
        content: props.modelValue,
        extensions: [
          StarterKit,
          Image.configure({
            inline: true,
            allowBase64: true,
          }),
          Link.configure({
            openOnClick: false,
            validate: href => /^https?:\/\//.test(href),
          }),
          Underline,
          TextAlign.configure({
            types: ['heading', 'paragraph'],
          }),
        ],
        onUpdate: ({ editor }) => {
          emit('update:modelValue', editor.getHTML())
        }
      })

      const addImage = () => {
        const url = prompt('Ingresa la URL de la imagen:')
        if (url) {
          editor.chain().focus().setImage({ src: url }).run()
        }
      }

      const addLink = () => {
        const url = prompt('Ingresa la URL del enlace:')
        if (url) {
          editor.chain().focus().setLink({ href: url }).run()
        }
      }

      return {
        editor,
        addImage,
        addLink
      }
    }
  }
  </script>

  <style lang="scss" src="../../../../../scss/editor.scss"></style>
