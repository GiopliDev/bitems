<template>
  <div class="app">
    <NavBar 
      :user="user" 
      @show-login="showLogin = true" 
      @show-register="showRegister = true" 
    />
    <div class="main-content">
      <UserSidebar v-if="user" :user="user" />
      <main class="content">
        <router-view></router-view>
      </main>
    </div>
    <LoginPopup 
      v-if="showLogin" 
      @close="showLogin = false" 
      @switch-to-register="showRegister = true; showLogin = false"
      @login-success="handleLoginSuccess"
    />
    <RegisterPopup 
      v-if="showRegister" 
      @close="showRegister = false" 
      @switch-to-login="showLogin = true; showRegister = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import NavBar from './components/NavBar.vue'
import UserSidebar from './components/UserSidebar.vue'
import LoginPopup from './components/LoginPopup.vue'
import RegisterPopup from './components/RegisterPopup.vue'
import axios from '@/config/axios'

const showLogin = ref(false)
const showRegister = ref(false)
const user = ref(null)

// Controlla lo stato della sessione al caricamento
onMounted(async () => {
  try {
    const response = await axios.get('/bitems/frontend/backend/session.php', {
      params: { action: 'check' }
    })
    
    if (response.data.isLoggedIn) {
      // Recupera i dati dell'utente
      const userResponse = await axios.get('/bitems/frontend/backend/getProfile.php', {
        params: { id: response.data.userId }
      })
      if (userResponse.data.success) {
        user.value = {
          id: userResponse.data.profile.ute_id,
          username: userResponse.data.profile.ute_username,
          balance: userResponse.data.profile.ute_saldo,
          avatar: userResponse.data.profile.ute_pfpUrl
        }
      }
    }
  } catch (error) {
    console.error('Error checking session:', error)
  }
})

// Gestisce il successo del login
function handleLoginSuccess(userData) {
  user.value = userData
  showLogin.value = false
}
</script>

<style>
:root {
  --primary: #bb86fc;
  --primary-light: #bb86fc;
  --secondary: #03dac6;
  --background: #121212;
  --surface: #1e1e1e;
  --surface-light: #2d2d2d;
  --error: #cf6679;
  --on-primary: #000000;
  --on-secondary: #000000;
  --on-background: #ffffff;
  --on-surface: #ffffff;
  --on-error: #000000;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: var(--background);
  color: var(--on-background);
  line-height: 1.6;
}

.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  display: flex;
  flex: 1;
}

.content {
  flex: 1;
  padding: 2rem;
}

@media (max-width: 768px) {
  .main-content {
    flex-direction: column;
  }
}
</style>
