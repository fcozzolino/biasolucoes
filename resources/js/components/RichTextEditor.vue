<!-- v2 -->
<template>
  <div class="rich-text-editor">
    <!-- editor com altura vinda de props.height -->
    <div ref="editor" :style="{ background: 'white', minHeight: props.height }"></div>
  </div>
</template>

<script setup>
  import { ref, onMounted, watch, nextTick, onBeforeUnmount } from 'vue';

  const props = defineProps({
    modelValue: {
      type: String,
      default: '',
    },
    placeholder: {
      type: String,
      default: 'Digite aqui...',
    },
    height: {
      type: String,
      default: '120px',
    },
  });

  const emit = defineEmits(['update:modelValue']);
  const editor = ref(null);
  const quill = ref(null);

  // expõe a instância de Quill para o pai
  defineExpose({ quill });

  onMounted(() => {
    if (typeof window.Quill === 'undefined') {
      // carrega Quill via CDN
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
      document.head.appendChild(link);

      const script = document.createElement('script');
      script.src = 'https://cdn.quilljs.com/1.3.6/quill.js';
      script.onload = initializeQuill;
      document.head.appendChild(script);
    } else {
      initializeQuill();
    }
  });

  onBeforeUnmount(() => {
    if (quill.value) {
      quill.value.off('text-change');
      quill.value = null;
    }
  });

  function initializeQuill() {
    if (!editor.value || !window.Quill) return;

    quill.value = new window.Quill(editor.value, {
      theme: 'snow',
      placeholder: props.placeholder,
      modules: {
        toolbar: [
          [{ header: [1, 2, 3, 4, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ align: [] }],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['blockquote', 'code-block'],
          ['link'],
          ['clean'],
        ],
      },
    });

    // conteúdo inicial
    quill.value.root.innerHTML = props.modelValue || '<p><br></p>';

    // sincroniza com o v-model
    quill.value.on('text-change', () => {
      emit('update:modelValue', quill.value.root.innerHTML);
    });
  }

  watch(
    () => props.modelValue,
    (newVal, oldVal) => {
      if (!quill.value || newVal === oldVal) return;
      const current = quill.value.root.innerHTML;
      if (current !== newVal) {
        const sel = quill.value.getSelection();
        quill.value.root.innerHTML = newVal || '<p><br></p>';
        nextTick(() => {
          // tenta restaurar a seleção
          try {
            if (sel) {
              quill.value.setSelection(sel.index, sel.length);
            }
          } catch {
            const len = quill.value.getLength();
            if (len > 0) quill.value.setSelection(len - 1, 0);
          }
        });
      }
    }
  );
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
    /* padding interno */
    padding: 12px;
  }
</style>
