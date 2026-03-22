<template>
  <div class="tp-card color-palette-card">
    <div class="card-header">COLOR PALETTE</div>
    <div class="palette-grid">
      <div v-for="color in colors" :key="color.hex || color.color_hex" class="color-item">
        <div class="swatch" :style="{ backgroundColor: color.hex || color.color_hex }"></div>
        <div class="color-meta">
          <span class="color-name">{{ color.name || color.color_name }}</span>
          <span class="color-code">{{ color.hex || color.color_hex || color.color_code }}</span>
        </div>
      </div>
      <div v-if="!colors?.length" class="empty-msg">No colors specified.</div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    colors: Array,
  });
</script>

<style scoped>
  .tp-card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    padding: 12px;
    margin-bottom: 12px;
  }

  .card-header {
    font-size: 13px;
    font-weight: 900;
    color: #1e293b;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 6px;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .palette-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }

  .color-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 10px;
    background: #f8fafc;
    border-radius: 6px;
    border: 1px solid #f1f5f9;
  }

  .swatch {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  }

  .color-meta {
    display: flex;
    flex-direction: column;
  }

  .color-name {
    font-size: 10px;
    font-weight: 800;
    color: #1e293b;
  }

  .color-code {
    font-size: 9px;
    font-weight: 600;
    color: #94a3b8;
    font-family: 'JetBrains Mono', monospace;
  }

  .empty-msg {
    grid-column: span 2;
    font-size: 10px;
    color: #94a3b8;
    font-style: italic;
    text-align: center;
    padding: 10px;
  }
</style>
