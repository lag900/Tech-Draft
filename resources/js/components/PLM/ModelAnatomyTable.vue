<template>
  <div class="anatomy-table-wrapper" :class="{ 'rtl': isRtl }">
    <div class="table-header-row mb-1">
      <h3 class="section-title-small">{{ t('Model Anatomy Table', 'جدول تشريح الموديل') }}</h3>
      <BaseButton size="sm" variant="white" @click="addRow">
        <template #icon-left>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        </template>
        {{ t('Add Row', 'إضافة سطر') }}
      </BaseButton>
    </div>

    <div class="modern-table-responsive">
      <table class="anatomy-table">
        <thead>
          <tr>
            <th>{{ t('Piece Name', 'اسم القطعة') }}</th>
            <th>{{ t('Part Name', 'اسم الجزء') }}</th>
            <th>{{ t('Count', 'العدد') }}</th>
            <th>{{ t('Image', 'الصورة') }}</th>
            <th class="text-right">{{ t('Actions', 'الإجراءات') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, index) in rows" :key="index">
            <td>
              <BaseInput 
                v-model="row.piece_name" 
                :placeholder="t('Ex: Shirt', 'مثال: قميص')" 
                @input="handlePieceInput(index)"
                size="sm"
              />
            </td>
            <td>
              <div class="part-input-wrapper">
                <BaseInput 
                  v-model="row.part_name" 
                  :placeholder="t('Ex: Front Panel', 'مثال: الصدر')" 
                  size="sm"
                  @focus="fetchSuggestions(row.piece_name, index)"
                />
                <div v-if="activeSuggestions[index]?.length" class="suggestions-popover">
                  <div 
                    v-for="sug in activeSuggestions[index]" 
                    :key="sug" 
                    class="sug-item"
                    @click="row.part_name = sug; activeSuggestions[index] = []"
                  >
                    {{ sug }}
                  </div>
                </div>
              </div>
            </td>
            <td>
              <BaseInput 
                v-model="row.count" 
                type="number" 
                min="1" 
                size="sm"
              />
            </td>
            <td>
              <div class="anatomy-image-upload">
                <input type="file" :id="'anatomy-img-' + index" hidden @change="handleImage(index, $event)">
                <label :for="'anatomy-img-' + index" class="upload-trigger-sm">
                  <img v-if="row.preview" :src="row.preview" class="anatomy-thumb">
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7M16 5l2 2 4-4"/></svg>
                </label>
              </div>
            </td>
            <td class="text-right">
              <button class="icon-btn-danger" @click="removeRow(index)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-1-5 flex justify-end">
      <BaseButton :loading="saving" @click="saveAnatomy">{{ t('Save Table', 'حفظ الجدول') }}</BaseButton>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import BaseInput from '../UI/BaseInput.vue';
import BaseButton from '../UI/BaseButton.vue';
import axios from 'axios';

const props = defineProps({
  orderId: { type: [Number, String], required: true },
  isRtl: Boolean
});

const rows = ref([]);
const saving = ref(false);
const activeSuggestions = reactive({});

const t = (en, ar) => props.isRtl ? ar : en;

onMounted(async () => {
  const res = await axios.get(`/api/model-anatomies?order_id=${props.orderId}`, {
    headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
  });
  if (res.data.length) {
    rows.value = res.data.map(r => ({ 
      ...r, 
      count: r.count || 1,
      preview: r.image_path ? `/storage/${r.image_path}` : null 
    }));
  } else {
    addRow();
  }
});

const addRow = () => {
  const lastRow = rows.value[rows.value.length - 1];
  rows.value.push({
    piece_name: lastRow ? lastRow.piece_name : '', // Smart suggestion: Copy previous piece name
    part_name: '',
    count: 1,
    image: null,
    preview: null
  });
};

const removeRow = (idx) => rows.value.splice(idx, 1);

const handlePieceInput = (idx) => {
  // Can be used for broader logic if needed
};

const fetchSuggestions = async (pieceName, idx) => {
  if (!pieceName) return;
  try {
    const res = await axios.get(`/api/model-anatomies/suggest?piece_name=${pieceName}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    activeSuggestions[idx] = res.data;
  } catch (e) {}
};

const handleImage = (idx, e) => {
  const file = e.target.files[0];
  if (!file) return;
  rows.value[idx].image = file;
  const reader = new FileReader();
  reader.onload = (re) => rows.value[idx].preview = re.target.result;
  reader.readAsDataURL(file);
};

const saveAnatomy = async () => {
  saving.value = true;
  try {
    const headers = { 
      'Content-Type': 'multipart/form-data',
      Authorization: `Bearer ${localStorage.getItem('auth_token')}` 
    };

    // Save each row (For simplicity in this turn, we'll iterate. Ideally use a bulk endpoint)
    for (const row of rows.value) {
      const fd = new FormData();
      fd.append('order_id', props.orderId);
      fd.append('piece_name', row.piece_name);
      fd.append('part_name', row.part_name);
      fd.append('count', row.count);
      if (row.image) fd.append('image', row.image);

      if (row.id) {
        await axios.post(`/api/model-anatomies/${row.id}?_method=PUT`, fd, { headers });
      } else {
        await axios.post('/api/model-anatomies', fd, { headers });
      }
    }
    alert(t('Saved successfully', 'تم الحفظ بنجاح'));
  } catch (e) {
    alert('Error saving anatomy table');
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
.anatomy-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
.anatomy-table th { background: #f8fafc; padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; }
.anatomy-table td { padding: 0.75rem 0.5rem; border-bottom: 1px solid #f1f5f9; }

.upload-trigger-sm { width: 40px; height: 40px; border-radius: 8px; border: 2px dashed #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #94a3b8; overflow: hidden; }
.anatomy-thumb { width: 100%; height: 100%; object-fit: cover; }

.part-input-wrapper { position: relative; }
.suggestions-popover { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 50; max-height: 150px; overflow-y: auto; }
.sug-item { padding: 0.5rem 1rem; cursor: pointer; font-size: 0.8125rem; font-weight: 600; color: #1a3a5f; }
.sug-item:hover { background: #f0f9ff; color: #0ea5e9; }

.icon-btn-danger { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #fee2e2; background: #fff1f2; color: #ef4444; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; }
.btn-danger:hover { background: #fca5a5; color: white; }

.rtl .anatomy-table th, .rtl .anatomy-table td { text-align: right; }
.rtl .suggestions-popover { text-align: right; }
</style>
