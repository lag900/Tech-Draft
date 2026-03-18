import { createI18n } from 'vue-i18n';
import en from './locales/en';
import ar from './locales/ar';

const messages = {
  en,
  ar
};

const locale = localStorage.getItem('lang') || 'ar';

export const i18n = createI18n({
  legacy: false, // use Composition API
  locale: locale,
  fallbackLocale: 'en',
  messages
});
