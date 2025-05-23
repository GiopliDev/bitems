<template>
  <div class="item-details" v-if="!loading">
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
    <div v-else-if="item" class="item-content">
      <div class="item-actions">
        <div class="action-buttons">
          <button 
            class="action-button like" 
            :class="{ active: userReaction === '1' }"
            @click="handleReaction('1')"
          >
            <i class="fas fa-thumbs-up"></i>
            <span>{{ item.likes }}</span>
          </button>
          <button 
            class="action-button dislike" 
            :class="{ active: userReaction === '0' }"
            @click="handleReaction('0')"
          >
            <i class="fas fa-thumbs-down"></i>
            <span>{{ item.dislikes }}</span>
          </button>
        </div>
        <button 
          v-if="!isOwner" 
          class="buy-button" 
          :disabled="item.art_qtaDisp <= 0"
          @click="handleBuy"
        >
          {{ item.art_qtaDisp > 0 ? 'Acquista' : 'Esaurito' }}
        </button>
      </div>
    </div>
  </div>
  <div v-else class="loading">
    Caricamento dettagli articolo...
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from '@/config/axios'

const route = useRoute()
const router = useRouter()
const item = ref<any>(null)
const loading = ref(true)
const error = ref('')
const userReaction = ref<string | null>(null)
const isOwner = ref(false)
const isLoggedIn = ref(false)
const showLoginPopup = ref(false)

async function loadItemDetails() {
  loading.value = true
  error.value = ''
  
  try {
    const response = await axios.get('/bitems/frontend/backend/getItem.php', {
      params: { id: route.query.id }
    })
    
    if (response.data.success) {
      item.value = response.data.item
      // Controlla se l'utente è il proprietario
      const userResponse = await axios.get('/bitems/frontend/backend/checkSession.php')
      if (userResponse.data.success) {
        isLoggedIn.value = true
        isOwner.value = userResponse.data.user.ute_id === item.value.art_ute_id
      }
    } else {
      error.value = response.data.error || 'Impossibile caricare i dettagli dell\'articolo'
    }
  } catch (err) {
    console.error('Error loading item details:', err)
    error.value = 'Si è verificato un errore nel caricamento dei dettagli dell\'articolo'
  } finally {
    loading.value = false
  }
}

async function handleReaction(voto: string) {
  if (!isLoggedIn.value) {
    showLoginPopup.value = true
    return
  }

  try {
    const response = await axios.post('/bitems/frontend/backend/checkUserReaction.php', {
      art_id: item.value.art_id,
      voto: voto
    })

    if (response.data.success) {
      // Aggiorna il conteggio dei like/dislike
      if (voto === '1') {
        if (userReaction.value === '1') {
          item.value.likes--
          userReaction.value = null
        } else {
          if (userReaction.value === '0') {
            item.value.dislikes--
          }
          item.value.likes++
          userReaction.value = '1'
        }
      } else {
        if (userReaction.value === '0') {
          item.value.dislikes--
          userReaction.value = null
        } else {
          if (userReaction.value === '1') {
            item.value.likes--
          }
          item.value.dislikes++
          userReaction.value = '0'
        }
      }
    }
  } catch (error) {
    console.error('Error handling reaction:', error)
  }
}

async function handleBuy() {
  if (!isLoggedIn.value) {
    showLoginPopup.value = true
    return
  }

  try {
    const response = await axios.post('/bitems/frontend/backend/checkPurchase.php', {
      art_id: item.value.art_id
    })

    if (response.data.success) {
      // Aggiorna la quantità disponibile
      item.value.art_qtaDisp--
      // Mostra un messaggio di successo
      alert('Acquisto effettuato con successo!')
    } else {
      alert(response.data.error || 'Errore durante l\'acquisto')
    }
  } catch (error) {
    console.error('Error handling purchase:', error)
    alert('Si è verificato un errore durante l\'acquisto')
  }
}

onMounted(loadItemDetails)
</script>

<style scoped>
.item-details {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: var(--on-surface);
  font-size: 1.2rem;
}

.error-message {
  text-align: center;
  padding: 2rem;
  color: var(--error);
  font-size: 1.2rem;
  background: var(--error-container);
  border-radius: 8px;
  margin: 1rem 0;
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  border: 1px solid var(--surface-light);
  background: var(--surface);
  color: var(--on-surface);
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s;
}

.action-button:hover {
  background: var(--surface-light);
}

.action-button.active {
  background: var(--primary-light);
  color: white;
  border-color: var(--primary-light);
}

.action-button.like.active {
  background: var(--success);
  border-color: var(--success);
}

.action-button.dislike.active {
  background: var(--error);
  border-color: var(--error);
}

.action-button i {
  font-size: 1.1rem;
}
</style> 