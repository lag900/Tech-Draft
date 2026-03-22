<template>
  <div class="tp-card labeling-card">
    <div class="card-header">LABELS & BRANDING</div>
    <div class="label-dense-list">
      <div class="ld-item">
        <span class="sl">Main Label</span><span class="sv">{{ mainLabel || 'Center Back' }}</span>
      </div>
      <div class="ld-item">
        <span class="sl">Size Label</span
        ><span class="sv">{{ sizeLabel || 'With Main Label' }}</span>
      </div>
      <div class="ld-item">
        <span class="sl">Care Label</span><span class="sv">{{ careLabel || 'Side Seam' }}</span>
      </div>
      <div class="ld-item full">
        <span class="sl">Placement</span><span class="sv">{{ placement || 'Standard' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    mainLabel: String,
    sizeLabel: String,
    careLabel: String,
    placement: String,
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

  .label-dense-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2px;
  }

  .ld-item {
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
