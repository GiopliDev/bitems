<template>
  <!-- No changes to template section -->
</template>

<script>
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

export default {
  setup() {
    const router = useRouter()
    const form = ref({
      username: '',
      password: ''
    })
    const errorMessage = ref('')

    async function handleLogin() {
      try {
        const response = await axios.post('/bitems/frontend/backend/login.php', {
          username: form.value.username,
          password: form.value.password
        })

        if (response.data.success) {
          localStorage.setItem('user', JSON.stringify(response.data.user))
          router.push('/')
        } else {
          errorMessage.value = response.data.error || 'Login failed'
        }
      } catch (error) {
        console.error('Login error:', error)
        errorMessage.value = 'An error occurred during login'
      }
    }

    return {
      form,
      handleLogin,
      errorMessage
    }
  }
}
</script>

<style>
  /* No changes to style section */
</style> 