import './bootstrap';

window.Vue = require('vue').default;

// Aquí puedes registrar tus componentes
Vue.component('productos-component', require('./components/ProductosComponent.vue').default);

const app = new Vue({
    el: '#app',
});