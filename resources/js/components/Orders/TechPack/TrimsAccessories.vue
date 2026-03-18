<template>
  <div class="tp-card trims-card">
    <div class="card-header">TRIMS & ACCESSORIES</div>
    <div class="trims-grid">
      <div class="trim-item"><span class="sl">Zipper</span><span class="sv">{{ zipper || '---' }}</span></div>
      <div class="trim-item"><span class="sl">Buttons</span><span class="sv">{{ buttons || '---' }}</span></div>
      <div class="trim-item"><span class="sl">Elastic / Cord</span><span class="sv">{{ elastic || '---' }}</span></div>
      <div class="trim-item"><span class="sl">Ribbing</span><span class="sv">{{ ribbing || '---' }}</span></div>
      <div class="trim-item"><span class="sl">Other</span><span class="sv">{{ other || '---' }}</span></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  zipper: String,
  buttons: String,
  elastic: String,
  ribbing: String,
  other: String
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

.trims-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 4px;
}

.trim-item {
  display: flex;
  justify-content: space-between;
  border-bottom: 1px solid #f1f5f9;
  padding: 4px 0;
}

.sl {
  font-size: 10px;
  font-weight: 700;
  color: #64748b;
}

.sv {
  font-size: 10px;
  font-weight: 800;
  color: #1e293b;
}
</style>
