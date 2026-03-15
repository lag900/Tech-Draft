<template>
  <div class="base-card" :class="{ 'no-padding': noPadding, 'hoverable': hoverable }">
    <div v-if="$slots.header || title" class="card-header">
      <slot name="header">
        <h3 v-if="title" class="card-title">{{ title }}</h3>
        <p v-if="subtitle" class="card-subtitle">{{ subtitle }}</p>
      </slot>
      <div v-if="$slots.actions" class="card-actions">
        <slot name="actions" />
      </div>
    </div>
    <div class="card-body">
      <slot />
    </div>
    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  subtitle: String,
  noPadding: Boolean,
  hoverable: Boolean
});
</script>

<style scoped>
.base-card {
  background: white;
  border-radius: 12px;
  border: 1px solid #eef2f6;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  transition: all 0.2s ease;
  overflow: visible;
}

.base-card.hoverable:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px -5px rgba(0,0,0,0.04);
  border-color: #e2e8f0;
}

.card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1a3a5f;
  margin: 0;
}

.card-subtitle {
  font-size: 0.8125rem;
  color: #64748b;
  margin-top: 0.25rem;
}

.card-body {
  padding: 1.5rem;
}

.no-padding .card-body {
  padding: 0;
}

/* Only clip table-containing cards, not cards with dropdowns */
.base-card.no-padding {
  overflow: hidden;
}

.card-footer {
  padding: 1rem 1.5rem;
  background: #f8fafc;
  border-top: 1px solid #f1f5f9;
}
</style>
