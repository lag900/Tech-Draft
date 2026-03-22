<template>
  <div id="app" class="app-container">
    <router-view />
  </div>
</template>

<script setup>
  import { useLang } from './composables/useLang';

  // Initialize global language reactivity and document dir/lang
  useLang();
</script>

<style>
  :root {
    --primary: #ffffff;
    --accent: #0ea5e9;
    --accent-light: #eff6ff;
    --text-dark: #1a3a5f;
    --text-gray: #64748b;
    --bg-gray: #f9fafb;
    --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --radius-lg: 12px;
    --radius-md: 8px;
    --font-ar: 'Tajawal', sans-serif;
    --font-en: 'Instrument Sans', system-ui, sans-serif;
  }

  html[dir='rtl'] {
    font-family: var(--font-ar);
  }

  html[dir='ltr'] {
    font-family: var(--font-en);
  }

  body {
    margin: 0;
    font-family: var(--font-ar);
    background-color: var(--bg-gray);
    color: var(--text-dark);
    -webkit-font-smoothing: antialiased;
  }

  html[dir='rtl'] body {
    font-family: var(--font-ar);
  }

  html[dir='ltr'] body {
    font-family: var(--font-en);
  }

  .app-container {
    min-height: 100vh;
  }

  /* RTL-specific tweaks */
  [dir='rtl'] * {
    font-family: var(--font-ar);
  }
</style>
