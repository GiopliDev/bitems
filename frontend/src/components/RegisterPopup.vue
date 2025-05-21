<template>
  <div class="popup-overlay" @click.self="close">
    <div class="popup-card">
      <button class="close-btn" @click="close">&times;</button>
      <h2>Benvenuto su <span class="brand">Bitems</span></h2>
      <form @submit.prevent="onSubmit">
        <label>Nome</label>
        <input type="text" v-model="nome" required />
        <label>Cognome</label>
        <input type="text" v-model="cognome" required />
        <label>Nickname</label>
        <input type="text" v-model="nickname" required />
        <label>Email</label>
        <input type="email" v-model="email" required />
        <label>Password</label>
        <input type="password" v-model="password" required />
        <button class="main-btn" type="submit">Register</button>
      </form>
      <hr />
      <div class="footer">
        Hai già un account?
        <a href="#" @click.prevent="switchToLogin">Accedi qui</a>
      </div>
    </div>
    <CustomAlert
      :show="showAlert"
      :title="alertTitle"
      :subtitle="alertSubtitle"
      @close="closeAlert"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from '@/config/axios';
import CustomAlert from './CustomAlert.vue';

const emit = defineEmits(['close', 'switchToLogin'])
const nome = ref('')
const cognome = ref('')
const nickname = ref('')
const email = ref('')
const password = ref('')

// Alert state
const showAlert = ref(false)
const alertTitle = ref('')
const alertSubtitle = ref('')

function close() { emit('close') }
function switchToLogin() { emit('switchToLogin') }

function showCustomAlert(title, subtitle) {
  alertTitle.value = title
  alertSubtitle.value = subtitle
  showAlert.value = true
}

function closeAlert() {
  showAlert.value = false
}

async function onSubmit() {
  try {
    const formData = new FormData();
    formData.append('nome', nome.value);
    formData.append('cognome', cognome.value);
    formData.append('nickname', nickname.value);
    formData.append('email', email.value);
    formData.append('password', password.value);

    const response = await axios.post('/bitems/frontend/backend/register.php', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    if (response.data.success) {
      showCustomAlert('Registrazione completata', 'Benvenuto su BitItems! Ora puoi effettuare il login.')
      setTimeout(() => {
        close()
        switchToLogin()
      }, 2000)
    } else {
      showCustomAlert('Errore', response.data.message || 'Si è verificato un errore durante la registrazione.')
    }
  } catch (error) {
    console.error('Registration error:', error)
    showCustomAlert(
      'Errore di registrazione',
      error.response?.data?.message || 'Si è verificato un errore durante la registrazione. Riprova più tardi.'
    )
  }
}
</script>
<style scoped>
.popup-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.6);
  display: flex; align-items: center; justify-content: center; z-index: 1000;
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
  position: absolute; top: 12px; right: 18px;
  background: none; border: none; color: #bbb;
  font-size: 2rem; cursor: pointer; transition: color 0.2s;
}
.close-btn:hover { color: var(--error, #cf6679); }
h2 {
  text-align: center;
  margin-bottom: 1.5rem;
  font-weight: 400;
}
.brand {
  color: var(--primary-light, #bb86fc);
  font-weight: 700;
}
form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}
label {
  font-size: 1rem;
  margin-bottom: 0.1rem;
  color: #fff;
}
input {
  background: transparent;
  border: 2px solid var(--secondary, #03dac6);
  border-radius: 8px;
  padding: 0.6rem 1rem;
  color: #fff;
  font-size: 1.1rem;
  outline: none;
  margin-bottom: 0.5rem;
  transition: border-color 0.2s;
}
input:focus { border-color: var(--primary-light, #bb86fc); }
.main-btn {
  margin: 1rem auto 0 auto;
  padding: 0.6rem 2.5rem;
  border: 2px solid var(--secondary, #03dac6);
  background: transparent;
  color: var(--secondary, #03dac6);
  font-weight: 700;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.main-btn:hover {
  background: var(--secondary, #03dac6);
  color: #18181c;
}
hr {
  width: 100%;
  border: none;
  border-top: 1px solid #aaa3;
  margin: 1.5rem 0 1rem 0;
}
.footer {
  text-align: center;
  color: #ccc;
  font-size: 1rem;
}
.footer a {
  color: var(--primary-light, #bb86fc);
  text-decoration: none;
  margin-left: 0.2rem;
  transition: color 0.2s;
}
.footer a:hover { color: var(--secondary, #03dac6); }
.custom-alert {
  background: radial-gradient(ellipse at top left, #1e2a2f 0%, #18181c 100%);
  border: 2px solid var(--secondary, #03dac6);
  border-radius: 20px;
  padding: 2.5rem 2rem 1.5rem 2rem;
  box-shadow: 0 8px 32px #000a;
}

.custom-alert-title {
  color: var(--primary-light, #bb86fc);
  font-weight: 700;
}

.custom-alert-subtitle {
  color: var(--on-surface, #fff);
}

.custom-alert-button {
  background: var(--secondary, #03dac6);
  color: #18181c;
  border: none;
  padding: 0.8rem 2rem;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.custom-alert-button:hover {
  background: var(--primary-light, #bb86fc);
}
</style> 