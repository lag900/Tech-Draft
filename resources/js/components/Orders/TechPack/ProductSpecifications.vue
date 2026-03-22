<template>
  <div class="product-specs-card">
    <div class="specs-layout">
      <!-- Left: Product Hero -->
      <div class="hero-pane">
        <span class="pane-label">PRODUCT IMAGE</span>
        <img :src="resolveImagePath(image)" alt="Product" class="hero-img" />
      </div>

      <!-- Right: Identity Details -->
      <div class="details-pane">
        <div class="pane-header">PRODUCT INFORMATION</div>
        <div class="specs-list">
          <div class="spec-item">
            <span class="l">Product Name</span>
            <span class="v fw-900">{{ styleName }}</span>
          </div>
          <div class="spec-item">
            <span class="l">Client / Brand</span>
            <span class="v">{{ clientName }}</span>
          </div>
          <div class="spec-item">
            <span class="l">Category</span>
            <span class="v">{{ category }}</span>
          </div>
          <div class="spec-item">
            <span class="l">Season / Cycle</span>
            <span class="v">{{ season }}</span>
          </div>
          <div class="spec-item">
            <span class="l">Target Quantity</span>
            <span class="v highlight">{{ totalQuantity }} Units</span>
          </div>
          <div class="spec-item">
            <span class="l">Status</span>
            <span class="v status-label" :class="(status || '').toLowerCase()">{{ status }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    image: String,
    styleName: String,
    clientName: String,
    category: String,
    season: String,
    totalQuantity: Number,
    status: String,
  });

  const resolveImagePath = (path) => {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    if (path.startsWith('blob:')) return path;
    return '/storage/' + path;
  };
</script>

<style scoped>
  .product-specs-card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    margin-bottom: 12px;
  }

  .specs-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    height: 340px;
  }

  .hero-pane {
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 12px;
  }

  .pane-label {
    position: absolute;
    top: 8px;
    left: 8px;
    font-size: 7px;
    font-weight: 900;
    background: #1a1a1a;
    color: white;
    padding: 2px 4px;
    border-radius: 2px;
    letter-spacing: 0.5px;
  }

  .hero-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  .details-pane {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
  }

  .pane-header {
    font-size: 13px;
    font-weight: 900;
    color: #1e293b;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 8px;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .specs-list {
    display: grid;
    gap: 8px;
  }

  .spec-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px dashed #f1f5f9;
    padding-bottom: 6px;
  }

  .l {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
  }

  .v {
    font-size: 11px;
    font-weight: 800;
    color: #1e293b;
  }

  .fw-900 {
    font-weight: 900;
  }

  .highlight {
    color: #4f46e5;
  }

  .status-label {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 9px;
    text-transform: uppercase;
    font-weight: 900;
    background: #f1f5f9;
  }

  .status-label.draft {
    background: #fee2e2;
    color: #991b1b;
  }
  .status-label.approved {
    background: #dcfce7;
    color: #166534;
  }
  .status-label.production {
    background: #e0e7ff;
    color: #3730a3;
  }
</style>
