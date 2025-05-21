<div class="sidebar-section">
  <h3 class="section-title">Account</h3>
  <div class="section-content">
    <router-link to="/profile" class="sidebar-link">
      <i class="fa-regular fa-user"></i>
      <span>Profilo</span>
    </router-link>
    <router-link to="/orders" class="sidebar-link">
      <i class="fa-regular fa-clipboard"></i>
      <span>I miei ordini</span>
    </router-link>
    <router-link to="/sales" class="sidebar-link">
      <i class="fa-regular fa-chart-line"></i>
      <span>Le mie vendite</span>
    </router-link>
    <button v-if="isLoggedIn" @click="logout" class="sidebar-link logout-btn">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Logout</span>
    </button>
    <router-link v-else to="/login" class="sidebar-link">
      <i class="fa-regular fa-right-to-bracket"></i>
      <span>Login</span>
    </router-link>
  </div>
</div>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/config/axios'

const router = useRouter()
const isLoggedIn = computed(() => {
  return localStorage.getItem('user') !== null
})

async function logout() {
  try {
    await axios.get('/bitems/frontend/backend/session.php', {
      params: { action: 'logout' }
    })
    localStorage.removeItem('user')
    router.push('/login')
  } catch (error) {
    console.error('Error during logout:', error)
  }
}
</script>

<style scoped>
.logout-btn {
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  color: var(--error, #cf6679);
}

.logout-btn:hover {
  background: var(--surface-light);
}
</style> 