// resources/js/composables/useAuth.js
import { ref } from 'vue'
import axios from 'axios'

export function useAuth() {
    const user = ref(null)
    const loading = ref(false)
    const errors = ref({})

    const login = async (credentials) => {
        loading.value = true
        errors.value = {}

        try {
            const response = await axios.post('/login', credentials)
            user.value = response.data.user
            return { success: true, data: response.data }
        } catch (error) {
            if (error.response) {
                errors.value = error.response.data.errors || {}

                // Adicionar erro geral se não houver erros específicos
                if (Object.keys(errors.value).length === 0 && error.response.data.message) {
                    errors.value.general = [error.response.data.message]
                }
            } else {
                // Erro de rede ou outro erro
                errors.value.general = ['Erro ao conectar com o servidor. Tente novamente.']
            }

            return {
                success: false,
                errors: errors.value,
                message: error.response?.data?.message
            }
        } finally {
            loading.value = false
        }
    }

    const loginWithPhone = async (phone, countryCode) => {
        loading.value = true
        errors.value = {}

        try {
            const response = await axios.post('/login/phone', {
                phone: countryCode + phone
            })
            return { success: true, data: response.data }
        } catch (error) {
            if (error.response) {
                errors.value = error.response.data.errors || {}

                if (Object.keys(errors.value).length === 0 && error.response.data.message) {
                    errors.value.general = [error.response.data.message]
                }
            } else {
                errors.value.general = ['Erro ao conectar com o servidor. Tente novamente.']
            }

            return {
                success: false,
                errors: errors.value
            }
        } finally {
            loading.value = false
        }
    }

    const register = async (data) => {
        loading.value = true
        errors.value = {}

        try {
            const response = await axios.post('/register', data)
            user.value = response.data.user
            return {
                success: true,
                data: response.data,
                redirect: response.data.redirect
            }
        } catch (error) {
            if (error.response) {
                errors.value = error.response.data.errors || {}

                if (Object.keys(errors.value).length === 0 && error.response.data.message) {
                    errors.value.general = [error.response.data.message]
                }
            } else {
                errors.value.general = ['Erro ao conectar com o servidor. Tente novamente.']
            }

            return {
                success: false,
                errors: errors.value
            }
        } finally {
            loading.value = false
        }
    }

    return {
        user,
        loading,
        errors,
        login,
        loginWithPhone,
        register
    }
}
