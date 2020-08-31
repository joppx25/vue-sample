require('./bootstrap');
import store from './store';
import Swal from 'vue-sweetalert2';
import Lang from 'laravel-vue-lang';
import 'sweetalert2/dist/sweetalert2.min.css';


window.Vue = require('vue');
Vue.use(Lang);
Vue.use(Swal);

// VueJs Components
Vue.component('guest-event-form', require('./components/guest/EventForm.vue').default);
Vue.component('guest-contact-form', require('./components/guest/ContactForm.vue').default);
Vue.component('thank-you', require('./components/guest/ThankYou.vue').default);
Vue.component('booking-details', require('./components/guest/BookingDetails.vue').default);
Vue.component('edit-booking-form', require('./components/guest/EditForm.vue').default);
Vue.component('event-form-field', require('./components/admin/events/FormField.vue').default);
Vue.component('event-list', require('./components/admin/events/List.vue').default);
Vue.component('event-calendar', require('./components/Admin/Events/CalendarComponent.vue').default);
Vue.component('booking-list', require('./components/Admin/Booking/List.vue').default);

const app = new Vue({
    el: '#app',
    store
});
