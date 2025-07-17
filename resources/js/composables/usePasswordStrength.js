import { computed } from 'vue'

export const usePasswordStrength = (password) => {
  const strength = computed(() => {
    if (!password.value) return 0

    let score = 0

    // Length
    if (password.value.length >= 8) score += 20
    if (password.value.length >= 12) score += 20

    // Complexity
    if (/[a-z]/.test(password.value)) score += 20
    if (/[A-Z]/.test(password.value)) score += 20
    if (/[0-9]/.test(password.value)) score += 20
    if (/[^A-Za-z0-9]/.test(password.value)) score += 20

    return Math.min(score, 100)
  })

  const level = computed(() => {
    const s = strength.value
    if (s === 0) return null
    if (s < 40) return 'weak'
    if (s < 70) return 'medium'
    return 'strong'
  })

  const color = computed(() => {
    const l = level.value
    if (!l) return 'gray'
    if (l === 'weak') return 'red'
    if (l === 'medium') return 'yellow'
    return 'green'
  })

  const message = computed(() => {
    const l = level.value
    if (!l) return ''
    if (l === 'weak') return 'Senha fraca'
    if (l === 'medium') return 'Senha média'
    return 'Senha forte'
  })

  return {
    strength,
    level,
    color,
    message
  }
}
