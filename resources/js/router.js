import { createRouter, createWebHistory } from 'vue-router'
import KanbanBoard from './components/KanbanBoard.vue'

const routes = [
  {
    path: '/board/:id',
    name: 'kanban',
    component: KanbanBoard,
    props: route => ({ boardId: parseInt(route.params.id) }) // aqui está a mágica
  }
]

export default createRouter({
  history: createWebHistory(),
  routes
})
