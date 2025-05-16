<template>
  <div class="add-item-layout">
    <h2 class="add-item-title">Aggiungi un nuovo articolo</h2>
    <form class="add-item-form" @submit.prevent="submitItem">
      <div class="form-row">
        <label for="title">Titolo</label>
        <input id="title" v-model="form.title" type="text" required maxlength="50" placeholder="Titolo dell'oggetto" />
      </div>
      <div class="form-row">
        <label for="game">Gioco dell'Oggetto</label>
        <select id="game" v-model="form.game" @change="onGameChange">
          <option v-for="g in games" :key="g" :value="g">{{ g }}</option>
          <option value="Altro">Altro...</option>
        </select>
        <input v-if="form.game === 'Altro'" v-model="form.otherGame" type="text" placeholder="Specifica il gioco" />
      </div>
      <div class="form-row">
        <label for="description">Descrizione</label>
        <textarea id="description" v-model="form.description" placeholder="Descrizione dell'oggetto" required></textarea>
      </div>
      <div class="form-row">
        <label for="qty">Qta Disponibile</label>
        <input id="qty" v-model.number="form.qty" type="number" min="1" max="9999" required />
      </div>
      <div class="form-row">
        <label for="price">Prezzo unitario (€)</label>
        <input id="price" v-model.number="form.price" type="number" min="0.01" step="0.01" required />
      </div>
      <div class="form-row">
        <label>Immagini (max 3)</label>
        <input type="file" accept="image/*" multiple @change="onImageChange" :disabled="form.images.length >= 3" />
        <div class="image-preview-row">
          <div v-for="(img, idx) in form.images" :key="idx" class="image-preview">
            <img :src="img.url" alt="preview" />
            <button type="button" class="remove-img" @click="removeImage(idx)">&times;</button>
          </div>
        </div>
      </div>
      <div class="form-row">
        <label for="category">Categoria</label>
        <select id="category" v-model="form.category">
          <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
        </select>
      </div>
      <div class="form-row">
        <label for="tags">Tags</label>
        <div class="tags-input-row">
          <input id="tags" v-model="tagInput" @keydown.enter.prevent="addTag" type="text" placeholder="Aggiungi un tag e premi Invio" />
        </div>
        <div class="tags-list">
          <span v-for="(tag, idx) in form.tags" :key="tag" class="tag-chip">
            {{ tag }} <button type="button" class="remove-tag" @click="removeTag(idx)">&times;</button>
          </span>
        </div>
      </div>
      <div class="form-row">
        <label for="status">Stato articolo</label>
        <select id="status" v-model="form.status">
          <option value="disponibile">Disponibile</option>
          <option value="esaurito">Esaurito</option>
          <option value="non visibile">Non visibile</option>
        </select>
      </div>
      <button class="main-btn" type="submit">Aggiungi articolo</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const games = [
  'Fortnite',
  'Minecraft',
  'CS:GO',
  'Path Of Exile 2',
  'League of Legends',
  'Valorant',
]
const categories = [
  'Cosmetic',
  'Boosting',
  'Accounts',
  'In Game Items',
  'Non specificato'
]
const form = ref({
  title: '',
  game: games[0],
  otherGame: '',
  qty: 1,
  price: 0.01,
  images: [], // {file, url}
  category: categories[0],
  tags: [],
  status: 'disponibile'
})
const tagInput = ref('')
function addTag() {
  const val = tagInput.value.trim()
  if (val && !form.value.tags.includes(val)) {
    form.value.tags.push(val)
  }
  tagInput.value = ''
}
function removeTag(idx) {
  form.value.tags.splice(idx, 1)
}
function onImageChange(e) {
  const files = Array.from(e.target.files)
  for (const file of files) {
    if (form.value.images.length >= 3) break
    const url = URL.createObjectURL(file)
    form.value.images.push({ file, url })
  }
  e.target.value = ''
}
function removeImage(idx) {
  form.value.images.splice(idx, 1)
}
function onGameChange() {
  if (form.value.game !== 'Altro') form.value.otherGame = ''
}
function submitItem() {
  // Qui invia i dati al backend o mostra un alert di successo
  alert('Articolo aggiunto! (demo)')
}
</script>

<style scoped>
.add-item-layout {
  max-width: 650px;
  margin: 3.5rem auto;
  background: var(--surface);
  border-radius: 20px;
  box-shadow: 0 4px 32px #0003;
  padding: 3.5rem 2.5rem 2.5rem 2.5rem;
}
.add-item-title {
  color: var(--primary-light);
  font-size: 2.1rem;
  font-weight: 800;
  margin-bottom: 2.8rem;
  text-align: center;
  letter-spacing: 0.01em;
}
.add-item-form {
  display: flex;
  flex-direction: column;
  gap: 2.1rem;
}
.form-row {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  margin-bottom: 0.5rem;
}
label {
  font-size: 1.13rem;
  font-weight: 600;
  color: var(--primary-light);
  margin-bottom: 0.2rem;
  letter-spacing: 0.01em;
}
input[type="text"], input[type="number"], select, textarea {
  background: var(--surface-light);
  border: 2px solid var(--primary-light);
  border-radius: 10px;
  padding: 1.1rem 1.3rem;
  color: var(--on-surface);
  font-size: 1.13rem;
  outline: none;
  transition: border-color 0.2s;
  margin-bottom: 0.1rem;
}
input[type="text"]:focus, input[type="number"]:focus, select:focus, textarea:focus {
  border-color: var(--secondary);
}
textarea {
  min-height: 90px;
  resize: vertical;
}
.image-preview-row {
  display: flex;
  gap: 1.1rem;
  margin-top: 0.7rem;
}
.image-preview {
  position: relative;
  width: 80px;
  height: 80px;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid var(--primary-light);
  background: var(--surface-light);
}
.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.remove-img {
  position: absolute;
  top: 2px;
  right: 2px;
  background: var(--error, #cf6679);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 26px;
  height: 26px;
  font-size: 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}
.tags-input-row {
  display: flex;
  gap: 0.7rem;
}
.tags-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.7rem;
  margin-top: 0.4rem;
}
.tag-chip {
  background: var(--primary-light);
  color: var(--on-primary);
  border-radius: 10px;
  padding: 0.4rem 1.1rem 0.4rem 1rem;
  font-size: 1.08rem;
  display: flex;
  align-items: center;
  gap: 0.3rem;
}
.remove-tag {
  background: transparent;
  color: var(--on-primary);
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  margin-left: 0.2rem;
}
.main-btn {
  margin-top: 2.2rem;
  padding: 1.1rem 2.7rem;
  border: 2px solid var(--primary-light);
  background: var(--primary-light);
  color: var(--on-primary);
  font-weight: 800;
  border-radius: 10px;
  font-size: 1.18rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
  letter-spacing: 0.01em;
}
.main-btn:hover {
  background: var(--secondary);
  color: #18181c;
}
</style> 