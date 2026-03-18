<template>
  <div class="tp-card factory-notes-card">
    <div class="card-header">INTERNAL FACTORY NOTES</div>
    <div class="notes-body" v-if="notes" v-html="formattedNotes"></div>
    <div v-else class="empty-msg">No additional technical instructions provided.</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  notes: String
});

const formattedNotes = computed(() => {
  if (!props.notes) return '';
  return props.notes.replace(/\n/g, '<br>');
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
  border-bottom: 2px solid #1e293b;
  padding-bottom: 6px;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.notes-body {
  font-size: 11px;
  line-height: 1.5;
  color: #334155;
  min-height: 80px;
}

.empty-msg {
  font-size: 11px;
  color: #94a3b8;
  font-style: italic;
  padding: 10px 0;
}
</style>
