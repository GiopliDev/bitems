<template>
  <aside class="user-sidebar">
    <div class="user-info">
      <img :src="user.avatar || 'https://via.placeholder.com/54'" alt="User Avatar" class="avatar">
      <h3>{{ user.username }}</h3>
    </div>
    <nav class="sidebar-menu">
      <router-link to="/profile" class="sidebar-link" active-class="active">Profile</router-link>
      <router-link to="/add-item" class="sidebar-link" active-class="active">Add Item</router-link>
      <router-link to="/history" class="sidebar-link" active-class="active">See History</router-link>
      <router-link to="/chats" class="sidebar-link" active-class="active">Chats</router-link>
      <router-link to="/bookmarks" class="sidebar-link" active-class="active">Bookmarks</router-link>
    </nav>
  </aside>
</template>

<script setup>
const props = defineProps({
  user: Object
})
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
}
.sidebar-link:hover, .sidebar-link.active {
  background: #23232b;
  border-left: 4px solid var(--secondary, #03dac6);
  color: var(--secondary, #03dac6);
}
</style>

