<template>
  <div>
    <header>
      <div class="nav-container">
        <a href="../" class="logo">
          <div class="logo-icon">B</div>
          Bit<span>Items</span>
        </a>
        <nav class="nav-links">
          <a href="/" class="nav-link">Home</a>
          <div class="dropdown">
            <button class="nav-link dropdown-btn">Giochi</button>
            <div class="dropdown-content">
              <a v-for="game in games" 
                 :key="game" 
                 href="#" 
                 class="dropdown-item"
                 @click.prevent="filterByGame(game)">
                {{ game }}
              </a>
            </div>
          </div>
          <a href="/catalogo" class="nav-link">Oggetti Virtuali</a>
          <a href="/top-vendite" class="nav-link">Top Vendite</a>
          <a v-if="user?.ute_username === 'Giopli'" href="/admin" class="nav-link admin">Admin Panel</a>
        </nav>
        <div v-if="user" class="user-info-bar">
          Hi {{ user?.ute_username || 'User' }}!<br>
          Balance: {{ (user?.ute_balance || 0).toFixed(2) }}$
        </div>
        <div class="auth-links" v-else>
          <a href="#" class="auth-btn login-btn" @click.prevent="emit('show-login')">Accedi</a>
          <a href="#" class="auth-btn register-btn" @click.prevent="emit('show-register')">Registrati</a>
        </div>
      </div>
    </header>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/config/axios'

const router = useRouter()
const games = ref<string[]>([])

defineProps({
  user: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['show-login', 'show-register']);

async function loadGames() {
  try {
    const response = await axios.get('/bitems/frontend/backend/getCatalogo.php', {
      params: { action: 'getGames' }
    })
    games.value = response.data
  } catch (error) {
    console.error('Error loading games:', error)
  }
}

function filterByGame(game: string) {
  router.push({
    path: '/catalogo',
    query: { game }
  })
}

onMounted(() => {
  loadGames()
})
</script>

<style scoped>
header {
  background-color: var(--surface);
  padding: 1rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}

.logo {
  font-weight: bold;
  font-size: 1.5rem;
  color: var(--primary-light);
  text-decoration: none;
  display: flex;
  align-items: center;
}

.logo span {
  color: var(--secondary);
}

.logo-icon {
  width: 40px;
  height: 40px;
  margin-right: 10px;
  background-color: var(--primary-light);
  border-radius: 8px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.nav-links {
  display: flex;
  gap: 2rem;
}

.nav-link {
  color: var(--on-surface);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
  position: relative;
}

.nav-link:hover {
  color: var(--primary-light);
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0;
  height: 2px;
  background-color: var(--primary-light);
  transition: width 0.3s;
}

.nav-link:hover::after {
  width: 100%;
}

.auth-links {
  display: flex;
  gap: 1rem;
}

.auth-btn {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.3s;
}

.login-btn {
  background-color: transparent;
  color: var(--on-surface);
  border: 1px solid var(--primary-light);
}

.login-btn:hover {
  background-color: rgba(187, 134, 252, 0.1);
}

.register-btn {
  background-color: var(--primary);
  color: var(--on-primary);
  border: none;
}

.register-btn:hover {
  background-color: var(--primary-light);
  box-shadow: 0 2px 8px rgba(187, 134, 252, 0.4);
}

.user-info-bar {
  color: var(--on-surface);
  font-size: 1rem;
  text-align: right;
  font-weight: 500;
  margin-left: 1rem;
}

@media (max-width: 768px) {
  .nav-links, .auth-links {
    display: none;
  }
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  color: var(--on-surface);
  transition: color 0.3s;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: var(--surface);
  min-width: 160px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  border-radius: 4px;
  z-index: 1;
  padding: 0.5rem 0;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown-item {
  color: var(--on-surface);
  padding: 0.5rem 1rem;
  text-decoration: none;
  display: block;
  transition: background-color 0.2s;
}

.dropdown-item:hover {
  background-color: var(--surface-light);
  color: var(--primary-light);
}

.nav-link.admin {
  color: var(--secondary);
}

.nav-link.admin:hover {
  color: var(--primary-light);
}
</style>
