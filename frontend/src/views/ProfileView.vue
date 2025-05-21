<template>
  <div class="profile-layout">
    <div class="profile-header">
      <div class="profile-image-container">
        <img 
          :src="profile.ute_img_url ? `/bitems/frontend/UploadedImages/${profile.ute_img_url}` : '/default-avatar.png'" 
          alt="Profile" 
          class="profile-image" 
        />
        <button v-if="isOwnProfile" class="edit-image-btn" @click="triggerImageUpload">
          <span class="icon">📷</span>
        </button>
        <input 
          type="file" 
          ref="imageInput" 
          accept="image/*" 
          style="display: none" 
          @change="onImageChange"
        />
      </div>
      <div class="profile-info">
        <div class="name-container">
          <h1 v-if="!isEditing || !isOwnProfile">{{ profile.ute_username }}</h1>
          <input 
            v-else 
            v-model="editingName" 
            type="text" 
            class="edit-input"
            @blur="updateName"
          />
          <button v-if="isOwnProfile" class="edit-btn" @click="startEditing">
            <span class="icon">✏️</span>
          </button>
        </div>
        <div class="description-container">
          <p v-if="!isEditing || !isOwnProfile">{{ profile.ute_dex || 'Nessuna descrizione' }}</p>
          <textarea 
            v-else 
            v-model="editingDescription" 
            class="edit-input"
            @blur="updateDescription"
          ></textarea>
        </div>
        <div class="reputation">
          <span class="rep-label">Reputazione:</span>
          <span class="rep-value">{{ profile.ute_rep || 0 }}</span>
        </div>
      </div>
    </div>

    <div v-if="userItems.length > 0" class="user-items-section">
      <h2>{{ isOwnProfile ? 'I tuoi oggetti in vendita' : 'Oggetti in vendita' }}</h2>
      <div class="items-grid">
        <ItemCard 
          v-for="item in userItems" 
          :key="item.art_id" 
          :item="item"
          :is-owner="isOwnProfile"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from '@/config/axios'
import ItemCard from '../components/ItemCard.vue'

const route = useRoute()
const profile = ref<any>({})
const userItems = ref<any[]>([])
const isEditing = ref(false)
const editingName = ref('')
const editingDescription = ref('')
const imageInput = ref<HTMLInputElement | null>(null)
const currentUserId = ref<number | null>(null)

const isOwnProfile = computed(() => {
  return currentUserId.value === Number(route.query.id)
})

onMounted(async () => {
  try {
    // Carica i dati della sessione
    const sessionRes = await axios.get('/bitems/frontend/backend/session.php', {
      params: { action: 'check' }
    })
    currentUserId.value = sessionRes.data.userId

    // Carica i dati del profilo
    const profileRes = await axios.get('/bitems/frontend/backend/getProfile.php', {
      params: { id: route.query.id }
    })
    profile.value = profileRes.data.profile
    userItems.value = profileRes.data.items
  } catch (error) {
    console.error('Error loading profile:', error)
  }
})

function startEditing() {
  isEditing.value = true
  editingName.value = profile.value.ute_username
  editingDescription.value = profile.value.ute_dex
}

async function updateName() {
  try {
    await axios.post('/bitems/frontend/backend/updateProfile.php', {
      action: 'updateName',
      name: editingName.value
    })
    profile.value.ute_username = editingName.value
    isEditing.value = false
  } catch (error) {
    console.error('Error updating name:', error)
  }
}

async function updateDescription() {
  try {
    await axios.post('/bitems/frontend/backend/updateProfile.php', {
      action: 'updateDescription',
      description: editingDescription.value
    })
    profile.value.ute_dex = editingDescription.value
    isEditing.value = false
  } catch (error) {
    console.error('Error updating description:', error)
  }
}

function triggerImageUpload() {
  imageInput.value?.click()
}

async function onImageChange(e: Event) {
  const input = e.target as HTMLInputElement
  if (!input.files?.length) return

  const file = input.files[0]
  const formData = new FormData()
  formData.append('image', file)

  try {
    // Prima carica l'immagine
    const uploadResponse = await axios.post('/bitems/frontend/backend/uploadImage.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (uploadResponse.data.success) {
      // Poi aggiorna il profilo con l'ID dell'immagine
      const updateResponse = await axios.post('/bitems/frontend/backend/updateProfile.php', {
        action: 'updateImage',
        image_id: uploadResponse.data.image_id
      })

      if (updateResponse.data.success) {
        profile.value.ute_img_id = uploadResponse.data.image_id
      }
    }
  } catch (error) {
    console.error('Error updating image:', error)
  }
}
</script>

<style scoped>
.profile-layout {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}

.profile-header {
  display: flex;
  gap: 2rem;
  margin-bottom: 3rem;
  background: var(--surface);
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 32px #0003;
}

.profile-image-container {
  position: relative;
  width: 200px;
  height: 200px;
}

.profile-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid var(--primary-light);
}

.edit-image-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  background: var(--primary-light);
  color: white;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.edit-image-btn:hover {
  background: var(--secondary);
}

.profile-info {
  flex: 1;
}

.name-container {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.name-container h1 {
  font-size: 2.5rem;
  color: var(--primary-light);
  margin: 0;
}

.edit-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  transition: transform 0.2s;
}

.edit-btn:hover {
  transform: scale(1.1);
}

.description-container {
  color: var(--on-surface);
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.reputation {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--on-surface);
  font-size: 1.1rem;
}

.rep-label {
  color: var(--primary-light);
  font-weight: 600;
}

.rep-value {
  color: var(--secondary);
  font-weight: 700;
}

.edit-input {
  background: var(--surface-light);
  border: 2px solid var(--primary-light);
  border-radius: 8px;
  padding: 0.8rem;
  color: var(--on-surface);
  font-size: 1.1rem;
  width: 100%;
  outline: none;
}

.edit-input:focus {
  border-color: var(--secondary);
}

.user-items-section {
  margin-top: 3rem;
}

.user-items-section h2 {
  color: var(--primary-light);
  margin-bottom: 1.5rem;
}

.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}
</style> 