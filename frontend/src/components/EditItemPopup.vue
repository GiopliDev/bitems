<template>
  <div class="popup-overlay" @click.self="close">
    <div class="popup-card">
      <button class="close-btn" @click="close">&times;</button>
      <h2>Modifica Articolo</h2>
      <form @submit.prevent="onSubmit">
        <div class="form-row">
          <label for="title">Titolo</label>
          <input id="title" v-model="form.title" type="text" required maxlength="50" />
        </div>
        <div class="form-row">
          <label for="description">Descrizione</label>
          <textarea id="description" v-model="form.description" required></textarea>
        </div>
        <div class="form-row">
          <label for="price">Prezzo (€)</label>
          <input id="price" v-model.number="form.price" type="number" min="0.01" step="0.01" required />
        </div>
        <div class="form-row">
          <label for="qty">Quantità Disponibile</label>
          <input id="qty" v-model.number="form.qty" type="number" min="1" required />
        </div>
        <div class="form-row">
          <label class="toggle-label">
            <input type="checkbox" v-model="form.isPrivato" />
            <span class="toggle-text">{{ form.isPrivato ? 'Privato' : 'Pubblico' }}</span>
          </label>
        </div>
        <button class="main-btn" type="submit">Salva Modifiche</button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/config/axios'

const props = defineProps<{
  item: {
    art_id: number
    art_titolo: string
    art_descrizione: string
    art_prezzoUnitario: number
    art_qtaDisp: number
    art_isPrivato: boolean
  }
}>()

const emit = defineEmits(['close', 'item-updated'])

const form = ref({
  title: '',
  description: '',
  price: 0,
  qty: 1,
  isPrivato: false
})

onMounted(() => {
  form.value = {
    title: props.item.art_titolo,
    description: props.item.art_descrizione,
    price: props.item.art_prezzoUnitario,
    qty: props.item.art_qtaDisp,
    isPrivato: props.item.art_isPrivato
  }
})

async function onSubmit() {
  try {
    const response = await axios.put('/bitems/frontend/backend/updateItem.php', {
      id: props.item.art_id,
      ...form.value
    })
    
    if (response.data.success) {
      emit('item-updated', response.data.item)
      emit('close')
    } else {
      alert('Errore durante l\'aggiornamento: ' + response.data.error)
    }
  } catch (error) {
    console.error('Error updating item:', error)
    alert('Si è verificato un errore durante l\'aggiornamento')
  }
}

function close() {
  emit('close')
}
</script>

<style scoped>
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.popup-card {
  background: radial-gradient(ellipse at top left, #1e2a2f 0%, #18181c 100%);
  border: 2px solid var(--secondary, #03dac6);
  border-radius: 20px;
  padding: 2.5rem 2rem 1.5rem 2rem;
  min-width: 400px;
  max-width: 95vw;
  box-shadow: 0 8px 32px #000a;
  position: relative;
  color: var(--on-surface, #fff);
}

.close-btn {
  position: absolute;
  top: 12px;
  right: 18px;
  background: none;
  border: none;
  color: #bbb;
  font-size: 2rem;
  cursor: pointer;
  transition: color 0.2s;
}

.close-btn:hover {
  color: var(--error, #cf6679);
}

h2 {
  text-align: center;
  margin-bottom: 1.5rem;
  font-weight: 400;
  color: var(--primary-light);
}

.form-row {
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
  margin-bottom: 1.2rem;
}

label {
  font-size: 1rem;
  color: var(--on-surface);
}

input, textarea {
  background: transparent;
  border: 2px solid var(--secondary);
  border-radius: 8px;
  padding: 0.6rem 1rem;
  color: var(--on-surface);
  font-size: 1.1rem;
  outline: none;
  transition: border-color 0.2s;
}

textarea {
  min-height: 100px;
  resize: vertical;
}

input:focus, textarea:focus {
  border-color: var(--primary-light);
}

.toggle-label {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  cursor: pointer;
}

.toggle-label input[type="checkbox"] {
  width: 1.2rem;
  height: 1.2rem;
  cursor: pointer;
}

.toggle-text {
  font-size: 1.1rem;
  color: var(--on-surface);
}

.main-btn {
  width: 100%;
  padding: 1rem;
  background: var(--primary-light);
  color: var(--on-primary);
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-top: 1rem;
}

.main-btn:hover {
  background: var(--secondary);
}
</style> 