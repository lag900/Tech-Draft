<template>
  <div class="visuals-grid-section">
    <div class="grid-title">DESIGN VISUALIZATION GRID</div>
    <div class="visual-grid">
      <div v-for="item in views" :key="item.label" class="visual-cell">
        <span class="cell-tag">{{ item.label }}</span>
        <div class="img-wrapper">
          <img v-if="item.src" :src="resolveImagePath(item.src)" />
          <div v-else class="null-img">NO DATA</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  views: Array // Array of { label, src }
});

const resolveImagePath = (path) => {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  if (path.startsWith('blob:')) return path;
  return '/storage/' + path;
};
</script>

<style scoped>
.visuals-grid-section {
  margin-bottom: 12px;
}

.grid-title {
  font-size: 13px;
  font-weight: 900;
  color: #1e293b;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 6px;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.visual-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.visual-cell {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 6px;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.cell-tag {
  position: absolute;
  top: 4px;
  left: 4px;
  font-size: 7px;
  font-weight: 900;
  background: #4f46e5;
  color: white;
  padding: 2px 4px;
  border-radius: 2px;
  z-index: 10;
}

.img-wrapper {
  width: 100%;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  border-radius: 4px;
  overflow: hidden;
}

.img-wrapper img {
  max-width: 100%;
  max-height: 160px;
  object-fit: contain;
}

.null-img {
  font-size: 10px;
  font-weight: 800;
  color: #cbd5e1;
}
</style>
