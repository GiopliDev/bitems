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
          <div class="nav-link dropdown">
            <span class="dropdown-trigger" @click="toggleDropdown">Giochi Affiliati</span>
            <div class="dropdown-menu" v-if="showDropdown">
              <router-link 
                v-for="game in games" 
                :key="game.id"
                :to="{ path: '/catalogo', query: { game: game.name }}"
                class="dropdown-item"
                @click="closeDropdown"
              >
                {{ game.name }}
              </router-link>
            </div>
          </div>
          <a href="/catalogo" class="nav-link">Oggetti Virtuali</a>
          <a href="/top-vendite" class="nav-link">Top Vendite</a>
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

interface Game {
  id: number;
  name: string;
}

interface User {
  ute_username?: string;
  ute_balance?: number;
}

const props = defineProps<{
  user: User | null;
}>();

const router = useRouter()
const games = ref<Game[]>([])
const showDropdown = ref(false)

onMounted(async () => {
  try {
    const response = await axios.get('/bitems/frontend/backend/getGames.php')
    if (response.data.success) {
      games.value = response.data.games
    }
  } catch (error) {
    console.error('Error loading games:', error)
  }
})

function closeDropdown() {
  showDropdown.value = false
}

function toggleDropdown() {
  showDropdown.value = !showDropdown.value
}

const emit = defineEmits(['show-login', 'show-register']);
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

.dropdown {
  position: relative;
  cursor: pointer;
}

.dropdown-trigger {
  color: var(--on-surface);
  font-weight: 600;
  font-size: 1.1rem;
  transition: color 0.2s;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.dropdown-trigger::after {
  content: '▼';
  font-size: 0.8rem;
  transition: transform 0.2s;
}

.dropdown-trigger:hover {
  color: var(--secondary);
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 0.5rem);
  left: 50%;
  transform: translateX(-50%);
  background: var(--surface);
  border: 1px solid var(--primary-light);
  border-radius: 8px;
  padding: 0.5rem 0;
  min-width: 220px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 100;
}

.dropdown-item {
  display: block;
  padding: 0.6rem 1rem;
  color: var(--on-surface);
  text-decoration: none;
  transition: all 0.2s;
  text-align: center;
}

.dropdown-item:hover {
  background: var(--surface-light);
  color: var(--secondary);
}

@media (max-width: 768px) {
  .nav-links, .auth-links {
    display: none;
  }
}
</style>
