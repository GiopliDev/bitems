<template>
  <div class="balance-editor">
    <button @click="showModal = true" class="balance-btn">
      💰 {{ user?.ute_saldo || 0 }}€
    </button>

    <div v-if="showModal" class="modal-overlay" @click="showModal = false">
      <div class="modal-content" @click.stop>
        <h2>Modifica Saldo</h2>
        <div class="input-group">
          <label for="newBalance">Nuovo Saldo (€):</label>
          <input 
            type="number" 
            id="newBalance" 
            v-model="newBalance" 
            step="0.01"
            min="0"
          />
        </div>
        <div class="modal-actions">
          <button @click="updateBalance" class="save-btn">Salva</button>
          <button @click="showModal = false" class="cancel-btn">Annulla</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@/config/axios'

const props = defineProps({
  user: Object
})

const showModal = ref(false)
const newBalance = ref(props.user?.ute_saldo || 0)

async function updateBalance() {
  try {
    const response = await axios.post('/bitems/frontend/backend/updateBalance.php', {
      balance: parseFloat(newBalance.value),
      userId: props.user.ute_id
    })

    if (response.data.success) {
      props.user.ute_saldo = parseFloat(newBalance.value)
      showModal.value = false
    }
  } catch (error) {
    console.error('Error updating balance:', error)
    alert('Errore durante l\'aggiornamento del saldo')
  }
}
</script>

<style scoped>
.balance-editor {
  position: relative;
}

.balance-btn {
  background: var(--primary-light);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.2s;
}

.balance-btn:hover {
  background: var(--secondary);
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: var(--surface);
  padding: 2rem;
  border-radius: 8px;
  min-width: 300px;
}

.input-group {
  margin: 1rem 0;
}

.input-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--on-surface);
}

.input-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid var(--primary-light);
  border-radius: 4px;
  font-size: 1rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

.save-btn, .cancel-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
}

.save-btn {
  background: var(--primary-light);
  color: white;
}

.cancel-btn {
  background: var(--error);
  color: white;
}

.save-btn:hover {
  background: var(--secondary);
}

.cancel-btn:hover {
  opacity: 0.9;
}
</style> 