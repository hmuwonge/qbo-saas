/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import RegisterProduct from "../../Modules/Goods/Resources/js/RegisterProduct.vue";
import FiscalNoteFrame from "../../Modules/CreditMemos/Resources/js/FiscalNote/FiscalNoteFrame.vue";
import RegisterOpeningStock from "../../Modules/Goods/Resources/js/RegisterOpeningStock.vue";
import SystemInput from "./components/system-input.vue";
import PrimaryButton from "./components/PrimaryButton.vue";
import SecondaryButton from "./components/SecondaryButton.vue";
import InputLabel from "./components/InputLabel.vue";
import InputError from "./components/InputError.vue";
import Loader from "./components/Loader.vue";
import Checkbox from "./components/Checkbox.vue";
import DangerButton from "./components/DangerButton.vue";
import TextInput from "./components/TextInput.vue";
/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

// import ExampleComponent from './components/ExampleComponent.vue';
// app.component('example-component', ExampleComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });


app.component('register-product', RegisterProduct);
app.component('register-opening-stock', RegisterOpeningStock);
app.component('fiscal-note-frame', FiscalNoteFrame);
app.component('system-input', SystemInput);
app.component('text-input', TextInput);
app.component('primary-button', PrimaryButton);
app.component('secondary-button', SecondaryButton);
app.component('input-error', InputError);
app.component('input-label', InputLabel);
app.component('loader', Loader);
app.component('checkbox', Checkbox);
app.component('danger-button', DangerButton);

// for modules
/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
