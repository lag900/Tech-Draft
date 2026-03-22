<template>
  <div class="measurement-table-container" :class="{ rtl: isRtl }">
    <table class="m-table">
      <thead>
        <tr>
          <th>{{ t('Point of Measure', 'نقطة القياس') }}</th>
          <th class="width-fixed">{{ t('Dimension (cm)', 'الأبعاد (سم)') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="label in labels"
          :key="typeof label === 'string' ? label : label.name"
          class="m-row"
        >
          <td class="m-label-cell">
            <div class="label-with-icon">
              <svg
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="3"
                class="m-icon"
              >
                <path
                  d="m7.5 15.5 2 2M11 12l2 2M14.5 8.5l2 2M17.61 2.81a3 3 0 1 1 4.24 4.24l-14.85 14.85a3 3 0 0 1-2.12.88L2 23l.12-2.88a3 3 0 0 1 .88-2.12L17.61 2.81Z"
                />
              </svg>
              <span class="m-label-text">{{ typeof label === 'string' ? label : label.name }}</span>
            </div>
          </td>
          <td class="m-input-cell">
            <div class="m-input-group">
              <input
                type="number"
                step="0.1"
                :value="
                  modelValue[typeof label === 'string' ? label : label.name] ??
                  (typeof label === 'object' ? label.defaultValue : 0)
                "
                class="m-input"
                @input="
                  updateValue(typeof label === 'string' ? label : label.name, $event.target.value)
                "
              />
              <span class="unit-box">{{
                typeof label === 'object' ? label.unit : t('cm', 'سم')
              }}</span>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
  const props = defineProps(['labels', 'modelValue', 'isRtl']);
  const emit = defineEmits(['update:modelValue']);

  const t = (en, ar) => (props.isRtl ? ar : en);

  const updateValue = (label, value) => {
    const newValue = { ...props.modelValue };
    newValue[label] = parseFloat(value) || 0;
    emit('update:modelValue', newValue);
  };
</script>

<style scoped>
  .measurement-table-container {
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    overflow: hidden;
    background: #fafbfc;
  }
  .m-table {
    width: 100%;
    border-collapse: collapse;
  }
  .m-table th {
    text-align: left;
    padding: 0.875rem 1.25rem;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    color: #94a3b8;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 800;
  }
  .width-fixed {
    width: 180px;
    text-align: right !important;
  }

  .m-row {
    transition: background 0.2s;
    background: white;
  }
  .m-row:hover {
    background: #fcfdfe;
  }

  .m-label-cell {
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid #f8fafc;
  }
  .label-with-icon {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .m-icon {
    color: #cbd5e1;
  }
  .m-label-text {
    font-weight: 700;
    color: #1a3a5f;
    font-size: 0.9375rem;
  }

  .m-input-cell {
    padding: 0.625rem 1.25rem;
    border-bottom: 1px solid #f8fafc;
  }
  .m-input-group {
    display: flex;
    align-items: center;
    background: #fafbfc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    height: 38px;
  }

  .m-input {
    width: 100%;
    border: none;
    background: transparent;
    padding: 0 0.75rem;
    text-align: right;
    font-weight: 800;
    color: #1a3a5f;
    font-size: 0.9375rem;
  }
  .m-input:focus {
    outline: none;
  }

  .unit-box {
    background: #f1f5f9;
    padding: 0 0.75rem;
    height: 100%;
    display: flex;
    align-items: center;
    font-size: 0.7rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    border-left: 1px solid #e2e8f0;
  }

  .rtl .m-table th {
    text-align: right;
  }
  .rtl .width-fixed {
    text-align: left !important;
  }
  .rtl .m-input {
    text-align: left;
  }
  .rtl .unit-box {
    border-left: none;
    border-right: 1px solid #e2e8f0;
  }
</style>
