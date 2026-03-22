<template>
  <div v-if="order" class="tech-pack-print-wrapper">
    <!-- PAGE 1: OVERVIEW & VISUALS -->
    <div class="tp-page">
      <TechPackHeader
        :order-code="order.order_code"
        :version="order.version_number || '1.0'"
        :date="formatDate(new Date())"
      />

      <ProductSpecifications
        :image="order.front_image"
        :style-name="order.title"
        :client-name="order.client_name"
        :category="order.category_name"
        :season="order.season"
        :total-quantity="order.total_quantity"
        :status="order.status"
      />

      <DesignGrid :views="designViews" />

      <div class="page-footer">Page 1 / 3</div>
    </div>

    <div class="page-break html2pdf__page-break"></div>

    <!-- PAGE 2: MATERIALS & MEASUREMENTS -->
    <div class="tp-page">
      <TechPackHeader
        :order-code="order.order_code"
        :version="order.version_number || '1.0'"
        :date="formatDate(new Date())"
      />

      <div class="tp-grid-2">
        <div class="col-stack">
          <ColorPalette :colors="order.colors || []" />
          <FabricSpecs
            :type="materials.fabric.type"
            :weight="materials.fabric.weight"
            :composition="materials.fabric.composition"
            :supplier="materials.fabric.supplier"
            :width="materials.fabric.width"
            :finish="materials.fabric.finish"
          />
        </div>
        <div class="col-stack">
          <BOMTable :items="bomData" />
          <MeasurementChart :sizes="measurementBreakdown" :points="measurementPoints" />
        </div>
      </div>

      <div class="page-footer">Page 2 / 3</div>
    </div>

    <div class="page-break html2pdf__page-break"></div>

    <!-- PAGE 3: PRODUCTION, ANATOMY & LOGISTICS -->
    <div class="tp-page">
      <TechPackHeader
        :order-code="order.order_code"
        :version="order.version_number || '1.0'"
        :date="formatDate(new Date())"
      />

      <div class="tp-grid-2">
        <div class="col-stack">
          <ConstructionSpecs
            :stitch-type="construction.stitch_type"
            :seam-type="construction.seam_type"
            :spi="construction.spi"
            :thread-type="construction.thread_type"
            :reinforcement="construction.reinforcement"
            :allowance="construction.seam_allowance"
          />
          <SewingSequence :steps="production.operations || []" />
        </div>
        <div class="col-stack">
          <ModelAnatomyTable :items="production.anatomy || []" />
          <MarkerConsumption
            :efficiency="production.marker.efficiency"
            :width="production.marker.width"
            :length="production.marker.length"
            consumption="---"
          />
        </div>
      </div>

      <div class="tp-grid-2 mt-10">
        <div class="col-stack">
          <TrimsAccessories
            :zipper="materials.trims.zipper"
            :buttons="materials.trims.button"
            :cord="materials.trims.cord"
            :ribbing="materials.trims.rib"
            :thread="materials.trims.thread"
          />
          <PackagingShipping
            :pack-type="packaging.packaging_type"
            :folding-method="packaging.folding_method"
            :carton-qty="packaging.carton_qty"
            :barcode-required="packaging.barcode_required"
          />
        </div>
        <div class="col-stack">
          <LabelingPlacement
            :main-label="packaging.labels.main"
            :size-label="packaging.labels.size"
            :care-label="packaging.labels.care"
            :placement="packaging.labels.position"
          />
          <FactoryNotes :notes="packaging.notes" />
        </div>
      </div>

      <div class="page-footer">Page 3 / 3</div>
    </div>
  </div>
</template>

<script setup>
  import { computed } from 'vue';

  // Sub-components
  import TechPackHeader from './TechPack/TechPackHeader.vue';
  import ProductSpecifications from './TechPack/ProductSpecifications.vue';
  import DesignGrid from './TechPack/DesignGrid.vue';
  import ColorPalette from './TechPack/ColorPalette.vue';
  import FabricSpecs from './TechPack/FabricSpecs.vue';
  import BOMTable from './TechPack/BOMTable.vue';
  import MeasurementChart from './TechPack/MeasurementChart.vue';
  import ConstructionSpecs from './TechPack/ConstructionSpecs.vue';
  import SewingSequence from './TechPack/SewingSequence.vue';
  import ModelAnatomyTable from './TechPack/ModelAnatomyTable.vue';
  import MarkerConsumption from './TechPack/MarkerConsumption.vue';
  import TrimsAccessories from './TechPack/TrimsAccessories.vue';
  import LabelingPlacement from './TechPack/LabelingPlacement.vue';
  import PackagingShipping from './TechPack/PackagingShipping.vue';
  import FactoryNotes from './TechPack/FactoryNotes.vue';

  const props = defineProps({
    orderData: { type: Object, required: true },
  });

  const unified = computed(() => props.orderData || {});
  const order = computed(() => unified.value.product || {});
  const materials = computed(() => unified.value.materials || { fabric: {}, trims: {}, bom: [] });
  const measurements = computed(() => unified.value.measurements || { sizes: [], points: [] });
  const construction = computed(() => unified.value.construction || {});
  const production = computed(
    () => unified.value.production || { operations: [], anatomy: [], marker: {} }
  );
  const packaging = computed(() => unified.value.packaging || { labels: {} });

  const formatDate = (date) => {
    return new Intl.DateTimeFormat('en-GB', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    }).format(date);
  };

  const totalQuantity = computed(() => order.value.total_quantity || 0);

  const measurementBreakdown = computed(() => measurements.value.sizes || []);
  const measurementPoints = computed(() => measurements.value.points || []);

  const designViews = computed(() => [
    { label: 'FRONT VIEW', src: order.value.front_image },
    { label: 'BACK VIEW', src: order.value.back_image },
    { label: 'TECHNICAL SKETCH', src: order.value.technical_sketch },
    { label: 'DETAIL ZOOM', src: order.value.detail_zoom },
  ]);

  const bomData = computed(() => materials.value.bom || []);
</script>

<style>
  /* PRINT WRAPPER AESTHETICS (Non-Scoped for child access) */
  .tech-pack-print-wrapper {
    background: #f1f5f9;
    padding: 40px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 40px;
  }

  .is-exporting.tech-pack-print-wrapper {
    background: white;
    padding: 0 !important;
    margin: 0 !important;
    gap: 0 !important;
    display: block;
  }

  .tp-page {
    width: 210mm;
    min-height: 296mm;
    background: white !important;
    padding: 15mm;
    position: relative;
    box-sizing: border-box;
    font-family:
      'Inter',
      system-ui,
      -apple-system,
      sans-serif !important;
    color: #1e293b;
    border-radius: 12px;
    box-shadow:
      0 10px 25px -5px rgba(0, 0, 0, 0.1),
      0 8px 10px -6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    page-break-inside: avoid;
  }

  .is-exporting .tp-page {
    box-shadow: none !important;
    border-radius: 0 !important;
    border: none !important;
    margin: 0 auto !important;
    width: 210mm;
    /* Removed strict 297mm height that caused canvas spillover and blank pages */
    min-height: 296mm;
    height: auto;
    max-height: none;
  }

  /* Page separators when viewing on web/print */
  .page-break {
    page-break-after: always !important;
    height: 0px;
    margin: 0;
    padding: 0;
    border: none;
  }

  /* Common Section Styling */
  .section-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 16px !important;
    margin-bottom: 20px !important;
    /* Prevent cutting inside elements */
    page-break-inside: avoid !important;
    break-inside: avoid !important;
    position: relative;
  }

  .section-card .card-title {
    font-size: 13px !important;
    font-weight: 900 !important;
    color: #2563eb !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px !important;
    border-bottom: 1.5px solid #f1f5f9 !important;
    padding-bottom: 8px !important;
  }

  /* Grid System */
  .tp-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
  }

  .col-stack {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  /* Visuals Enhancement */
  .page-footer {
    position: absolute;
    bottom: 15mm;
    right: 15mm;
    left: 15mm;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f1f5f9;
    padding-top: 10px;
    font-size: 10px !important;
    font-weight: 700;
    color: #94a3b8;
  }

  .footer-brand {
    color: #cbd5e1;
    text-transform: uppercase;
    letter-spacing: 1.5px;
  }

  /* Universal Typography for Print */
  .tp-page * {
    font-size: 11px;
    line-height: 1.4;
  }
  .tp-page b,
  .tp-page strong {
    font-weight: 800;
  }

  @media print {
    body {
      background: white;
    }
    .tech-pack-print-wrapper {
      background: white !important;
      padding: 0 !important;
      display: block;
      gap: 0 !important;
      margin: 0 !important;
    }
    .tp-page {
      margin: 0;
      padding: 15mm;
      box-shadow: none !important;
      border-radius: 0 !important;
      border: none !important;
      width: 210mm;
      min-height: 296mm;
      page-break-inside: avoid;
    }
    .no-print {
      display: none !important;
    }
  }
</style>
