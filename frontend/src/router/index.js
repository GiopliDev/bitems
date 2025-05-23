import { createRouter, createWebHistory } from 'vue-router'
import App from '../App.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: App
    },
    {
      path: '/catalogo',
      component: () => import('../views/Catalogo.vue')
    },
    {
      path: '/carrello',
      component: () => import('../views/Carrello.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/profilo',
      component: () => import('../views/Profilo.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/login',
      component: () => import('../views/Login.vue')
    }
  ]
})

// Navigation guard
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    // Emit an event to show login popup instead of redirecting
    const app = document.querySelector('#app')
    if (app) {
      app.__vue_app__.config.globalProperties.$root.$emit('show-login')
    }
    // Stay on the current page
    next(false)
  } else {
    next()
  }
})

export default router 