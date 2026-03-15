<template>
  <div id="app" class="app-container">
    <router-view />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  // Set Arabic as default language if no preference saved yet
  if (!localStorage.getItem('lang')) {
    localStorage.setItem('lang', 'ar');
  }
  const isRtl = localStorage.getItem('lang') === 'ar';
  document.documentElement.dir = isRtl ? 'rtl' : 'ltr';
  document.documentElement.lang = isRtl ? 'ar' : 'en';
});
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

html[dir="rtl"] {
  font-family: var(--font-ar);
}

html[dir="ltr"] {
  font-family: var(--font-en);
}

body {
  margin: 0;
  font-family: var(--font-ar);
  background-color: var(--bg-gray);
  color: var(--text-dark);
  -webkit-font-smoothing: antialiased;
}

html[dir="rtl"] body {
  font-family: var(--font-ar);
}

html[dir="ltr"] body {
  font-family: var(--font-en);
}

.app-container {
  min-height: 100vh;
}

.card {
  background: var(--primary);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

button {
  cursor: pointer;
  border: none;
  font-weight: 500;
  transition: all 0.2s;
  font-family: inherit;
}

button.primary {
  background-color: var(--accent);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: var(--radius-md);
}

button.primary:hover {
  filter: brightness(1.1);
}

input, select, textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: var(--radius-md);
  margin-top: 0.5rem;
  background: white;
  font-family: inherit;
}

label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-gray);
}

/* RTL-specific tweaks */
[dir="rtl"] * {
  font-family: var(--font-ar);
}
</style>
