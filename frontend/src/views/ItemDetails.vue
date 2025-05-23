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

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

const item = ref<any>(null)
const userReaction = ref<string | null>(null)
const isOwner = ref(false)

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
</script>

<style scoped>
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