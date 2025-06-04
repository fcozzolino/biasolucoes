<template>
  <div class="rich-text-editor">
    <div ref="editor" style="background: white; min-height: 150px;"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])
const editor = ref(null)
let quill = null

onMounted(() => {
  // Carrega o Quill do CDN se não estiver disponível
  if (typeof window.Quill === 'undefined') {
    // Adiciona o CSS
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css'
    document.head.appendChild(link)

    // Adiciona o JS
    const script = document.createElement('script')
    script.src = 'https://cdn.quilljs.com/1.3.6/quill.js'
    script.onload = () => {
      initializeQuill()
    }
    document.head.appendChild(script)
  } else {
    initializeQuill()
  }
})

function initializeQuill() {
  if (editor.value && window.Quill) {
    quill = new window.Quill(editor.value, {
      theme: 'snow',
      placeholder: 'Digite aqui...',
      modules: {
        toolbar: [
          [{ 'header': [1, 2, 3, 4, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ 'align': [] }], [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          ['blockquote', 'code-block'],
          ['link'],
          ['clean'] // Limpar formatação

        ]
      }
    })

    // Define conteúdo inicial
    if (props.modelValue) {
      quill.root.innerHTML = props.modelValue
    }

    // Escuta mudanças
    let isUpdating = false
    quill.on('text-change', () => {
      if (!isUpdating) {
        const content = quill.root.innerHTML
        emit('update:modelValue', content === '<p><br></p>' ? '' : content)
      }
    })
  }
}

// Atualiza o editor quando o valor muda externamente
watch(() => props.modelValue, (newValue, oldValue) => {
  if (quill && newValue !== oldValue) {
    // Verifica se a mudança veio do próprio editor
    const currentContent = quill.root.innerHTML
    if (currentContent !== newValue) {
      // Salva a posição do cursor
      const selection = quill.getSelection()

      // Flag para evitar loop infinito
      isUpdating = true

      // Atualiza o conteúdo
      quill.root.innerHTML = newValue || ''

      // Restaura a posição do cursor se possível
      if (selection) {
        // Aguarda o próximo tick para garantir que o DOM foi atualizado
        nextTick(() => {
          try {
            quill.setSelection(selection.index, selection.length)
          } catch (e) {
            // Se falhar, coloca o cursor no final
            const length = quill.getLength()
            quill.setSelection(length - 1, 0)
          }
          isUpdating = false
        })
      } else {
        isUpdating = false
      }
    }
  }
})
</script>

<style scoped>
.rich-text-editor {
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
}

:deep(.ql-toolbar) {
  background: #f8f9fa;
  border: none;
  border-bottom: 1px solid #ddd;
}

:deep(.ql-container) {
  border: none;
  font-size: 14px;
}

:deep(.ql-editor) {
  min-height: 120px;
  padding: 12px;
}
</style>
