<!-- resources/js/components/auth/LoginForm.vue -->
<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 p-4"
  >
    <!-- Background Animation -->
    <div class="absolute inset-0 overflow-hidden">
      <div
        class="absolute -top-10 -left-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"
      ></div>
      <div
        class="absolute -bottom-10 -right-10 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"
      ></div>
      <div
        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"
      ></div>
    </div>

    <!-- Login Card -->
    <div class="relative w-full max-w-md">
      <div class="glass-morphism rounded-2xl shadow-2xl p-8 backdrop-blur-lg">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
          <img
            src="/assets/img/logo.png"
            alt="Bia Soluções"
            class="h-12 mx-auto mb-4 filter drop-shadow-lg"
          />
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Bem-vindo de volta!</h2>
          <p class="text-gray-600 dark:text-gray-300 mt-2">Entre para continuar sua jornada</p>
        </div>

        <!-- Login Method Toggle -->
        <div class="flex justify-center mb-6">
          <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-1 flex">
            <button
              type="button"
              @click="loginMethod = 'email'"
              :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition-all duration-200',
                loginMethod === 'email'
                  ? 'bg-white dark:bg-gray-700 shadow text-indigo-600 dark:text-indigo-400'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700',
              ]"
            >
              <i class="fas fa-envelope mr-2"></i>
              Email
            </button>
            <button
              type="button"
              @click="loginMethod = 'phone'"
              :class="[
                'px-4 py-2 rounded-md text-sm font-medium transition-all duration-200',
                loginMethod === 'phone'
                  ? 'bg-white dark:bg-gray-700 shadow text-indigo-600 dark:text-indigo-400'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700',
              ]"
            >
              <i class="fas fa-phone mr-2"></i>
              Telefone
            </button>
          </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Email Login -->
          <template v-if="loginMethod === 'email'">
            <!-- Email Input -->
            <div class="relative">
              <div class="relative">
                <input
                  type="email"
                  v-model="email.value.value"
                  @blur="email.touched = true"
                  id="email"
                  :class="[
                    'peer w-full px-4 py-3 bg-white dark:bg-gray-800 border rounded-lg',
                    'focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                    'transition-all duration-200',
                    email.error && email.touched
                      ? 'border-red-500'
                      : 'border-gray-300 dark:border-gray-600',
                  ]"
                  placeholder=" "
                  required
                />
                <label
                  for="email"
                  class="absolute left-3 -top-2.5 bg-white dark:bg-gray-900 px-1 text-sm text-gray-600 dark:text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-sm peer-focus:text-indigo-600"
                >
                  Email
                </label>
              </div>
              <p v-if="email.error && email.touched" class="mt-1 text-sm text-red-500">
                {{ email.error }}
              </p>
            </div>

            <!-- Password Input -->
            <div class="relative">
              <div class="relative">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model="password.value.value"
                  @blur="password.touched = true"
                  id="password"
                  :class="[
                    'peer w-full px-4 py-3 pr-12 bg-white dark:bg-gray-800 border rounded-lg',
                    'focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                    'transition-all duration-200',
                    password.error && password.touched
                      ? 'border-red-500'
                      : 'border-gray-300 dark:border-gray-600',
                  ]"
                  placeholder=" "
                  required
                />
                <label
                  for="password"
                  class="absolute left-3 -top-2.5 bg-white dark:bg-gray-900 px-1 text-sm text-gray-600 dark:text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-sm peer-focus:text-indigo-600"
                >
                  Senha
                </label>
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-3.5 text-gray-500 hover:text-gray-700 focus:outline-none"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <p v-if="password.error && password.touched" class="mt-1 text-sm text-red-500">
                {{ password.error }}
              </p>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="remember"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                />
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Lembrar de mim</span>
              </label>
              <a
                href="/forgot-password"
                class="text-sm text-indigo-600 hover:text-indigo-500 transition-colors"
              >
                Esqueceu a senha?
              </a>
            </div>
          </template>

          <!-- Phone Login -->
          <template v-else>
            <div class="relative">
              <div class="flex">
                <select
                  v-model="countryCode"
                  class="px-3 py-3 bg-gray-50 dark:bg-gray-800 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l-lg focus:ring-2 focus:ring-indigo-500"
                >
                  <option value="+55">🇧🇷 +55</option>
                  <option value="+1">🇺🇸 +1</option>
                  <option value="+351">🇵🇹 +351</option>
                </select>
                <div class="relative flex-1">
                  <input
                    type="tel"
                    v-model="phone.value.value"
                    @blur="phone.touched = true"
                    @input="formatPhone"
                    id="phone"
                    :class="[
                      'peer w-full px-4 py-3 bg-white dark:bg-gray-800 border rounded-r-lg',
                      'focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                      'transition-all duration-200',
                      phone.error && phone.touched
                        ? 'border-red-500'
                        : 'border-gray-300 dark:border-gray-600',
                    ]"
                    placeholder=" "
                    required
                  />
                  <label
                    for="phone"
                    class="absolute left-3 -top-2.5 bg-white dark:bg-gray-900 px-1 text-sm text-gray-600 dark:text-gray-400 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-3 peer-placeholder-shown:left-4 peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-sm peer-focus:text-indigo-600"
                  >
                    Telefone
                  </label>
                </div>
              </div>
              <p v-if="phone.error && phone.touched" class="mt-1 text-sm text-red-500">
                {{ phone.error }}
              </p>
            </div>
          </template>

          <!-- CAPTCHA -->
          <div v-if="security.requireCaptcha" class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
              Por segurança, resolva o cálculo:
            </p>
            <div class="flex items-center space-x-3">
              <span
                class="font-mono text-lg bg-white dark:bg-gray-700 px-4 py-2 rounded border border-gray-300 dark:border-gray-600"
              >
                <span v-if="captcha.value && captcha.value.question">
                  {{ captcha.value.question }}
                </span>
              </span>
              <input
                type="number"
                v-model.number="captchaAnswer"
                class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"
                placeholder="?"
              />
            </div>
          </div>

          <!-- Error Messages -->
          <div
            v-if="auth.errors.general"
            class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg"
          >
            <p class="text-sm">{{ auth.errors.general[0] }}</p>
          </div>

          <!-- Lockout Message -->
          <div
            v-if="security.isLockedOut"
            class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg"
          >
            <p class="text-sm">
              Muitas tentativas. Tente novamente em {{ security.remainingLockoutTime }} segundos.
            </p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="auth.loading.value || security.isLockedOut.value"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transform transition-all duration-200 hover:scale-105 active:scale-95"
          >
            <span v-if="!auth.loading.value" class="flex items-center justify-center">
              <i
                :class="loginMethod === 'phone' ? 'fas fa-paper-plane' : 'fas fa-sign-in-alt'"
                class="mr-2"
              ></i>
              {{ loginMethod === 'phone' ? 'Enviar código' : 'Entrar' }}
            </span>
            <span v-else class="flex items-center justify-center">
              <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                  fill="none"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Processando...
            </span>
          </button>

          <!-- Social Login -->
          <div class="mt-6">
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white dark:bg-gray-900 text-gray-500">Ou continue com</span>
              </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3">
              <button
                type="button"
                @click="socialLogin('google')"
                class="w-full inline-flex justify-center py-2.5 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transform transition-all duration-200 hover:scale-105"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                  <path
                    fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                  />
                  <path
                    fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  />
                  <path
                    fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                  />
                  <path
                    fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  />
                </svg>
                <span class="ml-2">Google</span>
              </button>

              <button
                type="button"
                @click="socialLogin('microsoft')"
                class="w-full inline-flex justify-center py-2.5 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transform transition-all duration-200 hover:scale-105"
              >
                <svg class="w-5 h-5" viewBox="0 0 21 21">
                  <rect x="1" y="1" width="9" height="9" fill="#f25022" />
                  <rect x="1" y="11" width="9" height="9" fill="#00a4ef" />
                  <rect x="11" y="1" width="9" height="9" fill="#7fba00" />
                  <rect x="11" y="11" width="9" height="9" fill="#ffb900" />
                </svg>
                <span class="ml-2">Microsoft</span>
              </button>
            </div>
          </div>

          <!-- Sign Up Link -->
          <div class="text-center mt-6">
            <span class="text-gray-600 dark:text-gray-400">Não tem uma conta?</span>
            <a
              href="/register"
              class="text-indigo-600 hover:text-indigo-500 font-medium ml-1 transition-colors"
            >
              Cadastre-se gratuitamente
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import { useAuth } from '@/composables/useAuth';
  import { useValidation } from '@/composables/useValidation';
  import { useSecurity } from '@/composables/useSecurity';

  const router = useRouter();
  const auth = useAuth();
  const { rules, createFieldValidator } = useValidation();
  const security = useSecurity();

  // Form state
  const loginMethod = ref('email');
  const showPassword = ref(false);
  const remember = ref(false);
  const countryCode = ref('+55');

  const emailSimple = ref('');
  const passwordSimple = ref('');
  const phoneSimple = ref('');

  // Field validators
  const email = createFieldValidator('', [rules.required, rules.email]);
  const password = createFieldValidator('', [rules.required, rules.minLength(6)]);
  const phone = createFieldValidator('', [rules.required, rules.phone]);

  // CAPTCHA
  const captcha = ref({ question: '', answer: 0 });
  const captchaAnswer = ref('');

  // Debug - verificar estrutura dos campos
  console.log('Email field:', email);
  console.log('Password field:', password);
  console.log('Phone field:', phone);

  // Generate CAPTCHA when needed
  watch(
    () => security.requireCaptcha.value,
    val => {
      if (val) {
        captcha.value = security.generateCaptcha();
      }
    },
    { immediate: true }
  );

  // Format phone number
  const formatPhone = () => {
    let value = phone.value.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);

    if (value.length > 6) {
      value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
    } else if (value.length > 2) {
      value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    }

    phone.value.value = value;
  };

  // Handle login
  const handleLogin = async () => {
    console.log('=== DEBUG handleLogin ===');
    console.log('Login method:', loginMethod.value);
    console.log('Email value:', email.value);
    console.log('Password value:', password.value);
    console.log('Phone value:', phone.value);
    console.log('Auth loading before:', auth.loading);
    console.log('Captcha esperado:', captcha.value.answer);
    console.log('Captcha inserido:', captchaAnswer.value);

    if (security.isLockedOut.value) return;
    console.log('User is locked out');
    // Validate fields
    let isValid = true;

    if (loginMethod.value === 'email') {
      isValid = email.validate() && password.validate();
    } else {
      isValid = phone.validate();
    }

    if (!isValid) return;

    // Check CAPTCHA
    if (security.requireCaptcha.value && captchaAnswer.value != captcha.value.answer) {
      auth.errors.value = { general: ['CAPTCHA incorreto'] };
      captcha.value = security.generateCaptcha();
      captchaAnswer.value = '';
      return;
    }

    try {
      // Log activity
      await security.logActivity('login_attempt', `Login attempt via ${loginMethod.value}`);

      let result;

      if (loginMethod.value === 'email') {
        result = await auth.login({
          email: email.value.value, // Note: .value.value porque email é um objeto reativo
          password: password.value.value, // Note: .value.value
          remember: remember.value,
        });
        console.log('Result from auth.login:', result);  // Adicione isso
      } else {
        const cleanPhone = phone.value.value.replace(/\D/g, '');
        result = await auth.loginWithPhone(cleanPhone, countryCode.value);
      }

      if (result.success) {
        security.resetLoginAttempts();

        if (loginMethod.value === 'phone') {
          // Redirect to OTP verification
          router.push({
            name: 'verify-otp',
            query: { phone: countryCode.value + phone.value.value.replace(/\D/g, '') },
          });
        } else {
          console.log('Success! Redirecting...');
          // Redirect to dashboard
          window.location.href = '/dashboard';
        }
      } else {
        security.incrementLoginAttempts();

        // Handle specific errors
        if (result.errors) {
          if (result.errors.email) email.error.value = result.errors.email[0];
          if (result.errors.password) password.error.value = result.errors.password[0];
          if (result.errors.phone) phone.error.value = result.errors.phone[0];

          // Se houver erro geral, ele já está em auth.errors
          if (result.errors.general) {
            auth.errors.value = result.errors;
          }
        }
      }
    } catch (error) {
      console.error('Login error:', error);
      auth.errors.value = { general: ['Erro ao fazer login. Tente novamente.'] };
    }
  };

  // Social login
  const socialLogin = async provider => {
    await security.logActivity('social_login_attempt', `Social login attempt via ${provider}`);
    window.location.href = `/auth/${provider}`;
  };

  // Dark mode
  const darkMode = ref(false);

  onMounted(() => {
    // Check for saved theme preference or default to light
    darkMode.value = localStorage.getItem('darkMode') === 'true';
    if (darkMode.value) {
      document.documentElement.classList.add('dark');
    }

    console.log('=== DEBUG FIELDS ===');
    console.log('email object:', email);
    console.log('email.value:', email.value);
    console.log('typeof email.value:', typeof email.value);

    // Vamos verificar a estrutura real
    console.log('email keys:', Object.keys(email));
    if (email.value) {
      console.log('email.value keys:', Object.keys(email.value));
    }

    const interval = setInterval(() => {
      if (!security.isLockedOut.value) {
        clearInterval(interval);
      }
    }, 1000);
  });
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
