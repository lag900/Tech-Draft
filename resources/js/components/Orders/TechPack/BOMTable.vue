<template>
  <div class="tp-card bom-card">
    <div class="card-header">BILL OF MATERIALS (BOM)</div>
    <div class="table-container">
      <table class="bom-table">
        <thead>
          <tr>
            <th class="al-l">ITEM</th>
            <th class="al-l">DESCRIPTION</th>
            <th class="al-l">COLOR</th>
            <th style="width: 60px">UNIT</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.name">
            <td class="al-l fw-900">{{ item.name }}</td>
            <td class="al-l">{{ item.desc || item.description }}</td>
            <td class="al-l">{{ item.color }}</td>
            <td class="fw-700">{{ item.unit }}</td>
          </tr>
          <tr v-if="!items?.length">
            <td colspan="4" class="empty-msg">No BOM items defined.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    items: Array,
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

  .bom-table {
    width: 100%;
    border-collapse: collapse;
  }

  .bom-table th {
    background: #f8fafc;
    color: #64748b;
    font-size: 9px;
    font-weight: 900;
    padding: 6px 8px;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
  }

  .bom-table td {
    border-bottom: 1px solid #f1f5f9;
    padding: 6px 8px;
    font-size: 10px;
    color: #1e293b;
  }

  .al-l {
    text-align: left !important;
  }
  .fw-900 {
    font-weight: 900;
  }
  .fw-700 {
    font-weight: 700;
  }

  .empty-msg {
    padding: 16px !important;
    color: #94a3b8;
    font-style: italic;
    text-align: center !important;
    font-size: 10px;
  }
</style>
