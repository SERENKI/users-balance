import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('./Components/HomeView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('./Components/AuthForm.vue'),
    meta: { guestOnly: true }
  },
  {
    path: '/transactions',
    name: 'transactions',
    component: () => import('./Components/TransactionList.vue'),
    meta: {
      requiresAuth: true,
      title: 'История операций'
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_token')

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  }
  else if (to.meta.guestOnly && isAuthenticated) {
    next('/')
  }
  else {
    document.title = to.meta.title || 'Финансовый менеджер'
    next()
  }
})

export default router
