<template>
  <div v-if="show" class="custom-alert-overlay">
    <div class="custom-alert">
      <div class="custom-alert-content">
        <h2 class="custom-alert-title">{{ title }}</h2>
        <p class="custom-alert-subtitle">{{ subtitle }}</p>
        <button class="custom-alert-button" @click="closeAlert">OK</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CustomAlert',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String,
      required: true
    }
  },
  methods: {
    closeAlert() {
      this.$emit('close')
    }
  }
}
</script>

<style scoped>
.custom-alert-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.custom-alert {
  background-color: white;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 90%;
}

.custom-alert-content {
  text-align: center;
}

.custom-alert-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 12px;
  color: #333;
}

.custom-alert-subtitle {
  font-size: 1rem;
  color: #666;
  margin-bottom: 24px;
}

.custom-alert-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.custom-alert-button:hover {
  background-color: #45a049;
}
</style>
