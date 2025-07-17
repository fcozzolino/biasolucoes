import { ref, watch } from 'vue'

export const useValidation = () => {
  const rules = {
    required: (value) => !!value || 'Campo obrigatório',

    email: (value) => {
      const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return pattern.test(value) || 'Email inválido'
    },

    phone: (value) => {
      const pattern = /^[0-9]{10,11}$/
      return pattern.test(value) || 'Telefone inválido'
    },

    cpf: (value) => {
      if (!value) return true

      const cpf = value.replace(/[^\d]/g, '')

      if (cpf.length !== 11) return 'CPF deve ter 11 dígitos'

      // Validação do CPF
      if (/^(\d)\1{10}$/.test(cpf)) return 'CPF inválido'

      let sum = 0
      for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i)
      }

      let remainder = 11 - (sum % 11)
      if (remainder === 10 || remainder === 11) remainder = 0
      if (remainder !== parseInt(cpf.charAt(9))) return 'CPF inválido'

      sum = 0
      for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i)
      }

      remainder = 11 - (sum % 11)
      if (remainder === 10 || remainder === 11) remainder = 0
      if (remainder !== parseInt(cpf.charAt(10))) return 'CPF inválido'

      return true
    },

    cnpj: (value) => {
      if (!value) return true

      const cnpj = value.replace(/[^\d]/g, '')

      if (cnpj.length !== 14) return 'CNPJ deve ter 14 dígitos'

      // Validação simplificada do CNPJ
      if (/^(\d)\1{13}$/.test(cnpj)) return 'CNPJ inválido'

      return true
    },

    minLength: (min) => (value) => {
      return value.length >= min || `Mínimo ${min} caracteres`
    },

    maxLength: (max) => (value) => {
      return value.length <= max || `Máximo ${max} caracteres`
    },

    match: (field, fieldName = 'campo') => (value) => {
      return value === field || `${fieldName} não corresponde`
    },

    strongPassword: (value) => {
      if (value.length < 8) return 'Senha deve ter no mínimo 8 caracteres'
      if (!/[A-Z]/.test(value)) return 'Senha deve conter letra maiúscula'
      if (!/[a-z]/.test(value)) return 'Senha deve conter letra minúscula'
      if (!/[0-9]/.test(value)) return 'Senha deve conter número'
      if (!/[^A-Za-z0-9]/.test(value)) return 'Senha deve conter caractere especial'
      return true
    }
  }

  const validate = (value, validationRules) => {
    for (const rule of validationRules) {
      const result = rule(value)
      if (result !== true) return result
    }
    return true
  }

  const createFieldValidator = (initialValue = '', validationRules = []) => {
    const value = ref(initialValue)
    const error = ref('')
    const touched = ref(false)

    const validateField = () => {
      if (!touched.value) return
      const result = validate(value.value, validationRules)
      error.value = result === true ? '' : result
    }

    watch(value, validateField)

    return {
      value,
      error,
      touched,
      validate: () => {
        touched.value = true
        validateField()
        return error.value === ''
      },
      reset: () => {
        value.value = initialValue
        error.value = ''
        touched.value = false
      }
    }
  }

  return {
    rules,
    validate,
    createFieldValidator
  }
}
