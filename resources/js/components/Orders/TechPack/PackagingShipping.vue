<template>
  <div class="tp-card packaging-card">
    <div class="card-header">PACKAGING & SHIPPING DETAILS</div>
    <div class="pkg-horizontal-grid">
      <div class="ph-item">
        <span class="sl">Packaging Type</span><span class="sv">{{ packType || '---' }}</span>
      </div>
      <div class="ph-item">
        <span class="sl">Folding Method</span><span class="sv">{{ foldingMethod || '---' }}</span>
      </div>
      <div class="ph-item">
        <span class="sl">Carton Quantity</span><span class="sv">{{ cartonQty || '---' }}</span>
      </div>
      <div class="ph-item">
        <span class="sl">Barcode</span
        ><span class="sv highlight">{{ barcodeRequired ? 'REQUIRED' : 'NONE' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    packType: String,
    foldingMethod: String,
    cartonQty: [String, Number],
    barcodeRequired: Boolean,
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

  .pkg-horizontal-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px 16px;
  }

  .ph-item {
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

  .highlight {
    color: #4f46e5 !important;
  }
</style>
