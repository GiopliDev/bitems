<template>
  <aside class="user-sidebar">
    <div class="user-info">
      <img 
        :src="user?.ute_img_url ? `/bitems/frontend/UploadedImages/${user.ute_img_url}` : '/default-avatar.png'" 
        alt="User Avatar" 
        class="avatar"
      >
      <h3>{{ user?.ute_username || 'User' }}</h3>
    </div>
    <nav class="sidebar-menu">
      <router-link 
        :to="{ 
          path: '/profile',
          query: { id: user?.ute_id }
        }" 
        class="sidebar-link" 
        active-class="active"
      >
        Profile
      </router-link>
      <router-link to="/addItem" class="sidebar-link" active-class="active">Add Item</router-link>
      <router-link to="/history" class="sidebar-link" active-class="active">See History</router-link>
      <router-link to="/chats" class="sidebar-link" active-class="active">Chats</router-link>
      <router-link to="/bookmarks" class="sidebar-link" active-class="active">Bookmarks</router-link>
      <button @click="logout" class="sidebar-link logout-btn">
        Logout
      </button>
    </nav>
  </aside>
</template>

<script setup>
import { useRouter } from 'vue-router'
import axios from '@/config/axios'

const props = defineProps({
  user: Object
})

const router = useRouter()

async function logout() {
  try {
    await axios.get('/bitems/frontend/backend/session.php', {
      params: { action: 'logout' }
    })
    localStorage.removeItem('user')
    router.push('/')
    window.location.reload() // Ricarica la pagina per aggiornare lo stato
  } catch (error) {
    console.error('Error during logout:', error)
  }
}
</script>

<style scoped>
.user-sidebar {
  width: 170px;
  background: #18181c;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  border-right: 3px solid var(--primary-light, #bb86fc);
  padding-top: 1rem;
}
.user-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 2rem;
}
.avatar {
  width: 54px;
  height: 54px;
  border-radius: 50%;
  margin-bottom: 0.7rem;
  border: 2px solid var(--secondary, #03dac6);
  background: #23232b;
  object-fit: cover;
}
.user-info h3 {
  color: #fff;
  font-size: 1.1rem;
  margin: 0;
  font-weight: 600;
}
.sidebar-menu {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.sidebar-link {
  color: #00e6e6;
  padding: 0.85rem 1.2rem;
  text-decoration: none;
  font-weight: 600;
  border-left: 4px solid transparent;
  transition: background 0.2s, border-color 0.2s, color 0.2s;
  background: #23232b;
  margin-bottom: 0.2rem;
  text-align: left;
  border: none;
  cursor: pointer;
  font-size: 1rem;
}
.sidebar-link:hover, .sidebar-link.active {
  background: #23232b;
  border-left: 4px solid var(--secondary, #03dac6);
  color: var(--secondary, #03dac6);
}
.logout-btn {
  margin-top: auto;
  color: var(--error, #cf6679) !important;
  border-left: 4px solid var(--error, #cf6679) !important;
}
.logout-btn:hover {
  background: #23232b;
  color: var(--error, #cf6679) !important;
  border-left: 4px solid var(--error, #cf6679) !important;
}
</style>

