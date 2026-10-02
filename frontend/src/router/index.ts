import { createRouter, createWebHistory } from 'vue-router'
import { pinia } from '../stores.ts' // Import the created instance
import { useAuthenticationStore } from '../stores/authenticationStore';
import AboutPage from '../pages/AboutPage.vue'
import HomePage from '../pages/HomePage.vue'
import AppLayout from '../layout/AppLayout.vue'
import LoginPage from '../pages/LoginPage.vue'
import LoginLayout from '../layout/LoginLayout.vue';
import NotFoundPage from '../pages/errors/NotFoundPage.vue';
import ErrorLayout from '../layout/ErrorLayout.vue';

const authenticationStore = useAuthenticationStore(pinia);    
const { updateToRedirect } = authenticationStore;


const routes = [
  { 
    path: '/', 
    meta: { 
      layout : AppLayout,
      requiresAuth : true,
     },
    component: HomePage 
  },
  { 
    path: '/about', 
    meta: { 
      layout : AppLayout,
      requiresAuth : true,
    },
    component: AboutPage 
  },
  { 
    path: '/login', 
    meta: { 
      layout : LoginLayout,
      requiresAuth : false,
     },
    component: LoginPage 
  },
  {
    path: '/:pathMatch(.*)*',
    meta: { 
      layout : ErrorLayout,
      requiresAuth : false,
     },
    component: NotFoundPage
  }
]

const router = createRouter({
  history: createWebHistory('/app/'),
  routes
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !authenticationStore.authenticated) {
    updateToRedirect(from.path)
    next('/login');
  } else {
    next();
  }
});

export default router