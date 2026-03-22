<template>
  <div class="tp-card model-anatomy-card">
    <div class="card-header">MODEL ANATOMY (PIECE LIST)</div>
    <div class="table-scroll-shield">
      <table class="anatomy-table">
        <thead>
          <tr>
            <th class="al-l">PART VISUAL</th>
            <th class="al-l">PIECE NAME</th>
            <th class="al-l">PART DESCRIPTION</th>
            <th style="width: 50px">QTY</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, idx) in items" :key="idx">
            <td>
              <div class="anatomy-thumbnail">
                <img
                  v-if="item.img || item.image || item.image_path"
                  :src="resolveImagePath(item.img || item.image || item.image_path)"
                />
                <div v-else class="null-thumb">---</div>
              </div>
            </td>
            <td class="al-l fw-900">{{ item.piece || item.piece_name }}</td>
            <td class="al-l description-cell">
              {{ item.part || item.part_description || item.part_name }}
            </td>
            <td class="fw-900 highlight-qty">{{ item.qty || item.quantity || item.count }}</td>
          </tr>
          <tr v-if="!items?.length">
            <td colspan="4" class="empty-msg">No pieces defined for this model.</td>
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

  const resolveImagePath = (path) => {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    if (path.startsWith('blob:')) return path;
    return '/storage/' + path;
  };
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

  .anatomy-table {
    width: 100%;
    border-collapse: collapse;
  }

  .anatomy-table th {
    background: #f8fafc;
    color: #64748b;
    font-size: 9px;
    font-weight: 900;
    padding: 6px 8px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
  }

  .anatomy-table td {
    border-bottom: 1px solid #f1f5f9;
    padding: 8px;
    font-size: 10px;
    color: #1e293b;
  }

  .anatomy-thumbnail {
    width: 50px;
    height: 50px;
    border: 1px solid #f1f5f9;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
  }

  .anatomy-thumbnail img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  .description-cell {
    color: #64748b;
    font-weight: 600;
  }

  .highlight-qty {
    color: #4f46e5;
  }
  .al-l {
    text-align: left !important;
  }
  .fw-900 {
    font-weight: 900;
  }

  .empty-msg {
    padding: 24px !important;
    color: #94a3b8;
    font-style: italic;
    text-align: center !important;
    font-size: 11px;
  }
</style>
