import { ref, computed } from 'vue'
import axios from 'axios'

export const useSecurity = () => {
  const loginAttempts = ref(0)
  const lockoutTime = ref(null)
  const requireCaptcha = computed(() => loginAttempts.value >= 3)

  const isLockedOut = computed(() => {
    if (!lockoutTime.value) return false
    return new Date() < new Date(lockoutTime.value)
  })

  const remainingLockoutTime = computed(() => {
    if (!lockoutTime.value) return 0
    const diff = new Date(lockoutTime.value) - new Date()
    return Math.max(0, Math.ceil(diff / 1000))
  })

  const incrementLoginAttempts = () => {
    loginAttempts.value++

    if (loginAttempts.value >= 3) {
      //requireCaptcha.value = true
    }

    if (loginAttempts.value >= 5) {
      lockoutTime.value = new Date(Date.now() + 15 * 60 * 1000) // 15 minutes
    }
  }

  const resetLoginAttempts = () => {
    loginAttempts.value = 0
    lockoutTime.value = null
    //requireCaptcha.value = false
  }

  const generateCaptcha = () => {
    const operations = ['+', '-', '*']
    const operation = operations[Math.floor(Math.random() * operations.length)]
    let num1, num2, answer

    switch (operation) {
      case '+':
        num1 = Math.floor(Math.random() * 50) + 1
        num2 = Math.floor(Math.random() * 50) + 1
        answer = num1 + num2
        break
      case '-':
        num1 = Math.floor(Math.random() * 50) + 20
        num2 = Math.floor(Math.random() * 20) + 1
        answer = num1 - num2
        break
      case '*':
        num1 = Math.floor(Math.random() * 12) + 1
        num2 = Math.floor(Math.random() * 12) + 1
        answer = num1 * num2
        break
    }

    return {
      question: `${num1} ${operation} ${num2} = ?`,
      answer: answer
    }
  }

  const logActivity = async (type, description, properties = {}) => {
    try {
      await axios.post('/api/activity-log', {
        type,
        description,
        properties,
        user_agent: navigator.userAgent,
        timestamp: new Date().toISOString()
      })
    } catch (error) {
      console.error('Failed to log activity:', error)
    }
  }

  const checkSessionSecurity = async () => {
    try {
      const response = await axios.get('/api/session/security-check')
      return response.data
    } catch (error) {
      console.error('Session security check failed:', error)
      return { secure: false }
    }
  }

  return {
  loginAttempts: computed(() => loginAttempts.value),
  isLockedOut,
  remainingLockoutTime,
  requireCaptcha,
  incrementLoginAttempts,
  resetLoginAttempts,
  generateCaptcha,
  logActivity,
  checkSessionSecurity
}

}
