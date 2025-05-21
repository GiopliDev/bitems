<template>
  <div class="session-manager">
    <button v-if="isLoggedIn" class="logout-btn" @click="logout">
      <span class="icon">🚪</span>
      Logout
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/config/axios'

const router = useRouter()
const isLoggedIn = ref(false)

onMounted(async () => {
  try {
    const response = await axios.get('/bitems/frontend/backend/session.php', {
      params: { action: 'check' }
    })
    isLoggedIn.value = response.data.isLoggedIn
  } catch (error) {
    console.error('Error checking session:', error)
    isLoggedIn.value = false
  }
})

async function logout() {
  try {
    await axios.get('/bitems/frontend/backend/session.php', {
      params: { action: 'logout' }
    })
    isLoggedIn.value = false
    router.push('/login')
  } catch (error) {
    console.error('Error during logout:', error)
  }
}
</script>

<style scoped>
.session-manager {
  position: fixed;
  bottom: 2rem;
  left: 2rem;
  z-index: 100;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.5rem;
  background: var(--error, #cf6679);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.logout-btn:hover {
  background: var(--error-dark, #b4495f);
}

.icon {
  font-size: 1.2rem;
}
</style> 