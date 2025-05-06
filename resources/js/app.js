import { createApp } from 'vue';
import axios from 'axios';
import router from './router'
import App from './Components/App.vue';
import AuthForm from './Components/AuthForm.vue';
import HomeView from './Components/HomeView.vue';
import TransactionList from './Components/TransactionList.vue';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min'
import '../css/app.css';

axios.defaults.baseURL = import.meta.env.VITE_APP_API_URL || 'http://localhost:8000';
axios.defaults.withCredentials = true;

axios.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

const app = createApp(App);
app.use(router)

app.component('AuthForm', AuthForm);
app.component('HomeView', HomeView);
app.component('TransactionList', TransactionList);

app.mount('#app');
