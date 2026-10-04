import { createApp } from 'vue';
import Main from './components/Main.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import '../css/app.css';

const path = window.location.pathname;

let component = Main;

if (path === '/login') {
    component = Login;
} else if (path === '/register') {
    component = Register;
}

createApp(component).mount('#app');