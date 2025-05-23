<template>
  <div class="chat-layout">
    <aside class="chat-sidebar">
      <h2 class="chat-title">Chat</h2>
      <input class="chat-search" type="text" placeholder="Search..." v-model="search" />
      <ul class="chat-list">
        <li v-for="chat in filteredChats" :key="chat.id" :class="{active: chat.id === selectedChatId}" @click="selectChat(chat.id)">
          <img :src="chat.avatar" class="chat-avatar" alt="avatar" />
          <div class="chat-info">
            <div class="chat-name">{{ chat.name }}</div>
            <div class="chat-last">{{ chat.lastMessage }}</div>
          </div>
        </li>
      </ul>
    </aside>
    <main class="chat-main" v-if="selectedChat">
      <div class="chat-header">
        <img :src="selectedChat.avatar" class="chat-avatar-large" alt="avatar" />
        <div class="chat-header-info">
          <div class="chat-header-name">{{ selectedChat.name }}</div>
        </div>
        <div class="chat-header-actions">
          <button class="icon-btn"><i class="fa-regular fa-image"></i></button>
          <button class="icon-btn"><i class="fa-solid fa-gear"></i></button>
          <button class="icon-btn"><i class="fa-regular fa-circle-question"></i></button>
        </div>
      </div>
      <div class="chat-messages">
        <div v-for="msg in selectedChat.messages" :key="msg.id" :class="['chat-message', msg.mine ? 'mine' : '']">
          <div class="chat-bubble">{{ msg.text }}</div>
          <div class="chat-time">{{ msg.time }}</div>
        </div>
      </div>
      <form class="chat-input-row" @submit.prevent="sendMessage">
        <input v-model="newMessage" class="chat-input" type="text" placeholder="Enter text here..." />
        <button class="send-btn" type="submit"><i class="fa-regular fa-paper-plane"></i></button>
      </form>
    </main>
    <main class="chat-main" v-else>
      <div class="chat-empty">Seleziona una chat per iniziare a conversare.</div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
// Demo dati statici, da sostituire con API
const chats = ref([
  {
    id: 1,
    name: 'Aiden Chavez',
    avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
    lastMessage: 'Project has been already finished and I have results to show you.',
    messages: [
      { id: 1, text: 'Hi Aiden, how are you? How is the project coming along?', time: '10:10 AM, Today', mine: true },
      { id: 2, text: 'Are we meeting today?', time: '10:12 AM, Today', mine: false },
      { id: 3, text: 'Project has been already finished and I have results to show you.', time: '10:15 AM, Today', mine: false }
    ]
  },
  {
    id: 2,
    name: 'Vincent Porter',
    avatar: 'https://randomuser.me/api/portraits/men/1.jpg',
    lastMessage: 'See you soon!',
    messages: [
      { id: 1, text: 'See you soon!', time: 'Yesterday', mine: false }
    ]
  },
  {
    id: 3,
    name: 'Monica Ward',
    avatar: 'https://randomuser.me/api/portraits/women/44.jpg',
    lastMessage: 'Thanks!',
    messages: [
      { id: 1, text: 'Thanks!', time: 'Yesterday', mine: false }
    ]
  }
])
const search = ref('')
const selectedChatId = ref(chats.value[0]?.id || null)
const newMessage = ref('')

const filteredChats = computed(() => {
  if (!search.value) return chats.value
  return chats.value.filter(c => c.name.toLowerCase().includes(search.value.toLowerCase()))
})
const selectedChat = computed(() => chats.value.find(c => c.id === selectedChatId.value))

function selectChat(id) {
  selectedChatId.value = id
}
function sendMessage() {
  if (!newMessage.value.trim() || !selectedChat.value) return
  selectedChat.value.messages.push({
    id: Date.now(),
    text: newMessage.value,
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ', Today',
    mine: true
  })
  newMessage.value = ''
}
</script>

<style scoped>
.chat-layout {
  display: flex;
  height: 80vh;
  background: var(--surface);
  border-radius: 16px;
  box-shadow: 0 2px 16px #0002;
  overflow: hidden;
  max-width: 1100px;
  margin: 2rem auto;
}
.chat-sidebar {
  width: 270px;
  background: var(--surface-light);
  border-right: 2px solid var(--primary-light);
  display: flex;
  flex-direction: column;
  padding: 1.5rem 0.7rem 1rem 0.7rem;
  gap: 1rem;
}
.chat-title {
  color: var(--primary-light);
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  margin-left: 0.5rem;
}
.chat-search {
  padding: 0.5rem 1rem;
  border-radius: 8px;
  border: 1.5px solid var(--primary-light);
  background: var(--surface);
  color: var(--on-surface);
  font-size: 1rem;
  margin-bottom: 0.7rem;
}
.chat-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.chat-list li {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 0.6rem 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.18s;
}
.chat-list li.active, .chat-list li:hover {
  background: var(--primary-light);
}
.chat-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--secondary);
}
.chat-info {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
}
.chat-name {
  font-weight: 600;
  color: var(--on-surface);
  font-size: 1.08rem;
}
.chat-last {
  color: #bbb;
  font-size: 0.98rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}
.chat-main {
  flex: none;
  width: 600px;
  min-width: 600px;
  max-width: 600px;
  display: flex;
  flex-direction: column;
  background: var(--surface);
  position: relative;
  min-width: 0;
  margin: 0 auto;
  height: 80vh;
}
.chat-header {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  padding: 1.2rem 2rem 1.2rem 1.2rem;
  border-bottom: 2px solid var(--primary-light);
  background: var(--surface-light);
}
.chat-avatar-large {
  width: 54px;
  height: 54px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--secondary);
}
.chat-header-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.chat-header-name {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--primary-light);
}
.chat-header-actions {
  display: flex;
  gap: 0.5rem;
}
.icon-btn {
  background: transparent;
  border: none;
  color: var(--primary-light);
  font-size: 1.3rem;
  cursor: pointer;
  padding: 0.3rem 0.5rem;
  border-radius: 6px;
  transition: background 0.18s;
}
.icon-btn:hover {
  background: var(--primary-light);
  color: var(--on-primary);
}
.chat-messages {
  flex: 1;
  min-height: 0;
  padding: 2rem 2.5rem 1rem 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  overflow-y: auto;
  background: var(--surface);
  width: 100%;
}
.chat-message {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  max-width: 60%;
}
.chat-message.mine {
  align-self: flex-end;
  align-items: flex-end;
}
.chat-bubble {
  background: var(--primary-light);
  color: var(--on-primary);
  padding: 0.8rem 1.2rem;
  border-radius: 16px;
  font-size: 1.05rem;
  box-shadow: 0 2px 8px #0001;
  margin-bottom: 0.2rem;
  max-width: 100%;
  word-break: break-word;
}
.chat-message.mine .chat-bubble {
  background: var(--secondary);
  color: #18181c;
}
.chat-time {
  font-size: 0.92rem;
  color: #bbb;
  margin-top: 0.1rem;
}
.chat-input-row {
  display: flex;
  align-items: center;
  padding: 1rem 2rem;
  border-top: 2px solid var(--primary-light);
  background: var(--surface-light);
  width: 100%;
  z-index: 2;
}
.chat-input {
  flex: 1;
  padding: 0.7rem 1.2rem;
  border-radius: 8px;
  border: 1.5px solid var(--primary-light);
  background: var(--surface);
  color: var(--on-surface);
  font-size: 1.08rem;
  margin-right: 1rem;
}
.send-btn {
  background: var(--primary-light);
  color: var(--on-primary);
  border: none;
  border-radius: 8px;
  padding: 0.7rem 1.2rem;
  font-size: 1.2rem;
  cursor: pointer;
  transition: background 0.18s, color 0.18s;
}
.send-btn:hover {
  background: var(--secondary);
  color: #18181c;
}
.chat-empty {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #bbb;
  font-size: 1.2rem;
}
@media (max-width: 900px) {
  .chat-layout {
    flex-direction: column;
    height: auto;
    max-width: 100vw;
  }
  .chat-sidebar {
    width: 100vw;
    border-right: none;
    border-bottom: 2px solid var(--primary-light);
    flex-direction: row;
    overflow-x: auto;
    gap: 0.5rem;
  }
  .chat-main {
    padding: 0;
  }
}
</style> 