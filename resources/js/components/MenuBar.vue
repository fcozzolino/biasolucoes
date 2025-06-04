<template>
  <div class="editor-toolbar mb-2">
    <button @click="editor.chain().focus().toggleBold().run()" :class="{ active: editor.isActive('bold') }">B</button>
    <button @click="editor.chain().focus().toggleItalic().run()" :class="{ active: editor.isActive('italic') }">I</button>
    <button @click="editor.chain().focus().toggleUnderline().run()" :class="{ active: editor.isActive('underline') }">U</button>
    <button @click="editor.chain().focus().toggleBulletList().run()" :class="{ active: editor.isActive('bulletList') }">• Lista</button>
    <button @click="editor.chain().focus().toggleOrderedList().run()" :class="{ active: editor.isActive('orderedList') }">1. Lista</button>
  </div>
</template>

<script setup>
defineProps({
  editor: Object,
})
</script>

<style scoped>
.editor-toolbar {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

button {
  background-color: #f3f3f3;
  border: none;
  padding: 0.4rem 0.6rem;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
}

button.active {
  background-color: #d1d1ff;
}
</style>
