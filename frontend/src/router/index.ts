import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('../views/AboutView.vue')
  },
  {
    path: '/catalogo',
    name: 'catalogo',
    component: () => import('../views/Catalogo.vue')
  },
  {
    path: '/itemDetail',
    name: 'itemDetail',
    component: () => import('../views/ItemDetail.vue')
  },
  {
    path: '/chats',
    name: 'chats',
    component: () => import('../views/ChatsView.vue')
  },
  {
    path: '/addItem',
    name: 'addItem',
    component: () => import('../views/AddItemView.vue')
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('../views/ProfileView.vue'),
    props: (route) => ({ id: route.query.id })
  },
  {
    path: '/profilo',
    redirect: to => ({ path: '/profile', query: to.query })
  },
  {
    path: '/chi-siamo',
    name: 'chi-siamo',
    component: () => import('../views/ChiSiamoView.vue')
  },
  {
    path: '/trending',
    name: 'trending',
    component: () => import('../views/TrendingView.vue')
  },
  {
    path: '/history',
    name: 'history',
    component: () => import('../views/HistoryView.vue')
  },
  {
    path: '/bookmarks',
    name: 'bookmarks',
    component: () => import('../views/BookmarksView.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue')
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('../views/AdminPanel.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    // Emit an event to show login popup instead of redirecting
    const app = document.querySelector('#app')
    if (app) {
      const vueApp = (app as any).__vue_app__
      if (vueApp && vueApp.config && vueApp.config.globalProperties) {
        const root = vueApp.config.globalProperties.$root
        if (root) {
          root.$emit('show-login')
        }
      }
    }
    // Stay on the current page
    next(false)
  } else {
    next()
  }
})

export default router
