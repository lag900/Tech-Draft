<template>
  <div class="tp-card sewing-sequence-card">
    <div class="card-header">SEWING OPERATION SEQUENCE</div>
    <div class="sequence-list">
      <div v-for="(step, index) in steps" :key="index" class="sequence-item">
        <span class="step-idx">{{ index + 1 }}</span>
        <span class="step-txt">{{ step }}</span>
      </div>
      <div v-if="!steps?.length" class="empty-msg">No sewing steps defined.</div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  steps: Array
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

.sequence-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sequence-item {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f8fafc;
  padding: 6px 10px;
  border-radius: 6px;
  border: 1px solid #f1f5f9;
}

.step-idx {
  background: #4f46e5;
  color: white;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 9px;
  font-weight: 900;
  flex-shrink: 0;
}

.step-txt {
  font-size: 10px;
  font-weight: 700;
  color: #1e293b;
}

.empty-msg {
  font-size: 10px;
  color: #94a3b8;
  font-style: italic;
  padding: 8px;
}
</style>
