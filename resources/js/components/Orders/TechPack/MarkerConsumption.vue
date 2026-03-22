<template>
  <div class="tp-section marker-consumption">
    <div class="section-header">MARKER & CONSUMPTION</div>
    <div class="stats-grid">
      <div class="stat-card">
        <span class="label">Efficiency</span>
        <span class="value">{{ efficiency ? efficiency + '%' : '---' }}</span>
      </div>
      <div class="stat-card highlight">
        <span class="label">Net Consumption</span>
        <span class="value">{{ consumption ? consumption + 'm' : '---' }}</span>
      </div>
      <div class="stat-card">
        <span class="label">Lay Width</span>
        <span class="value">{{ width ? width + 'cm' : '---' }}</span>
      </div>
      <div class="stat-card">
        <span class="label">Marker Length</span>
        <span class="value">{{ length ? length + 'm' : '---' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    efficiency: [String, Number],
    consumption: [String, Number],
    width: [String, Number],
    length: [String, Number],
  });
</script>

<style scoped>
  .marker-consumption {
    margin-bottom: 25px;
  }

  .section-header {
    font-size: 11px;
    font-weight: 900;
    color: #666;
    margin-bottom: 12px;
    text-transform: uppercase;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
  }

  .stat-card {
    background: #fbfbfb;
    border: 1px solid #ddd;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    border-bottom: 3px solid #eee;
  }

  .stat-card.highlight {
    border-bottom-color: #4f46e5;
  }

  .label {
    display: block;
    font-size: 8px;
    font-weight: 800;
    color: #666;
    margin-bottom: 4px;
  }

  .value {
    font-size: 16px;
    font-weight: 900;
    color: #000;
  }
</style>
