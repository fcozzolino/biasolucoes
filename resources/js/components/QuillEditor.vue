<!-- QuillEditor.vue -->
<template>
  <div class="quill-container">
    <QuillEditor
      v-model:content="content"
      content-type="html"
      :options="editorOptions"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { QuillEditor } from 'vue3-quill'

const content = ref('<p>Digite aqui...</p>')

const editorOptions = {
  theme: 'snow',
  modules: {
    toolbar: [
      ['bold', 'italic', 'underline'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      [{ header: [1, 2, 3, false] }],
    ]
  }
}
</script>

<style scoped>
.quill-container {
  max-width: 100%;
}
</style>
