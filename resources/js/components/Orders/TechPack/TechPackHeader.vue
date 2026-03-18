<template>
  <header class="tp-header">
    <div class="logo-area">
      <img src="../../../../images/logo.png" alt="Company Logo" class="brand-logo" />
      <div class="brand-meta">
        <h2 class="brand-name">TECHDRAFT</h2>
        <span class="brand-status">PROFESSIONAL MANUFACTURING</span>
      </div>
    </div>
    
    <div class="tp-identity">
      <h1 class="tp-main-title">MANUFACTURING TECH PACK</h1>
      <p class="tp-subtitle">Garment Specification & Production Document</p>
    </div>

    <div class="tp-meta-card">
      <div class="meta-row">
        <span class="meta-label">CODE:</span>
        <span class="meta-value">#{{ orderCode }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">VERSION:</span>
        <span class="meta-value">{{ version }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">DATE:</span>
        <span class="meta-value">{{ date }}</span>
      </div>
    </div>
  </header>
</template>

<script setup>
defineProps({
  orderCode: String,
  version: { type: String, default: '1.0' },
  date: String
});
</script>

<style scoped>
.tp-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #1a1a1a;
  padding-bottom: 8px;
  margin-bottom: 12px;
}

.logo-area {
  display: flex;
  align-items: center;
  gap: 10px;
}

.brand-logo {
  height: 32px;
  object-fit: contain;
}

.brand-name {
  font-size: 16px;
  font-weight: 900;
  margin: 0;
  line-height: 1;
  color: #1a1a1a;
  letter-spacing: -0.5px;
}

.brand-status {
  font-size: 7px;
  font-weight: 800;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.tp-identity {
  text-align: center;
}

.tp-main-title {
  font-size: 20px;
  font-weight: 900;
  margin: 0;
  color: #1a1a1a;
  letter-spacing: -0.5px;
}

.tp-subtitle {
  font-size: 10px;
  font-weight: 600;
  color: #64748b;
  margin-top: 2px;
}

.tp-meta-card {
  display: flex;
  flex-direction: column;
  gap: 2px;
  background: #f8fafc;
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  min-width: 130px;
}

.meta-row {
  display: flex;
  justify-content: space-between;
  font-size: 10px;
  font-weight: 800;
}

.meta-label {
  color: #94a3b8;
  margin-right: 8px;
}

.meta-value {
  color: #1e293b;
}
</style>
