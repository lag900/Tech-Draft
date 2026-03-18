<template>
  <div class="tab-pane animate-fade-in">
    <BaseCard :title="t('Internal Factory Notes & Remarks', 'ملاحظات المصنع الداخلية والتنبيهات')">
      <div class="notes-container">
        <textarea 
          v-model="form.factory_notes" 
          class="tech-notes-area" 
          :placeholder="t('Enter production tips, internal warnings, or reminders here...', 'أدخل نصائح الإنتاج أو التحذيرات الداخلية أو التذكيرات هنا...')"
          :disabled="!canEdit"
        ></textarea>
        
        <div class="notes-footer mt-1">
           <div class="tip-box">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.663 17h4.674M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3 3 0 0014 18.25V19a2 2 0 01-2 2h0a2 2 0 01-2-2v-.75a3 3 0 00-.547-1.803l-.548-.547z"/></svg>
              <span>{{ t('Notes entered here are for factory staff only and will be included in the internal Tech Pack document.', 'الملاحظات المدخلة هنا مخصصة لموظفي المصنع فقط وسيتم تضمينها في وثيقة ملف المواصفات الفنية الداخلية.') }}</span>
           </div>
        </div>
      </div>
    </BaseCard>
  </div>
</template>

<script setup>
import BaseCard from '../../../components/UI/BaseCard.vue';
import { useLang } from '../../../composables/useLang';

const props = defineProps({
  form: { type: Object, required: true },
  canEdit: { type: Boolean, default: false }
});

const { t } = useLang();
</script>

<style scoped>
.tech-notes-area {
    width: 100%;
    min-height: 300px;
    padding: 1.25rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    font-size: 0.9375rem;
    line-height: 1.6;
    color: #1e293b;
    resize: vertical;
    outline: none;
    transition: all 0.2s;
}

.tech-notes-area:focus {
    border-color: #3b82f6;
    background: white;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.05);
}

.tip-box {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    padding: 1rem;
    background: #eff6ff;
    border-radius: 10px;
    color: #1d4ed8;
    font-size: 0.8125rem;
    font-weight: 600;
}

.mt-1 { margin-top: 1rem; }
</style>
