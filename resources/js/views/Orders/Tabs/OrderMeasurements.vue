<template>
  <div class="tab-pane animate-fade-in">
    <BaseCard :title="t('Measurement Spec Chart', 'جدول قياسات المواصفات الفنية')">
      <div class="table-container-compact">
        <table class="tech-table">
          <thead>
            <tr>
              <th class="sticky-col">{{ t('Point of Measure', 'نقطة القياس') }}</th>
              <th v-for="size in availableSizes" :key="size" class="text-center">{{ size }}</th>
              <th class="text-center">{{ t('Tol. (+/-)', 'التفاوت') }}</th>
              <th>{{ t('Spec Notes', 'ملاحظات المواصفات') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="point in points" :key="point.id">
              <td class="font-bold sticky-col">{{ point.point_of_measure }}</td>
              <td v-for="size in availableSizes" :key="size" class="text-center m-val">
                  <!-- logic: if it matches base size show value, else show value + grading -->
                  {{ getValForSize(point, size) }}
              </td>
              <td class="text-center text-muted">{{ point.tolerance || '±1.0' }}</td>
              <td class="text-xs text-slate-500">{{ point.notes || '---' }}</td>
            </tr>
            <tr v-if="points.length === 0">
               <td :colspan="availableSizes.length + 3" class="text-center py-8 text-muted italic">
                  {{ t('No measurements point defined for this style.', 'لم يتم تعريف نقاط قياس لهذا الموديل.') }}
               </td>
            </tr>
          </tbody>
        </table>
      </div>
    </BaseCard>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import BaseCard from '../../../components/UI/BaseCard.vue';
import { useLang } from '../../../composables/useLang';

const props = defineProps({
  order: { type: Object, required: true },
});

const { t } = useLang();

const points = computed(() => props.order.points_of_measure || []);

const availableSizes = computed(() => {
    if (props.order.sizes && props.order.sizes.length > 0) {
        return props.order.sizes.map(s => s.size_name);
    }
    // Fallback if production_details has sizes as object
    if (props.order.production_details?.sizes) {
        return Object.keys(props.order.production_details.sizes);
    }
    return ['Base'];
});

const getValForSize = (point, size) => {
    // Basic logic: return base value for now. 
    // In advanced mode, we calculate base + (grading * offset)
    if (!point.dimension_value) return '---';
    return point.dimension_value;
};
</script>

<style scoped>
.tech-table { width: 100%; border-collapse: collapse; }
.tech-table th { 
    text-align: left; 
    font-size: 0.7rem; 
    color: #475569; 
    text-transform: uppercase; 
    padding: 1rem 0.75rem; 
    background: #f8fafc; 
    font-weight: 800; 
    border-bottom: 2px solid #e2e8f0;
}
.tech-table td { 
    padding: 0.875rem 0.75rem; 
    border-bottom: 1px solid #f1f5f9; 
    font-size: 0.8125rem; 
    color: #1e293b;
}

.sticky-col {
    position: sticky;
    left: 0;
    background: inherit;
    z-index: 10;
    box-shadow: 2px 0 5px rgba(0,0,0,0.02);
}

.font-bold { font-weight: 700; color: #0f172a; }
.text-center { text-align: center; }
.text-muted { color: #94a3b8; }
.text-xs { font-size: 0.75rem; }
.m-val { font-weight: 800; color: #2563eb; }

.table-container-compact {
    overflow-x: auto;
    border-radius: 8px;
    border: 1px solid #f1f5f9;
}
</style>
