<template>
  <div class="edit-user-popup">
    <div class="popup-content">
      <h2>Modifica Utente</h2>
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="username">Username</label>
          <input 
            type="text" 
            id="username" 
            v-model="editedUser.ute_username" 
            required
          >
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="editedUser.ute_email" 
            required
          >
        </div>
        
        <div class="form-group">
          <label for="balance">Saldo</label>
          <input 
            type="number" 
            id="balance" 
            v-model="editedUser.ute_saldo" 
            step="0.01"
            required
          >
        </div>
        
        <div class="form-group">
          <label for="reputation">Reputazione</label>
          <input 
            type="number" 
            id="reputation" 
            v-model="editedUser.ute_rep" 
            required
          >
        </div>
        
        <div class="actions">
          <button type="button" class="cancel-btn" @click="$emit('close')">
            Annulla
          </button>
          <button type="submit" class="save-btn">
            Salva Modifiche
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const props = defineProps<{
  user: {
    ute_id: number
    ute_username: string
    ute_email: string
    ute_saldo: number
    ute_rep: number
  }
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'user-updated', user: any): void
}>()

const editedUser = ref({
  ute_id: props.user.ute_id,
  ute_username: props.user.ute_username,
  ute_email: props.user.ute_email,
  ute_saldo: props.user.ute_saldo,
  ute_rep: props.user.ute_rep
})

function handleSubmit() {
  emit('user-updated', editedUser.value)
}
</script>

<style scoped>
.edit-user-popup {
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

.popup-content {
  background: var(--surface);
  padding: 2rem;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h2 {
  color: var(--primary-light);
  margin-bottom: 1.5rem;
  text-align: center;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--on-surface);
}

input {
  width: 100%;
  padding: 0.8rem;
  border: 2px solid var(--surface-light);
  border-radius: 8px;
  background: var(--surface-light);
  color: var(--on-surface);
  font-size: 1rem;
}

input:focus {
  outline: none;
  border-color: var(--primary-light);
}

.actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

button {
  flex: 1;
  padding: 0.8rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-btn {
  background: var(--surface-light);
  color: var(--on-surface);
}

.save-btn {
  background: var(--primary-light);
  color: var(--on-primary);
}

button:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .popup-content {
    width: 95%;
    padding: 1.5rem;
  }
  
  .actions {
    flex-direction: column;
  }
}
</style> 