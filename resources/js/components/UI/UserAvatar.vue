<template>
  <div class="user-avatar" :class="[sizeClass]" :style="avatarStyle">
    <img
      v-if="(src || user?.avatar) && !imageError"
      :src="src || '/storage/' + user.avatar"
      :alt="name"
      class="avatar-img"
      @error="handleImageError"
    />
    <span v-else class="avatar-letter">
      {{ initial }}
    </span>
  </div>
</template>

<script setup>
  import { computed, ref } from 'vue';

  const props = defineProps({
    user: {
      type: Object,
      required: true,
    },
    size: {
      type: String,
      default: 'md', // sm, md, lg
    },
    // Custom name fallback if user.name isn't what we want
    customName: {
      type: String,
      default: '',
    },
    src: {
      type: String,
      default: '',
    },
  });

  const imageError = ref(false);

  const name = computed(() => {
    if (props.customName) return props.customName;
    return props.user?.client?.brand_name || props.user?.name || '?';
  });

  const initial = computed(() => {
    return name.value.charAt(0).toUpperCase();
  });

  const sizeClass = computed(() => {
    return `size-${props.size}`;
  });

  const handleImageError = () => {
    imageError.value = true;
  };

  // Logic for colors from UserList.vue
  const ROLE_COLORS = {
    superadmin: '#4f46e5',
    admin: '#0ea5e9',
    manager: '#8b5cf6',
    production: '#f59e0b',
    qc: '#10b981',
    dataentry: '#64748b',
    client: '#1a3a5f',
  };

  const avatarStyle = computed(() => {
    if ((props.src || props.user?.avatar) && !imageError.value) return {};

    const role = props.user?.role || 'dataentry';
    const color = ROLE_COLORS[role] || ROLE_COLORS.dataentry;

    return {
      backgroundColor: `${color}20`,
      color: color,
    };
  });
</script>

<style scoped>
  .user-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    border-radius: 50%; /* Requirement 5: circular avatars */
    overflow: hidden;
    flex-shrink: 0;
    user-select: none;
  }

  .avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .avatar-letter {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }

  /* Sizes */
  .size-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
  }

  .size-md {
    width: 40px;
    height: 40px;
    font-size: 1.125rem;
  }

  .size-lg {
    width: 48px;
    height: 48px;
    font-size: 1.25rem;
  }

  .size-xl {
    width: 72px;
    height: 72px;
    font-size: 2rem;
  }

  .size-xxl {
    width: 100px;
    height: 100px;
    font-size: 2.5rem;
  }
</style>
