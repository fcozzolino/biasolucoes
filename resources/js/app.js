// Estilos e bibliotecas
import 'bootstrap'
import '../css/app.css'
import '../../public/vendor/css/pages/app-kanban.css'
import 'quill/dist/quill.snow.css'


// Vue e dependências
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'


// Configurações globais do Axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

axios.interceptors.response.use(
  response => response,
  error => {
    console.error('Erro na requisição Axios:', error)

    if (error.response) {
      console.error('Status:', error.response.status)
      console.error('Dados:', error.response.data)
    }

    return Promise.reject(error)
  }
)

// Fix para o problema do TemplateCustomizer
document.addEventListener('DOMContentLoaded', () => {
  if (typeof window.TemplateCustomizer === 'undefined') {
    console.log('TemplateCustomizer não encontrado, criando versão mock')
    window.TemplateCustomizer = {
      init: function () {
        console.log('TemplateCustomizer mock inicializado')
        return this
      },
      setStyle: function () { return this },
      setTheme: function () { return this },
      setLayoutType: function () { return this },
      setLayoutMenuFlipped: function () { return this },
      setDropdownOnHover: function () { return this },
      setLayoutNavbarFixed: function () { return this },
      setLayoutFooterFixed: function () { return this },
      enable: function () { return this },
      disable: function () { return this },
      update: function () { return this }
    }

    if (document.querySelector('.template-customizer-open-btn')) {
      console.log('Botão customizador detectado, inicializando mock')
      window.TemplateCustomizer.init()
    }
  }
})

// Inicialização da aplicação Vue via router
const app = createApp(App)

app.config.errorHandler = (err, vm, info) => {
  console.error('Erro na aplicação Vue:', err)
  console.error('Informações do erro:', info)
}

app.use(router)

app.mount('#app')
