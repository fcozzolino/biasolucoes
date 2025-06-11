import { createApp } from 'vue';
import axios from 'axios';

// Importar estilos necessários
import '../css/app.css'
import '../../public/vendor/css/pages/app-kanban.css'
import '../../public/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss'
import 'quill/dist/quill.snow.css'

import PerfectScrollbar from 'perfect-scrollbar'
window.PerfectScrollbar = PerfectScrollbar

// Configurar axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Importar componentes
import KanbanBoard from './components/KanbanBoard.vue';
import TarefasIndex from './components/TarefasIndex.vue';

// Função para montar componentes individuais
window.mountVueComponents = () => {
  // Montar TarefasIndex se o elemento existir
  const tarefasEl = document.getElementById('tarefas-app');
  if (tarefasEl) {
    const app = createApp(TarefasIndex);
    app.config.globalProperties.$axios = axios;
    app.mount('#tarefas-app');
  }
  const kanbanEl = document.getElementById('kanban-app');
  if (kanbanEl) {
    const app = createApp(KanbanBoard);
    app.config.globalProperties.$axios = axios;
    // Passar o UUID como prop
    const boardUuid = kanbanEl.dataset.boardUuid;
    app.provide('boardUuid', boardUuid);
    app.mount('#kanban-app');
  }
};

// Executar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
  window.mountVueComponents();
});
