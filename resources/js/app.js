import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

// ── Arabic First: set default language before any component mounts ──
if (!localStorage.getItem('lang')) {
    localStorage.setItem('lang', 'ar');
}
// Apply direction immediately so there's no flash of LTR content
const lang = localStorage.getItem('lang');
document.documentElement.dir  = lang === 'ar' ? 'rtl' : 'ltr';
document.documentElement.lang = lang === 'ar' ? 'ar'  : 'en';

const app = createApp(App);

app.config.errorHandler = (err, vm, info) => {
    console.error(err, info);
    const errDiv = document.createElement('div');
    errDiv.style.cssText = 'color:red; background:#fee; padding:20px; position:fixed; top:0; left:0; right:0; z-index:99999; border-bottom:3px solid red; font-family:monospace; direction:ltr; text-align:left; max-height: 100vh; overflow: auto;';
    errDiv.innerHTML = '<strong>Vue Error:</strong> ' + err.message + '<br><br><pre style="white-space:pre-wrap;">' + err.stack + '</pre>';
    document.body.appendChild(errDiv);
};

app.use(router).mount('#app');
