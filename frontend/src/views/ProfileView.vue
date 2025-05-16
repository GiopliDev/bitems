<template>
  <div class="profile-layout">
    <div class="profile-header">
      <div class="profile-avatar-box">
        <img :src="user.avatar" class="profile-avatar" alt="avatar" />
        <input v-if="editMode" type="file" accept="image/*" @change="onAvatarChange" />
      </div>
      <div class="profile-info">
        <div class="profile-row">
          <span class="profile-label">Nome:</span>
          <span v-if="!editMode">{{ user.name }}</span>
          <input v-else v-model="editUser.name" type="text" />
        </div>
        <div class="profile-row">
          <span class="profile-label">Reputazione:</span>
          <span class="profile-rep">{{ user.reputation }}</span>
        </div>
        <div class="profile-row">
          <span class="profile-label">Descrizione:</span>
          <span v-if="!editMode">{{ user.description }}</span>
          <textarea v-else v-model="editUser.description" rows="3"></textarea>
        </div>
        <div class="profile-actions">
          <button v-if="!editMode" class="main-btn" @click="editMode = true">Modifica profilo</button>
          <button v-else class="main-btn" @click="saveProfile">Salva</button>
          <button v-if="editMode" class="main-btn outline" @click="cancelEdit">Annulla</button>
        </div>
      </div>
    </div>
    <div class="profile-items-section">
      <!-- Solo se l'utente è il proprietario del profilo -->
      <!-- v-if="isMyProfile" -->
      <div class="profile-items-block">
        <h3>Oggetti comprati</h3>
        <div class="profile-items-list">
          <div v-for="item in boughtItems" :key="item.id" class="profile-item">
            <img :src="item.image" alt="item" />
            <div class="profile-item-info">
              <div class="profile-item-title">{{ item.title }}</div>
              <div class="profile-item-game">{{ item.game }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="profile-items-block">
        <h3>Oggetti salvati</h3>
        <div class="profile-items-list">
          <div v-for="item in savedItems" :key="item.id" class="profile-item">
            <img :src="item.image" alt="item" />
            <div class="profile-item-info">
              <div class="profile-item-title">{{ item.title }}</div>
              <div class="profile-item-game">{{ item.game }}</div>
            </div>
          </div>
        </div>
      </div>
      <!-- /v-if -->
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
// Demo dati statici
const user = reactive({
  avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
  name: 'Aiden Chavez',
  reputation: 87,
  description: 'Appassionato di gaming e collezionista di oggetti rari.'
})
const editUser = reactive({
  name: user.name,
  description: user.description,
  avatar: user.avatar
})
const editMode = ref(false)
function onAvatarChange(e) {
  const file = e.target.files[0]
  if (file) {
    const url = URL.createObjectURL(file)
    editUser.avatar = url
  }
}
function saveProfile() {
  user.name = editUser.name
  user.description = editUser.description
  user.avatar = editUser.avatar
  editMode.value = false
  // Qui invia i dati aggiornati al backend
}
function cancelEdit() {
  editUser.name = user.name
  editUser.description = user.description
  editUser.avatar = user.avatar
  editMode.value = false
}
// Demo items
const boughtItems = [
  { id: 1, title: 'Vandal Prime Skin', game: 'Valorant', image: 'https://via.placeholder.com/80x80?text=Item1' },
  { id: 2, title: 'Account Gold IV', game: 'League of Legends', image: 'https://via.placeholder.com/80x80?text=Item2' }
]
const savedItems = [
  { id: 3, title: 'AWP | Dragon Lore', game: 'CS:GO', image: 'https://via.placeholder.com/80x80?text=Item3' },
  { id: 4, title: 'Spectre Nebula', game: 'Valorant', image: 'https://via.placeholder.com/80x80?text=Item4' }
]
// const isMyProfile = true // <-- Da implementare lato backend/autenticazione
</script>

<style scoped>
.profile-layout {
  max-width: 900px;
  margin: 3.5rem auto 2.5rem auto;
  background: var(--surface);
  border-radius: 24px;
  box-shadow: 0 6px 36px #0003;
  padding: 3.2rem 2.7rem 2.7rem 2.7rem;
}
.profile-header {
  display: flex;
  gap: 3.2rem;
  align-items: flex-start;
  margin-bottom: 3.2rem;
}
.profile-avatar-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}
.profile-avatar {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid var(--primary-light);
  background: var(--surface-light);
}
.profile-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.profile-row {
  display: flex;
  align-items: center;
  gap: 1.3rem;
  font-size: 1.18rem;
}
.profile-label {
  color: var(--primary-light);
  font-weight: 700;
  min-width: 120px;
}
.profile-rep {
  color: var(--secondary);
  font-weight: 700;
  font-size: 1.18rem;
}
.profile-info input[type="text"], .profile-info textarea {
  background: var(--surface-light);
  border: 2px solid var(--primary-light);
  border-radius: 10px;
  padding: 0.9rem 1.3rem;
  color: var(--on-surface);
  font-size: 1.13rem;
  outline: none;
  transition: border-color 0.2s;
}
.profile-info input[type="text"]:focus, .profile-info textarea:focus {
  border-color: var(--secondary);
}
.profile-actions {
  display: flex;
  gap: 1.2rem;
  margin-top: 0.9rem;
}
.main-btn {
  padding: 0.9rem 2.1rem;
  border: 2px solid var(--primary-light);
  background: var(--primary-light);
  color: var(--on-primary);
  font-weight: 700;
  border-radius: 10px;
  font-size: 1.13rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.main-btn.outline {
  background: transparent;
  color: var(--primary-light);
  border: 2px solid var(--primary-light);
}
.main-btn.outline:hover {
  background: #222;
  color: var(--secondary);
}
.profile-items-section {
  margin-top: 2.7rem;
  display: flex;
  flex-direction: column;
  gap: 2.7rem;
}
.profile-items-block {
  background: var(--surface-light);
  border-radius: 16px;
  box-shadow: 0 2px 12px #0001;
  padding: 1.7rem 1.5rem 2rem 1.5rem;
}
.profile-items-block h3 {
  color: var(--primary-light);
  font-size: 1.25rem;
  font-weight: 800;
  margin-bottom: 1.3rem;
  letter-spacing: 0.01em;
}
.profile-items-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1.3rem;
}
.profile-item {
  display: flex;
  align-items: center;
  gap: 1.1rem;
  background: var(--surface);
  border-radius: 12px;
  padding: 0.9rem 1.3rem;
  box-shadow: 0 1px 6px #0001;
  min-width: 220px;
  max-width: 320px;
}
.profile-item img {
  width: 60px;
  height: 60px;
  border-radius: 10px;
  object-fit: cover;
  background: var(--surface-light);
}
.profile-item-info {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.profile-item-title {
  font-weight: 700;
  color: var(--on-surface);
  font-size: 1.13rem;
}
.profile-item-game {
  color: var(--primary-light);
  font-size: 1.01rem;
}
</style> 