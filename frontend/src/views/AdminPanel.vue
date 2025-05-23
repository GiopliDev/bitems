<template>
  <div class="admin-panel">
    <div class="admin-header">
      <h1>Pannello Amministratore</h1>
      <div class="admin-stats">
        <div class="stat-card">
          <span class="emoji">👥</span>
          <div class="stat-info">
            <h3>Utenti Totali</h3>
            <p>{{ totalUsers }}</p>
          </div>
        </div>
        <div class="stat-card">
          <span class="emoji">🛍️</span>
          <div class="stat-info">
            <h3>Articoli Totali</h3>
            <p>{{ totalItems }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Tabs -->
    <div class="tabs">
      <button 
        :class="['tab-btn', { active: activeTab === 'users' }]"
        @click="activeTab = 'users'"
      >
        <span class="emoji">👥</span>
        Gestione Utenti
      </button>
      <button 
        :class="['tab-btn', { active: activeTab === 'items' }]"
        @click="activeTab = 'items'"
      >
        <span class="emoji">🛍️</span>
        Gestione Articoli
      </button>
    </div>

    <!-- Users Tab -->
    <div v-if="activeTab === 'users'" class="tab-content">
      <div class="search-bar">
        <span class="emoji">🔍</span>
        <input 
          type="text" 
          v-model="userSearch" 
          placeholder="Cerca utenti per username o email..."
          @input="searchUsers"
        >
      </div>
      
      <div class="users-list">
        <div v-for="user in filteredUsers" :key="user.ute_id" class="user-card">
          <div class="user-info">
            <img :src="`/bitems/frontend/UploadedImages/${user.img_url}`" :alt="user.ute_username" class="user-avatar">
            <div class="user-details">
              <h3>{{ user.ute_username }}</h3>
              <p><span class="emoji">📧</span> {{ user.ute_email }}</p>
              <p><span class="emoji">⭐</span> Reputazione: {{ user.ute_rep }}</p>
              <p><span class="emoji">💰</span> Saldo: {{ user.ute_saldo }}€</p>
            </div>
          </div>
          <div class="user-actions">
            <button class="edit-btn" @click="editUser(user)">
              <span class="emoji">✏️</span>
              Modifica
            </button>
            <button 
              class="ban-btn" 
              @click="confirmBanUser(user)"
              v-if="user.ute_id !== 1"
            >
              <span class="emoji">🚫</span>
              Banna Utente
            </button>
          </div>
        </div>
      </div>
      
      <div v-if="hasMoreUsers" class="load-more">
        <button @click="loadMoreUsers" class="load-more-btn">
          <span class="emoji">📥</span>
          Carica altri utenti
        </button>
      </div>
    </div>

    <!-- Items Tab -->
    <div v-if="activeTab === 'items'" class="tab-content">
      <div class="search-bar">
        <span class="emoji">🔍</span>
        <input 
          type="text" 
          v-model="itemSearch" 
          placeholder="Cerca articoli per titolo o venditore..."
          @input="searchItems"
        >
      </div>
      
      <div class="items-table">
        <table>
          <thead>
            <tr>
              <th>Titolo</th>
              <th>Venditore</th>
              <th>Prezzo</th>
              <th>Quantità</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in filteredItems" :key="item.art_id" @click="viewItemDetails(item)">
              <td>{{ item.art_titolo }}</td>
              <td>{{ item.seller_name }}</td>
              <td>{{ item.art_prezzoUnitario }}€</td>
              <td>{{ item.art_qtaDisp }}</td>
              <td>
                <button class="edit-btn" @click.stop="editItem(item)">
                  <span class="emoji">✏️</span>
                </button>
                <button class="delete-btn" @click.stop="confirmDeleteItem(item)">
                  <span class="emoji">🗑️</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div v-if="hasMoreItems" class="load-more">
        <button @click="loadMoreItems" class="load-more-btn">
          <span class="emoji">📥</span>
          Carica altri articoli
        </button>
      </div>
    </div>

    <!-- Modals -->
    <CustomAlert
      v-if="showAlert"
      :show="showAlert"
      :title="alertTitle"
      :subtitle="alertSubtitle"
      @close="closeAlert"
    />

    <EditItemPopup
      v-if="showEditPopup"
      :item="selectedItem"
      @close="showEditPopup = false"
      @item-updated="handleItemUpdate"
    />

    <EditUserPopup
      v-if="showEditUserPopup"
      :user="selectedUser"
      @close="showEditUserPopup = false"
      @user-updated="handleUserUpdate"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from '@/config/axios'
import CustomAlert from '../components/CustomAlert.vue'
import EditItemPopup from '../components/EditItemPopup.vue'
import EditUserPopup from '../components/EditUserPopup.vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// State
const activeTab = ref('users')
const users = ref([])
const items = ref([])
const userSearch = ref('')
const itemSearch = ref('')
const showAlert = ref(false)
const alertTitle = ref('')
const alertSubtitle = ref('')
const showEditPopup = ref(false)
const showEditUserPopup = ref(false)
const selectedItem = ref(null)
const selectedUser = ref(null)
const currentUserPage = ref(1)
const currentItemPage = ref(1)
const totalUsers = ref(0)
const totalItems = ref(0)
const hasMoreUsers = ref(false)
const hasMoreItems = ref(false)

// Computed
const filteredUsers = computed(() => {
  const search = userSearch.value.toLowerCase()
  return users.value.filter(user => 
    user.ute_username.toLowerCase().includes(search) ||
    user.ute_email.toLowerCase().includes(search)
  )
})

const filteredItems = computed(() => {
  const search = itemSearch.value.toLowerCase()
  return items.value.filter(item =>
    item.art_titolo.toLowerCase().includes(search) ||
    item.seller_name.toLowerCase().includes(search)
  )
})

// Check if user is admin
const isAdmin = computed(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  return user.ute_id === 1
})

// Methods
function showCustomAlert(title: string, subtitle: string) {
  alertTitle.value = title
  alertSubtitle.value = subtitle
  showAlert.value = true
}

function closeAlert() {
  showAlert.value = false
}

async function checkSession() {
  try {
    const response = await axios.get('http://localhost:80/bitems/frontend/backend/checkSession.php', {
      withCredentials: true
    })
    if (response.data === "false") {
      showCustomAlert('Errore', 'Sessione scaduta. Effettua nuovamente il login.')
      router.push('/')
      return false
    }
    return true
  } catch (error) {
    console.error('Session check error:', error)
    showCustomAlert('Errore', 'Errore di autenticazione')
    router.push('/')
    return false
  }
}

async function loadUsers(page = 1) {
  try {
    const response = await axios.get(`http://localhost:80/bitems/frontend/backend/AdminPanel.php?action=getUsers&page=${page}`, {
      withCredentials: true
    })
    if (page === 1) {
      users.value = response.data.users
    } else {
      users.value = [...users.value, ...response.data.users]
    }
    totalUsers.value = response.data.total
    hasMoreUsers.value = response.data.hasMore
    currentUserPage.value = page
  } catch (error) {
    console.error('Error loading users:', error)
    showCustomAlert('Errore', 'Impossibile caricare gli utenti')
  }
}

async function loadItems(page = 1) {
  try {
    const response = await axios.get(`http://localhost:80/bitems/frontend/backend/AdminPanel.php?action=getItems&page=${page}`, {
      withCredentials: true
    })
    if (page === 1) {
      items.value = response.data.items
    } else {
      items.value = [...items.value, ...response.data.items]
    }
    totalItems.value = response.data.total
    hasMoreItems.value = response.data.hasMore
    currentItemPage.value = page
  } catch (error) {
    console.error('Error loading items:', error)
    showCustomAlert('Errore', 'Impossibile caricare gli articoli')
  }
}

function searchUsers() {
  const search = userSearch.value.toLowerCase()
  filteredUsers.value = users.value.filter(user => 
    user.ute_username.toLowerCase().includes(search) ||
    user.ute_email.toLowerCase().includes(search)
  )
}

function searchItems() {
  const search = itemSearch.value.toLowerCase()
  filteredItems.value = items.value.filter(item =>
    item.art_titolo.toLowerCase().includes(search) ||
    item.seller_name.toLowerCase().includes(search)
  )
}

function loadMoreUsers() {
  loadUsers(currentUserPage.value + 1)
}

function loadMoreItems() {
  loadItems(currentItemPage.value + 1)
}

function viewItemDetails(item) {
  router.push(`/itemdetail?id=${item.art_id}`)
}

function editUser(user) {
  selectedUser.value = user
  showEditUserPopup.value = true
}

async function handleUserUpdate(updatedUser) {
  try {
    const response = await axios.put('http://localhost:80/bitems/frontend/backend/AdminPanel.php?action=updateUser', {
      userId: updatedUser.ute_id,
      updates: {
        ute_username: updatedUser.ute_username,
        ute_email: updatedUser.ute_email,
        ute_saldo: updatedUser.ute_saldo,
        ute_rep: updatedUser.ute_rep
      }
    }, {
      withCredentials: true
    })
    
    if (response.data === "true") {
      showCustomAlert('Successo', 'Utente aggiornato con successo')
      await loadUsers(currentUserPage.value)
    } else {
      showCustomAlert('Errore', 'Errore durante l\'aggiornamento dell\'utente')
    }
  } catch (error) {
    console.error('Error updating user:', error)
    showCustomAlert('Errore', 'Si è verificato un errore durante l\'aggiornamento dell\'utente')
  }
  showEditUserPopup.value = false
}

async function confirmBanUser(user) {
  if (confirm(`Sei sicuro di voler bannare l'utente ${user.ute_username}?`)) {
    try {
      const response = await axios.post('http://localhost:80/bitems/frontend/backend/AdminPanel.php?action=banUser', {
        userId: user.ute_id
      }, {
        withCredentials: true
      })
      
      if (response.data === "true") {
        showCustomAlert('Successo', `Utente ${user.ute_username} bannato con successo`)
        await loadUsers(currentUserPage.value)
      } else {
        showCustomAlert('Errore', 'Errore durante il ban dell\'utente')
      }
    } catch (error) {
      console.error('Error banning user:', error)
      showCustomAlert('Errore', 'Si è verificato un errore durante il ban dell\'utente')
    }
  }
}

function editItem(item) {
  selectedItem.value = item
  showEditPopup.value = true
}

async function confirmDeleteItem(item) {
  if (confirm(`Sei sicuro di voler eliminare l'articolo "${item.art_titolo}"?`)) {
    try {
      const response = await axios.delete(`http://localhost:80/bitems/frontend/backend/AdminPanel.php?action=deleteItem&id=${item.art_id}`, {
        withCredentials: true
      })
      
      if (response.data === "true") {
        showCustomAlert('Successo', 'Articolo eliminato con successo')
        await loadItems(currentItemPage.value)
      } else {
        showCustomAlert('Errore', 'Errore durante l\'eliminazione dell\'articolo')
      }
    } catch (error) {
      console.error('Error deleting item:', error)
      showCustomAlert('Errore', 'Si è verificato un errore durante l\'eliminazione dell\'articolo')
    }
  }
}

function handleItemUpdate(updatedItem) {
  const index = items.value.findIndex(item => item.art_id === updatedItem.art_id)
  if (index !== -1) {
    items.value[index] = updatedItem
    filteredItems.value = items.value
  }
  showEditPopup.value = false
}

// Initialize data
onMounted(async () => {
  if (!isAdmin.value) {
    showCustomAlert('Errore', 'Accesso non autorizzato')
    router.push('/')
    return
  }
  
  if (!await checkSession()) return
  
  console.log('Loading admin panel data...')
  await Promise.all([loadUsers(1), loadItems(1)])
})
</script>

<style scoped>
.admin-panel {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.admin-header {
  margin-bottom: 2rem;
}

h1 {
  color: var(--primary-light);
  margin-bottom: 1.5rem;
}

.admin-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--surface);
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.emoji {
  font-size: 2rem;
}

.stat-info h3 {
  margin: 0;
  font-size: 0.9rem;
  color: var(--on-surface);
  opacity: 0.8;
}

.stat-info p {
  margin: 0.5rem 0 0;
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--primary-light);
}

.tabs {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.tab-btn {
  padding: 1rem 2rem;
  border: none;
  background: var(--surface-light);
  color: var(--on-surface);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.tab-btn .emoji {
  font-size: 1.2rem;
}

.tab-btn.active {
  background: var(--primary-light);
  color: var(--on-primary);
}

.search-bar {
  margin-bottom: 1.5rem;
  position: relative;
}

.search-bar .emoji {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1.2rem;
}

.search-bar input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid var(--surface-light);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s;
}

.search-bar input:focus {
  border-color: var(--primary-light);
  outline: none;
}

.users-list, .items-table {
  display: grid;
  gap: 1rem;
}

.user-card, .item-card {
  background: var(--surface);
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}

.user-card:hover, .item-card:hover {
  transform: translateY(-2px);
}

.user-info, .item-info {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.user-avatar, .item-image {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
}

.item-image {
  border-radius: 8px;
}

.user-details, .item-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.user-details h3, .item-details h3 {
  color: var(--primary-light);
  margin: 0;
  font-size: 1.2rem;
}

.user-details p, .item-details p {
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--on-surface);
}

.user-details .emoji, .item-details .emoji {
  font-size: 1.2rem;
  width: 1.2rem;
  text-align: center;
}

.item-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.tag {
  background: var(--surface-light);
  color: var(--on-surface);
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.9rem;
}

.user-actions, .item-actions {
  display: flex;
  gap: 1rem;
}

.ban-btn, .edit-btn, .delete-btn {
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
}

.ban-btn {
  background: var(--error);
  color: var(--on-error);
}

.edit-btn {
  background: var(--primary-light);
  color: var(--on-primary);
}

.delete-btn {
  background: var(--error);
  color: var(--on-error);
}

.ban-btn:hover, .edit-btn:hover, .delete-btn:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

.items-table {
  width: 100%;
  overflow-x: auto;
  margin-bottom: 1rem;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: var(--surface);
  border-radius: 8px;
  overflow: hidden;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid var(--surface-light);
}

th {
  background: var(--surface-light);
  font-weight: 600;
  color: var(--primary-light);
}

tr {
  cursor: pointer;
  transition: background-color 0.2s;
}

tr:hover {
  background-color: var(--surface-light);
}

.load-more {
  display: flex;
  justify-content: center;
  margin-top: 1rem;
}

.load-more-btn {
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 6px;
  background: var(--primary-light);
  color: var(--on-primary);
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.load-more-btn:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .admin-panel {
    padding: 1rem;
  }

  .user-card, .item-card {
    flex-direction: column;
    gap: 1rem;
  }

  .user-actions, .item-actions {
    width: 100%;
    justify-content: center;
  }

  .user-info, .item-info {
    flex-direction: column;
    text-align: center;
  }

  .user-details p, .item-details p {
    justify-content: center;
  }
}
</style> 