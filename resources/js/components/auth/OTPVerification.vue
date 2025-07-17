<!-- resources/js/components/auth/OTPVerification.vue -->
<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 p-4">
    <!-- Background Animation -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-10 -left-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
      <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <!-- OTP Card -->
    <div class="relative w-full max-w-md">
      <div class="glass-morphism rounded-2xl shadow-2xl p-8 backdrop-blur-lg">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <i class="fas fa-mobile-alt text-3xl text-white"></i>
          </div>
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Verificação de Segurança
          </h2>
          <p class="text-gray-600 dark:text-gray-300 mt-2">
            Digite o código de 6 dígitos enviado para
          </p>
          <p class="text-indigo-600 dark:text-indigo-400 font-medium mt-1">
            {{ formattedPhone }}
          </p>
        </div>

        <!-- OTP Input Form -->
        <form @submit.prevent="verifyOTP" class="space-y-6">
          <!-- OTP Digits -->
          <div class="flex justify-center space-x-2 md:space-x-3">
            <input
              v-for="(digit, index) in otpDigits"
              :key="index"
              :ref="el => otpRefs[index] = el"
              v-model="otpDigits[index]"
              @input="handleOTPInput(index, $event)"
              @keydown="handleKeydown(index, $event)"
              @paste="handlePaste"
              type="text"
              inputmode="numeric"
              maxlength="1"
              :class="[
                'w-12 h-14 md:w-14 md:h-16 text-center text-xl md:text-2xl font-semibold',
                'border-2 rounded-lg transition-all duration-200',
                'focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                otpError && otpDigits[index]
                  ? 'border-red-500 bg-red-50 dark:bg-red-900/20'
                  : otpDigits[index]
                    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                    : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800',
                'hover:border-indigo-400'
              ]"
              :disabled="loading"
            >
          </div>

          <!-- Error Message -->
          <div v-if="otpError" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-lg">
            <p class="text-sm flex items-center">
              <i class="fas fa-exclamation-circle mr-2"></i>
              {{ otpError }}
            </p>
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-400 px-4 py-3 rounded-lg">
            <p class="text-sm flex items-center">
              <i class="fas fa-check-circle mr-2"></i>
              {{ successMessage }}
            </p>
          </div>

          <!-- Resend Code -->
          <div class="text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Não recebeu o código?
            </p>
            <button
              type="button"
              @click="resendOTP"
              :disabled="resendTimer > 0 || loading"
              class="mt-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-500
                     disabled:text-gray-400 disabled:cursor-not-allowed
                     font-medium transition-colors duration-200"
            >
              <span v-if="resendTimer > 0">
                Reenviar em {{ resendTimer }}s
              </span>
              <span v-else>
                <i class="fas fa-redo mr-1"></i>
                Reenviar código
              </span>
            </button>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading || !isOTPComplete"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-4 rounded-lg
                   hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2
                   focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50
                   disabled:cursor-not-allowed transform transition-all duration-200
                   hover:scale-105 active:scale-95"
          >
            <span v-if="!loading" class="flex items-center justify-center">
              <i class="fas fa-shield-alt mr-2"></i>
              Verificar Código
            </span>
            <span v-else class="flex items-center justify-center">
              <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Verificando...
            </span>
          </button>

          <!-- Alternative Options -->
          <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-center text-sm text-gray-600 dark:text-gray-400 mb-4">
              Problemas com o código?
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
              <button
                type="button"
                @click="requestCallVerification"
                :disabled="loading"
                class="flex-1 inline-flex justify-center items-center py-2.5 px-4
                       border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                       bg-white dark:bg-gray-800 text-sm font-medium
                       text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700
                       transform transition-all duration-200 hover:scale-105
                       disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-phone mr-2"></i>
                Receber ligação
              </button>

              <button
                type="button"
                @click="tryAnotherMethod"
                :disabled="loading"
                class="flex-1 inline-flex justify-center items-center py-2.5 px-4
                       border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                       bg-white dark:bg-gray-800 text-sm font-medium
                       text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700
                       transform transition-all duration-200 hover:scale-105
                       disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-envelope mr-2"></i>
                Usar email
              </button>
            </div>
          </div>

          <!-- Back to Login -->
          <div class="text-center">
            <a
              href="/login"
              class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
            >
              <i class="fas fa-arrow-left mr-1"></i>
              Voltar ao login
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { useSecurity } from '@/composables/useSecurity'

const route = useRoute()
const router = useRouter()
const auth = useAuth()
const security = useSecurity()

// Props
const phone = ref(route.query.phone || '')

// OTP state
const otpDigits = ref(['', '', '', '', '', ''])
const otpRefs = ref([])
const otpError = ref('')
const successMessage = ref('')
const loading = ref(false)

// Timer
const resendTimer = ref(60)
let timerInterval = null

// Computed
const formattedPhone = computed(() => {
  if (!phone.value) return ''
  const cleaned = phone.value.replace(/\D/g, '')
  if (cleaned.startsWith('55')) {
    const number = cleaned.substring(2)
    return `+55 (${number.substring(0, 2)}) ${number.substring(2, 7)}-${number.substring(7)}`
  }
  return phone.value
})

const isOTPComplete = computed(() => {
  return otpDigits.value.every(digit => digit !== '')
})

// Methods
const handleOTPInput = (index, event) => {
  const value = event.target.value

  // Only allow digits
  if (!/^\d*$/.test(value)) {
    otpDigits.value[index] = ''
    return
  }

  // Clear error when typing
  if (otpError.value) {
    otpError.value = ''
  }

  // Move to next input if digit entered
  if (value && index < 5) {
    otpRefs.value[index + 1]?.focus()
  }

  // Auto-submit when all digits entered
  if (isOTPComplete.value) {
    verifyOTP()
  }
}

const handleKeydown = (index, event) => {
  // Handle backspace
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    otpRefs.value[index - 1]?.focus()
  }

  // Handle arrow keys
  if (event.key === 'ArrowLeft' && index > 0) {
    event.preventDefault()
    otpRefs.value[index - 1]?.focus()
  }

  if (event.key === 'ArrowRight' && index < 5) {
    event.preventDefault()
    otpRefs.value[index + 1]?.focus()
  }
}

const handlePaste = (event) => {
  event.preventDefault()
  const pastedData = event.clipboardData.getData('text')
  const digits = pastedData.replace(/\D/g, '').slice(0, 6).split('')

  digits.forEach((digit, index) => {
    if (index < 6) {
      otpDigits.value[index] = digit
    }
  })

  // Focus last filled input or first empty
  const lastFilledIndex = digits.length - 1
  if (lastFilledIndex >= 0 && lastFilledIndex < 6) {
    otpRefs.value[Math.min(lastFilledIndex + 1, 5)]?.focus()
  }

  // Auto-submit if complete
  if (isOTPComplete.value) {
    verifyOTP()
  }
}

const verifyOTP = async () => {
  if (!isOTPComplete.value) return

  loading.value = true
  otpError.value = ''

  const code = otpDigits.value.join('')

  try {
    const result = await auth.verifyOTP(phone.value, code)

    if (result.success) {
      successMessage.value = 'Código verificado com sucesso!'
      await security.logActivity('otp_verified', 'OTP verification successful')

      // Redirect after short delay
      setTimeout(() => {
        window.location.href = '/dashboard'
      }, 1500)
    } else {
      otpError.value = result.errors?.code?.[0] || 'Código inválido ou expirado'

      // Clear OTP inputs on error
      otpDigits.value = ['', '', '', '', '', '']
      otpRefs.value[0]?.focus()

      await security.logActivity('otp_failed', 'OTP verification failed')
    }
  } catch (error) {
    otpError.value = 'Erro ao verificar código. Tente novamente.'
  } finally {
    loading.value = false
  }
}

const resendOTP = async () => {
  if (resendTimer.value > 0) return

  loading.value = true
  otpError.value = ''
  successMessage.value = ''

  try {
    const result = await auth.loginWithPhone(phone.value)

    if (result.success) {
      successMessage.value = 'Novo código enviado!'
      startResendTimer()

      // Clear inputs
      otpDigits.value = ['', '', '', '', '', '']
      otpRefs.value[0]?.focus()

      await security.logActivity('otp_resent', 'OTP code resent')
    } else {
      otpError.value = 'Erro ao reenviar código. Tente novamente.'
    }
  } catch (error) {
    otpError.value = 'Erro ao reenviar código. Tente novamente.'
  } finally {
    loading.value = false
  }
}

const startResendTimer = () => {
  resendTimer.value = 60

  timerInterval = setInterval(() => {
    resendTimer.value--
    if (resendTimer.value <= 0) {
      clearInterval(timerInterval)
    }
  }, 1000)
}

const requestCallVerification = async () => {
  // Implement voice call verification
  alert('Funcionalidade em desenvolvimento')
}

const tryAnotherMethod = () => {
  router.push('/login')
}

// Lifecycle
onMounted(() => {
  // Focus first input
  otpRefs.value[0]?.focus()

  // Start resend timer
  startResendTimer()

  // Check if phone is provided
  if (!phone.value) {
    router.push('/login')
  }
})

onUnmounted(() => {
  if (timerInterval) {
    clearInterval(timerInterval)
  }
})
</script>

<style scoped>
/* Glass morphism effect */
.glass-morphism {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.dark .glass-morphism {
  background: rgba(26, 32, 44, 0.95);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Remove input spinners */
input[type="text"]::-webkit-outer-spin-button,
input[type="text"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="text"] {
  -moz-appearance: textfield;
}

/* Blob animation */
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
