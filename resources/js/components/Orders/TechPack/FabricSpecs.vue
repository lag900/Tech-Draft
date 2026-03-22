<template>
  <div class="tp-card fabric-specs-card">
    <div class="card-header">FABRIC SPECIFICATIONS</div>
    <div class="specs-dense-list">
      <div class="sd-item">
        <span class="sl">Fabric Type</span><span class="sv">{{ type || '---' }}</span>
      </div>
      <div class="sd-item">
        <span class="sl">GSM / Weight</span><span class="sv">{{ weight || '---' }}</span>
      </div>
      <div class="sd-item">
        <span class="sl">Composition</span><span class="sv">{{ composition || '---' }}</span>
      </div>
      <div class="sd-item">
        <span class="sl">Supplier</span><span class="sv">{{ supplier || '---' }}</span>
      </div>
      <div class="sd-item">
        <span class="sl">Fabric Width</span
        ><span class="sv">{{ width ? width + ' cm' : '---' }}</span>
      </div>
      <div class="sd-item">
        <span class="sl">Fabric Finish</span><span class="sv">{{ finish || 'Standard' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    type: String,
    weight: [String, Number],
    composition: String,
    supplier: String,
    width: [String, Number],
    finish: String,
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

  .specs-dense-list {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 16px;
  }

  .sd-item {
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
