<template>
  <div class="popup-overlay" @click.self="close">
    <div class="popup-card">
      <button class="close-btn" @click="close">&times;</button>
      <h2>Pagamento</h2>
      <div class="payment-details">
        <p>Saldo attuale: {{ currentBalance }}€</p>
        <p>Prezzo unitario: {{ price }}€</p>
        <p>Quantità: {{ quantity }}</p>
        <p>Tasse: 1.00€</p>
        <p>Totale da pagare: {{ totalPrice }}€</p>
        <p v-if="error" class="error-message">{{ error }}</p>
      </div>
      <div class="popup-actions">
        <button class="main-btn" @click="processPayment" :disabled="processing">
          {{ processing ? 'Elaborazione...' : 'Conferma pagamento' }}
        </button>
        <button class="main-btn outline" @click="close">Annulla</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/config/axios'

const props = defineProps<{
  itemId: number
  quantity: number
  price: number
}>()

const emit = defineEmits(['close', 'payment-success'])
const router = useRouter()

const currentBalance = ref(0)
const processing = ref(false)
const error = ref('')

const totalPrice = computed(() => {
  return (props.price * props.quantity + 1).toFixed(2)
})

async function checkSession() {
  try {
    const response = await axios.get('/bitems/frontend/backend/session.php?action=check')
    if (!response.data.isLoggedIn) {
      router.push('/login')
      return false
    }
    return true
  } catch (error) {
    console.error('Error checking session:', error)
    return false
  }
}

async function getUserBalance() {
  try {
    const response = await axios.get('/bitems/frontend/backend/getUserBalance.php')
    currentBalance.value = response.data.balance
  } catch (error: any) {
    console.error('Error getting balance:', error)
    if (error.response?.status === 401) {
      router.push('/login')
    }
  }
}

async function processPayment() {
  if (!await checkSession()) {
    router.push('/login')
    return
  }

  processing.value = true
  error.value = ''

  try {
    console.log('Sending payment data:', {
      itemId: props.itemId,
      quantity: props.quantity
    })

    const response = await axios.post('/bitems/frontend/backend/processPayment.php', {
      itemId: props.itemId,
      quantity: props.quantity
    }, {
      headers: {
        'Content-Type': 'application/json'
      },
      withCredentials: true
    })

    if (response.data.success) {
      currentBalance.value = response.data.newBalance
      emit('payment-success')
      close()
    } else {
      error.value = response.data.error || 'Errore durante il pagamento'
    }
  } catch (error: any) {
    console.error('Payment error:', error.response?.data || error)
    if (error.response?.status === 401) {
      router.push('/login')
    } else {
      error.value = error.response?.data?.error || 'Errore durante il pagamento'
    }
  } finally {
    processing.value = false
  }
}

function close() { 
  emit('close') 
}

onMounted(async () => {
  if (await checkSession()) {
    await getUserBalance()
  }
})
</script>

<style scoped>
.popup-overlay {
  position: fixed; 
  inset: 0; 
  background: rgba(0,0,0,0.6);
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
  min-width: 340px;
  max-width: 95vw;
  box-shadow: 0 8px 32px #000a;
  position: relative;
  color: var(--on-surface, #fff);
  display: flex;
  flex-direction: column;
  align-items: center;
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

.payment-details {
  text-align: center;
  margin: 1.5rem 0;
}

.error-message {
  color: var(--error);
  margin-top: 1rem;
}

.popup-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.main-btn {
  padding: 0.7rem 1.5rem;
  border: 2px solid var(--primary-light, #bb86fc);
  background: var(--primary-light, #bb86fc);
  color: var(--on-primary, #fff);
  font-weight: 700;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.main-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.main-btn.outline {
  background: transparent;
  color: var(--primary-light, #bb86fc);
  border: 2px solid var(--primary-light, #bb86fc);
}

.main-btn.outline:hover {
  background: #222;
  color: var(--secondary, #03dac6);
}
</style> 