<template>
  <div class="bookmarks-container">
    <h1>I tuoi preferiti</h1>
    <div v-if="bookmarks.length === 0" class="no-bookmarks">
      <p>Non hai ancora salvato nessun articolo nei preferiti</p>
      <router-link to="/catalogo" class="browse-btn">Esplora il catalogo</router-link>
    </div>
    <div v-else class="bookmarks-grid">
      <div v-for="item in bookmarks" :key="item.art_id" class="bookmark-card">
        <button class="remove-bookmark" @click="removeBookmark(item.art_id)">×</button>
        <router-link :to="{ path: '/item', query: { id: item.art_id }}" class="bookmark-link">
          <div class="bookmark-image">
            <img 
              :src="item.images && item.images.length > 0 ? `/bitems/frontend/UploadedImages/${item.images[0]}` : '/bitems/frontend/UploadedImages/default.jpg'" 
              :alt="item.art_titolo"
            />
          </div>
          <div class="bookmark-info">
            <h3>{{ item.art_titolo }}</h3>
            <p class="game-name">{{ item.game_name }}</p>
            <p class="price">{{ item.art_prezzoUnitario }}€</p>
            <div class="tags">
              <span v-for="tag in item.tags" :key="tag" class="tag">#{{ tag }}</span>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/config/axios'

interface Bookmark {
  art_id: number
  art_titolo: string
  art_prezzoUnitario: number
  game_name: string
  tags: string[]
  images: string[]
}

const bookmarks = ref<Bookmark[]>([])

async function loadBookmarks() {
  try {
    const userId = localStorage.getItem('user_id')
    if (!userId) return

    const response = await axios.post('/bitems/frontend/backend/bookmarks.php', {
      action: 'getBookmarks',
      id_utente: userId
    })

    if (response.data.success) {
      bookmarks.value = response.data.bookmarks
    }
  } catch (error) {
    console.error('Error loading bookmarks:', error)
  }
}

async function removeBookmark(itemId: number) {
  try {
    const userId = localStorage.getItem('user_id')
    if (!userId) return

    const response = await axios.post('/bitems/frontend/backend/bookmarks.php', {
      action: 'removeBookmark',
      id_utente: userId,
      id_articolo: itemId
    })

    if (response.data.success) {
      bookmarks.value = bookmarks.value.filter(item => item.art_id !== itemId)
    }
  } catch (error) {
    console.error('Error removing bookmark:', error)
  }
}

onMounted(loadBookmarks)
</script>

<style scoped>
.bookmarks-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

h1 {
  color: var(--primary-light);
  margin-bottom: 2rem;
  font-size: 2rem;
}

.no-bookmarks {
  text-align: center;
  padding: 3rem;
  background: var(--surface);
  border-radius: 12px;
  box-shadow: 0 2px 16px #0002;
}

.no-bookmarks p {
  color: var(--on-surface);
  margin-bottom: 1.5rem;
  font-size: 1.2rem;
}

.browse-btn {
  display: inline-block;
  padding: 0.8rem 1.5rem;
  background: var(--primary-light);
  color: var(--on-primary);
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  transition: background-color 0.2s;
}

.browse-btn:hover {
  background: var(--secondary);
}

.bookmarks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.bookmark-card {
  position: relative;
  background: var(--surface);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 16px #0002;
  transition: transform 0.2s;
}

.bookmark-card:hover {
  transform: translateY(-4px);
}

.remove-bookmark {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 32px;
  height: 32px;
  background: var(--error);
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 1.5rem;
  cursor: pointer;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.remove-bookmark:hover {
  background: #d32f2f;
}

.bookmark-link {
  text-decoration: none;
  color: inherit;
  display: block;
}

.bookmark-image {
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.bookmark-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.bookmark-info {
  padding: 1.2rem;
}

.bookmark-info h3 {
  color: var(--primary-light);
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.game-name {
  color: var(--secondary);
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.price {
  color: var(--on-surface);
  font-weight: 700;
  font-size: 1.1rem;
  margin-bottom: 0.8rem;
}

.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.tag {
  background: var(--surface-light);
  color: var(--secondary);
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
  font-size: 0.9rem;
}
</style> 