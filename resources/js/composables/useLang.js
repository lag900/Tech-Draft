/**
 * useLang — Global reactive language composable with i18n integration.
 *
 * Usage:
 *   import { useLang } from '@/composables/useLang'
 *   const { isRtl, t, toggleLang, setLang } = useLang()
 */
import { ref, watch, computed } from 'vue';
import { i18n } from '../i18n';
import axios from 'axios';

// ── Singleton state (module-level so it's shared across all imports) ──
const currentLocale = ref(localStorage.getItem('lang') || 'ar');
const isRtl = computed(() => currentLocale.value === 'ar');

// Sync i18n and document with currentLocale
watch(currentLocale, async (newLang) => {
    // Update vue-i18n
    i18n.global.locale.value = newLang;
    
    // Update DOM
    document.documentElement.dir  = newLang === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = newLang;
    
    // Persist
    localStorage.setItem('lang', newLang);
    
    // Sync with Laravel server (Removed non-existent endpoint to fix 405 error)
    // You can re-enable this later when backend endpoint exists
}, { immediate: true });

/**
 * Toggle between Arabic (RTL) and English (LTR).
 */
function toggleLang() {
    currentLocale.value = currentLocale.value === 'ar' ? 'en' : 'ar';
}

/**
 * Set language explicitly.
 * @param {'ar'|'en'} lang
 */
function setLang(lang) {
    currentLocale.value = lang;
}

/**
 * Translation helper — uses i18n if key is found, otherwise fallback to old pattern.
 * @param {string} keyOrEn  Translation key or English string
 * @param {string} ar       Arabic string (optional if using keys)
 * @returns {string}
 */
function t(keyOrEn, ar) {
    // If it looks like a key (no spaces, starts with letter) and exists in i18n
    if (keyOrEn && !keyOrEn.includes(' ') && i18n.global.te(keyOrEn)) {
        return i18n.global.t(keyOrEn);
    }
    // Fallback to existing manual pattern
    return currentLocale.value === 'ar' ? (ar || keyOrEn) : keyOrEn;
}

export function useLang() {
    return { 
        isRtl, 
        t, 
        toggleLang, 
        setLang, 
        locale: currentLocale,
        i18n: i18n.global 
    };
}
