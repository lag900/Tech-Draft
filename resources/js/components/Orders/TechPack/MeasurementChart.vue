<template>
  <div class="test-results-chart">
    <div class="chart-header">MEASUREMENT SPECIFICATIONS (CM)</div>
    <div class="chart-container">
      <table class="m-table">
        <thead>
          <tr>
            <th class="al-l">POINT OF MEASURE</th>
            <th v-for="size in sizes" :key="size.name">{{ size.name }}</th>
            <th style="width: 80px">SPEC</th>
            <th style="width: 50px">TOL (+/-)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(point, idx) in points" :key="idx">
            <td class="al-l fw-900">{{ point.point || point.point_of_measure }}</td>
            <td v-for="size in sizes" :key="size.name" class="grad-cell">
              {{ point.grading ? point.grading[size.name] || '---' : '---' }}
            </td>
            <td class="spec-val highlight">{{ point.value || point.dimension_value || '---' }}</td>
            <td class="tol-val">{{ point.tol || point.tolerance || '0.5 cm' }}</td>
          </tr>
          <tr v-if="!points?.length">
            <td :colspan="sizes.length + 3" class="empty-msg">No measurement data defined.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
defineProps({
  sizes: Array,
  points: Array 
});
</script>

<style scoped>
.test-results-chart {
  margin-bottom: 15px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 12px;
}

.chart-header {
  font-size: 13px;
  font-weight: 900;
  color: #1e293b;
  border-bottom: 1.5px solid #1e293b;
  padding-bottom: 6px;
  margin-bottom: 12px;
  text-transform: uppercase;
}

.m-table {
  width: 100%;
  border-collapse: collapse;
}

.m-table th {
  background: #f8fafc;
  color: #64748b;
  font-size: 9px;
  font-weight: 900;
  padding: 6px;
  border: 1px solid #e2e8f0;
}

.m-table td {
  border: 1px solid #f1f5f9;
  padding: 6px;
  font-size: 10px;
  text-align: center;
}

.al-l { text-align: left !important; }
.fw-900 { font-weight: 900; color: #1e293b; }
.highlight { background: #f0f9ff; font-weight: 900; color: #0369a1; }
.tol-val { color: #94a3b8; font-weight: 700; }

.empty-msg {
  padding: 20px !important;
  color: #94a3b8;
  font-style: italic;
  font-size: 11px;
}
</style>
