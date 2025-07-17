<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 p-4">
    <!-- Background Animation -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-10 -left-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
      <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Register Card -->
    <div class="relative w-full max-w-4xl">
      <div class="glass-morphism rounded-2xl shadow-2xl p-8 backdrop-blur-lg">
        <!-- Progress Steps -->
        <div class="mb-8">
          <div class="flex items-center justify-center">
            <div v-for="(step, index) in steps" :key="index" class="flex items-center">
              <div
                :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300',
                  currentStep >= index + 1
                    ? 'bg-indigo-600 text-white'
                    : 'bg-gray-300 text-gray-600'
                ]"
              >
                <i v-if="currentStep > index + 1" class="fas fa-check"></i>
                <span v-else>{{ index + 1 }}</span>
              </div>
              <div
                v-if="index < steps.length - 1"
                :class="[
                  'w-20 h-1 mx-2 transition-all duration-300',
                  currentStep > index + 1 ? 'bg-indigo-600' : 'bg-gray-300'
                ]"
              ></div>
            </div>
          </div>
          <div class="flex justify-between mt-2">
            <span
              v-for="(step, index) in steps"
              :key="`label-${index}`"
              :class="[
                'text-xs font-medium',
                currentStep === index + 1 ? 'text-indigo-600' : 'text-gray-500'
              ]"
            >
              {{ step }}
            </span>
          </div>
        </div>

        <!-- Form Content -->
        <form @submit.prevent="handleSubmit">
          <!-- Step 1: Account Info -->
          <div v-show="currentStep === 1" class="space-y-6 animate-fade-in">
            <div class="text-center mb-6">
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                Crie sua conta
              </h2>
              <p class="text-gray-600 dark:text-gray-300 mt-2">
                Comece sua jornada conosco
              </p>
            </div>

            <!-- Name -->
            <div>
              <label class="form-label">Nome completo</label>
              <input
                type="text"
                v-model="form.name"
                class="form-input"
                placeholder="João Silva"
                required
              >
              <p v-if="errors.name" class="form-error">{{ errors.name[0] }}</p>
            </div>

            <!-- Email -->
            <div>
              <label class="form-label">Email</label>
              <input
                type="email"
                v-model="form.email"
                class="form-input"
                placeholder="joao@exemplo.com"
                required
              >
              <p v-if="errors.email" class="form-error">{{ errors.email[0] }}</p>
            </div>

            <!-- Phone -->
            <div>
              <label class="form-label">Telefone (opcional)</label>
              <div class="flex">
                <select v-model="form.countryCode" class="px-3 py-3 bg-gray-50 dark:bg-gray-800 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l-lg">
                  <option value="+55">🇧🇷 +55</option>
                  <option value="+1">🇺🇸 +1</option>
                  <option value="+351">🇵🇹 +351</option>
                </select>
                <input
                  type="tel"
                  v-model="form.phone"
                  @input="formatPhone"
                  class="flex-1 px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-r-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                  placeholder="(11) 98765-4321"
                >
              </div>
              <p v-if="errors.phone" class="form-error">{{ errors.phone[0] }}</p>
            </div>

            <!-- Password -->
            <div>
              <label class="form-label">Senha</label>
              <div class="relative">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model="form.password"
                  @input="checkPasswordStrength"
                  class="form-input pr-12"
                  placeholder="Mínimo 8 caracteres"
                  required
                >
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-3.5 text-gray-500 hover:text-gray-700"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>

              <!-- Password Strength -->
              <div class="mt-2">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-xs text-gray-600">Força da senha</span>
                  <span :class="[
                    'text-xs font-medium',
                    passwordStrength.color === 'red' ? 'text-red-600' : '',
                    passwordStrength.color === 'yellow' ? 'text-yellow-600' : '',
                    passwordStrength.color === 'green' ? 'text-green-600' : ''
                  ]">
                    {{ passwordStrength.message }}
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    :class="[
                      'h-2 rounded-full transition-all duration-300',
                      passwordStrength.color === 'red' ? 'bg-red-500' : '',
                      passwordStrength.color === 'yellow' ? 'bg-yellow-500' : '',
                      passwordStrength.color === 'green' ? 'bg-green-500' : ''
                    ]"
                    :style="`width: ${passwordStrength.strength}%`"
                  ></div>
                </div>
              </div>
              <p v-if="errors.password" class="form-error">{{ errors.password[0] }}</p>
            </div>

            <!-- Password Confirmation -->
            <div>
              <label class="form-label">Confirme a senha</label>
              <input
                :type="showPassword ? 'text' : 'password'"
                v-model="form.password_confirmation"
                class="form-input"
                placeholder="Digite a senha novamente"
                required
              >
            </div>

            <!-- Terms -->
            <div>
              <label class="flex items-start">
                <input
                  type="checkbox"
                  v-model="form.acceptTerms"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mt-1"
                  required
                >
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                  Concordo com os <a href="#" class="text-indigo-600 hover:text-indigo-500">Termos de Uso</a>
                  e <a href="#" class="text-indigo-600 hover:text-indigo-500">Política de Privacidade</a>
                </span>
              </label>
            </div>
          </div>

          <!-- Step 2: Workspace -->
          <div v-show="currentStep === 2" class="space-y-6 animate-fade-in">
            <div class="text-center mb-6">
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                Configure seu espaço de trabalho
              </h2>
              <p class="text-gray-600 dark:text-gray-300 mt-2">
                Personalize sua experiência (opcional)
              </p>
            </div>

            <!-- Workspace Type -->
            <div>
              <label class="form-label">Como você usará a plataforma?</label>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                <label
                  :class="[
                    'relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all',
                    workspaceType === 'personal'
                      ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                      : 'border-gray-300 dark:border-gray-600 hover:border-gray-400'
                  ]"
                >
                  <input
                    type="radio"
                    v-model="workspaceType"
                    value="personal"
                    class="sr-only"
                  >
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-800 rounded-lg flex items-center justify-center mr-4">
                      <i class="fas fa-user text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <div>
                      <h3 class="font-semibold text-gray-800 dark:text-white">Uso Pessoal</h3>
                      <p class="text-sm text-gray-600 dark:text-gray-400">Para projetos individuais</p>
                    </div>
                  </div>
                </label>

                <label
                  :class="[
                    'relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all',
                    workspaceType === 'team'
                      ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                      : 'border-gray-300 dark:border-gray-600 hover:border-gray-400'
                  ]"
                >
                  <input
                    type="radio"
                    v-model="workspaceType"
                    value="team"
                    class="sr-only"
                  >
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center mr-4">
                      <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                      <h3 class="font-semibold text-gray-800 dark:text-white">Equipe/Empresa</h3>
                      <p class="text-sm text-gray-600 dark:text-gray-400">Colabore com sua equipe</p>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Workspace Name (if team) -->
            <div v-if="workspaceType === 'team'" class="animate-fade-in">
              <label class="form-label">Nome da empresa/equipe</label>
              <input
                type="text"
                v-model="form.workspace_name"
                class="form-input"
                placeholder="Minha Empresa"
              >
              <p v-if="errors.workspace_name" class="form-error">{{ errors.workspace_name[0] }}</p>
            </div>

            <!-- Team Size (if team) -->
            <div v-if="workspaceType === 'team'" class="animate-fade-in">
              <label class="form-label">Tamanho da equipe</label>
              <select v-model="teamSize" class="form-input">
                <option value="1-5">1-5 pessoas</option>
                <option value="6-20">6-20 pessoas</option>
                <option value="21-50">21-50 pessoas</option>
                <option value="50+">Mais de 50 pessoas</option>
              </select>
            </div>

            <!-- Skip Option -->
            <div class="text-center text-sm text-gray-600 dark:text-gray-400">
              <button
                type="button"
                @click="skipWorkspace"
                class="hover:text-indigo-600 transition-colors"
              >
                Pular esta etapa e configurar depois →
              </button>
            </div>
          </div>

          <!-- Step 3: Plans & Modules -->
          <div v-show="currentStep === 3" class="space-y-6 animate-fade-in">
            <div class="text-center mb-6">
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                Escolha seu plano
              </h2>
              <p class="text-gray-600 dark:text-gray-300 mt-2">
                Selecione o melhor plano para suas necessidades
              </p>
            </div>

            <!-- Plans -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div
                v-for="plan in plans"
                :key="plan.id"
                :class="[
                  'relative border-2 rounded-lg p-6 cursor-pointer transition-all transform hover:scale-105',
                  form.plan_id === plan.id
                    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                    : 'border-gray-300 dark:border-gray-600',
                  plan.is_popular ? 'ring-2 ring-indigo-500' : ''
                ]"
                @click="form.plan_id = plan.id"
              >
                <!-- Popular Badge -->
                <div v-if="plan.is_popular" class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                  <span class="bg-indigo-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                    Mais Popular
                  </span>
                </div>

                <div class="text-center">
                  <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ plan.name }}</h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ plan.description }}</p>

                  <div class="mt-4">
                    <span class="text-3xl font-bold text-gray-800 dark:text-white">
                      R$ {{ plan.price }}
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">/mês</span>
                  </div>

                  <ul class="mt-6 space-y-3 text-sm">
                    <li class="flex items-center text-gray-700 dark:text-gray-300">
                      <i class="fas fa-check text-green-500 mr-2"></i>
                      {{ plan.max_users === -1 ? 'Usuários ilimitados' : `Até ${plan.max_users} usuários` }}
                    </li>
                    <li v-for="(value, feature) in plan.features" :key="feature" class="flex items-center text-gray-700 dark:text-gray-300">
                      <i class="fas fa-check text-green-500 mr-2"></i>
                      {{ formatFeature(feature, value) }}
                    </li>
                  </ul>

                  <div v-if="plan.trial_days > 0" class="mt-4">
                    <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                      {{ plan.trial_days }} dias grátis
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modules Selection -->
            <div v-if="workspaceType === 'team'" class="mt-8">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                Módulos disponíveis
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label
                  v-for="module in modules"
                  :key="module.id"
                  :class="[
                    'flex items-center p-4 border rounded-lg cursor-pointer transition-all',
                    form.modules.includes(module.id)
                      ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                      : 'border-gray-300 dark:border-gray-600 hover:border-gray-400'
                  ]"
                >
                  <input
                    type="checkbox"
                    :value="module.id"
                    v-model="form.modules"
                    class="sr-only"
                  >
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center mr-3">
                      <i :class="[module.icon, 'text-indigo-600 dark:text-indigo-400']"></i>
                    </div>
                    <div>
                      <h4 class="font-medium text-gray-800 dark:text-white">{{ module.name }}</h4>
                      <p class="text-xs text-gray-600 dark:text-gray-400">{{ module.description }}</p>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex justify-between mt-8">
            <button
              v-if="currentStep > 1"
              type="button"
              @click="prevStep"
              class="btn-secondary"
            >
              <i class="fas fa-arrow-left mr-2"></i>
              Voltar
            </button>
            <div v-else></div>

            <button
              v-if="currentStep < 3"
              type="button"
              @click="nextStep"
              class="btn-primary"
              :disabled="!canProceed"
            >
              Próximo
              <i class="fas fa-arrow-right ml-2"></i>
            </button>

            <button
              v-else
              type="submit"
              class="btn-primary"
              :disabled="loading || !canProceed"
            >
              <span v-if="!loading" class="flex items-center">
                <i class="fas fa-rocket mr-2"></i>
                Criar conta
              </span>
              <span v-else class="flex items-center">
                <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Criando...
              </span>
            </button>
          </div>

          <!-- Login Link -->
          <div class="text-center mt-6">
            <span class="text-gray-600 dark:text-gray-400">Já tem uma conta?</span>
            <a
              href="/login"
              class="text-indigo-600 hover:text-indigo-500 font-medium ml-1 transition-colors"
            >
              Fazer login
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { usePasswordStrength } from '@/composables/usePasswordStrength'

const router = useRouter()
const auth = useAuth()

// Form state
const currentStep = ref(1)
const steps = ['Dados pessoais', 'Workspace', 'Plano']
const showPassword = ref(false)
const workspaceType = ref('personal')
const teamSize = ref('1-5')
const loading = ref(false)

const form = ref({
  name: '',
  email: '',
  phone: '',
  countryCode: '+55',
  password: '',
  password_confirmation: '',
  acceptTerms: false,
  workspace_name: '',
  plan_id: null,
  modules: []
})

const errors = ref({})

// Mock data (will come from API)
const plans = ref([
  {
    id: 1,
    name: 'Básico',
    description: 'Ideal para começar',
    price: '0,00',
    max_users: 5,
    trial_days: 0,
    is_popular: false,
    features: {
      boards_limit: 3,
      storage_gb: 1,
      file_size_mb: 10,
      api_access: false,
      custom_domain: false,
      priority_support: false
    }
  },
  {
    id: 2,
    name: 'Profissional',
    description: 'Para equipes em crescimento',
    price: '49,90',
    max_users: 20,
    trial_days: 30,
    is_popular: true,
    features: {
      boards_limit: -1,
      storage_gb: 10,
      file_size_mb: 50,
      api_access: true,
      custom_domain: false,
      priority_support: true
    }
  },
  {
    id: 3,
    name: 'Empresarial',
    description: 'Recursos completos',
    price: '149,90',
    max_users: -1,
    trial_days: 30,
    is_popular: false,
    features: {
      boards_limit: -1,
      storage_gb: 100,
      file_size_mb: 200,
      api_access: true,
      custom_domain: true,
      priority_support: true,
      dedicated_account_manager: true,
      sso: true,
      audit_logs: true
    }
  }
])

const modules = ref([
  {
    id: 2,
    name: 'Financeiro',
    slug: 'financeiro',
    description: 'Controle financeiro completo',
    icon: 'fas fa-dollar-sign'
  },
  {
    id: 3,
    name: 'RH',
    slug: 'rh',
    description: 'Gestão de pessoas',
    icon: 'fas fa-users'
  },
  {
    id: 4,
    name: 'CRM',
    slug: 'crm',
    description: 'Relacionamento com clientes',
    icon: 'fas fa-handshake'
  }
])

// Password strength
const passwordStrength = usePasswordStrength(computed(() => form.value.password))

// Methods
const canProceed = computed(() => {
  switch (currentStep.value) {
    case 1:
      return form.value.name &&
             form.value.email &&
             form.value.password &&
             form.value.password_confirmation &&
             form.value.password === form.value.password_confirmation &&
             form.value.acceptTerms
    case 2:
      return workspaceType.value === 'personal' || form.value.workspace_name
    case 3:
      return form.value.plan_id !== null
    default:
      return false
  }
})

const nextStep = () => {
  if (canProceed.value && currentStep.value < 3) {
    currentStep.value++
  }
}

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

const skipWorkspace = () => {
  workspaceType.value = 'personal'
  form.value.workspace_name = ''
  currentStep.value = 3
}

const formatPhone = () => {
  let value = form.value.phone.replace(/\D/g, '')
  if (value.length > 11) value = value.slice(0, 11)

  if (value.length > 6) {
    value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3')
  } else if (value.length > 2) {
    value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2')
  }

  form.value.phone = value
}

const checkPasswordStrength = () => {
  // Handled by composable
}

const formatFeature = (feature, value) => {
  const featureMap = {
    boards_limit: value === -1 ? 'Quadros ilimitados' : `${value} quadros`,
    storage_gb: `${value} GB de armazenamento`,
    file_size_mb: `Arquivos até ${value} MB`,
    api_access: 'Acesso à API',
    custom_domain: 'Domínio personalizado',
    priority_support: 'Suporte prioritário',
    dedicated_account_manager: 'Gerente de conta dedicado',
    sso: 'Single Sign-On (SSO)',
    audit_logs: 'Logs de auditoria'
  }

  return featureMap[feature] || feature
}

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  // Prepare data
  const data = {
    ...form.value,
    phone: form.value.phone ? form.value.countryCode + form.value.phone.replace(/\D/g, '') : null
  }

  // Only include workspace name if team type selected
  if (workspaceType.value === 'personal') {
    delete data.workspace_name
    data.modules = []
  }

  try {
    const result = await auth.register(data)

    if (result.success) {
      // Show success message
      await showSuccessAnimation()

      // Redirect to onboarding or dashboard
      setTimeout(() => {
        window.location.href = result.redirect || '/onboarding'
      }, 1500)
    } else {
      errors.value = result.errors || {}

      // Go back to step with error
      if (errors.value.email || errors.value.password) {
        currentStep.value = 1
      } else if (errors.value.workspace_name) {
        currentStep.value = 2
      }
    }
  } catch (error) {
    console.error('Registration error:', error)
    errors.value = { general: ['Erro ao criar conta. Tente novamente.'] }
  } finally {
    loading.value = false
  }
}

const showSuccessAnimation = () => {
  return new Promise(resolve => {
    // You can implement a success animation here
    resolve()
  })
}

// Set default plan on mount
onMounted(() => {
  // Select the free plan by default
  const freePlan = plans.value.find(p => p.price === '0,00')
  if (freePlan) {
    form.value.plan_id = freePlan.id
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

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
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
