<template>
  <div class="tp-card construction-card">
    <div class="card-header">CONSTRUCTION SPECIFICATIONS</div>
    <div class="specs-grid">
      <div class="sg-item"><span class="sl">Stitch Type</span><span class="sv">{{ stitchType || '---' }}</span></div>
      <div class="sg-item"><span class="sl">Seam Type</span><span class="sv">{{ seamType || '---' }}</span></div>
      <div class="sg-item"><span class="sl">SPI Target</span><span class="sv">{{ spi || '10-12' }}</span></div>
      <div class="sg-item"><span class="sl">Thread Type</span><span class="sv">{{ threadType || 'Tex 24 Spun Poly' }}</span></div>
      <div class="sg-item"><span class="sl">Reinforcement</span><span class="sv">{{ reinforcement || 'Standard' }}</span></div>
      <div class="sg-item"><span class="sl">Seam Allowance</span><span class="sv">{{ allowance || '1.0 cm' }}</span></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  stitchType: String,
  seamType: String,
  spi: String,
  threadType: String,
  reinforcement: String,
  allowance: String
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

.specs-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4px 16px;
}

.sg-item {
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
