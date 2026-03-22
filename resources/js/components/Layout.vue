<template>
  <ClientLayout v-if="user?.role === 'client'">
    <slot />
  </ClientLayout>
  <AdminLayout v-else>
    <slot />
  </AdminLayout>
</template>

<script setup>
  import { ref } from 'vue';
  import AdminLayout from './AdminLayout.vue';
  import ClientLayout from './ClientLayout.vue';

  const getUser = () => {
    try {
      return JSON.parse(localStorage.getItem('user') || 'null');
    } catch (e) {
      return null;
    }
  };
  const user = ref(getUser());
</script>
