import './bootstrap'
import '../css/app.css'

import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'

// Import components
import LoginForm from './components/auth/LoginForm.vue'
import RegisterForm from './components/auth/RegisterForm.vue'
import OTPVerification from './components/auth/OTPVerification.vue'

// Create router with auth routes
const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: LoginForm
        },
        {
            path: '/register',
            name: 'register',
            component: RegisterForm
        },
        {
            path: '/verify-otp',
            name: 'verify-otp',
            component: OTPVerification
        }
    ]
})

// Create app
const app = createApp({})

// Register components globally (para usar em Blade se necessário)
app.component('login-form', LoginForm)
app.component('register-form', RegisterForm)
app.component('otp-verification', OTPVerification)

// Use router
app.use(router)

// Configure axios
import axios from 'axios'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

// Mount app
app.mount('#app')

console.log('Auth app mounted successfully!')
