<template>
  <div v-if="order" class="tech-pack-print">
    <!-- PAGE 1: PRIMARY SPECS & VISUALS -->
    <div class="a4-page">
      <TechPackHeader
        v-if="can('orders.view')"
        :order-code="order.order_code"
        :version="order.version_number || '1.0'"
        :date="formatDate(new Date())"
      />

      <div class="page-content">
        <ProductSpecifications
          v-if="can('orders.view')"
          :image="order.product_image"
          :style-name="order.title"
          :client-name="order.client?.brand_name || order.client_name || '---'"
          :category="order.category?.name || order.category_name || '---'"
          :season="order.season + ' ' + (order.year || '')"
          :target-market="order.target_market || 'Global'"
          :total-quantity="totalQuantity"
          :status="order.status"
        />

        <DesignGrid v-if="can('designs.view') || can('orders.view')" :views="designViews" />

        <div class="row-flex mt-20">
          <div class="col-60">
            <FabricSpecs
              v-if="can('fabrics.view')"
              :type="order.fabric?.type || order.fabric_code"
              :weight="order.fabric?.gsm || order.fabric_weight"
              :composition="order.fabric?.composition"
              :supplier="order.fabric?.supplier"
              :width="order.fabric?.width"
              :finish="order.fabric?.finish"
            />
          </div>
          <div class="col-40">
            <ColorPalette v-if="can('orders.view')" :colors="order.colors || []" />
          </div>
        </div>
      </div>

      <div class="page-footer">Page 1 of 3 - Manufacturing Tech Pack</div>
    </div>

    <!-- PAGE 2: CONSTRUCTION & ANATOMY -->
    <div class="a4-page">
      <TechPackHeader
        v-if="can('orders.view')"
        :order-code="order.order_code"
        :version="order.version_number || '1.0'"
        :date="formatDate(new Date())"
      />

      <div class="page-content">
        <ModelAnatomyTable
          v-if="can('orders.view')"
          :items="order.anatomy || order.anatomies || []"
        />

        <div class="row-flex mt-20">
          <div class="col-50">
            <ConstructionSpecs
              v-if="can('orders.view')"
              :stitch-type="order.construction?.stitch_type"
              :seam-type="order.construction?.seam_type"
              :spi="'10-12'"
              :reinforcement="order.construction?.reinforcement"
            />
            <TrimsAccessories
              v-if="can('orders.view')"
              :zipper="order.trims?.zipper"
              :buttons="order.trims?.button"
              :ribbing="order.trims?.rib"
            />
          </div>
          <div class="col-50">
            <SewingSequence v-if="can('orders.view')" :steps="order.sewing_sequence || []" />
          </div>
        </div>
      </div>

      <div class="page-footer">Page 2 of 3 - Construction Details</div>
    </div>

    <!-- PAGE 3: MEASUREMENTS & LOGISTICS -->
    <div class="a4-page">
      <TechPackHeader
        v-if="can('orders.view')"
        :order-code="order.order_code"
        :version="order.version_number || '1.0'"
        :date="formatDate(new Date())"
      />

      <div class="page-content">
        <MeasurementChart
          v-if="can('measurements.view')"
          :sizes="measurementBreakdown"
          :points="measurementPoints"
        />

        <div class="row-flex mt-20">
          <div class="col-50">
            <MarkerConsumption
              v-if="can('orders.view')"
              :efficiency="activeMarker?.efficiency"
              :consumption="activeMarker?.total_consumption"
              :width="activeMarker?.fabric_width"
              :length="activeMarker?.marker_length"
            />
            <BOMTable v-if="can('orders.view')" :items="bomData" />
          </div>
          <div class="col-50">
            <LabelingPlacement
              v-if="can('orders.view')"
              :main-label="order.packaging?.main_label"
              :size-label="order.packaging?.size_label"
              :care-label="order.packaging?.care_label"
              :placement="order.packaging?.placement"
            />
            <PackagingShipping
              v-if="can('orders.view')"
              :pack-type="order.packaging?.packaging_type"
              :folding-method="order.packaging?.folding_method"
              :carton-qty="'Assorted'"
              :barcode-required="order.packaging?.barcode_required"
            />
          </div>
        </div>

        <FactoryNotes v-if="can('orders.view')" :notes="order.notes" />
      </div>

      <div class="page-footer">Page 3 of 3 - Quality & Logistics</div>
    </div>
  </div>
</template>

<script setup>
  import { computed, ref } from 'vue';
  import { getUser, hasPermission, ROLES } from '../../utils/permissions';

  const user = ref(getUser());
  const isSuperAdmin = computed(() => user.value?.role === ROLES.SUPER_ADMIN);
  const can = (permission) => isSuperAdmin.value || hasPermission(user.value, permission);

  // Import Subcomponents
  import TechPackHeader from './TechPack/TechPackHeader.vue';
  import ProductSpecifications from './TechPack/ProductSpecifications.vue';
  import DesignGrid from './TechPack/DesignGrid.vue';
  import ModelAnatomyTable from './TechPack/ModelAnatomyTable.vue';
  import ColorPalette from './TechPack/ColorPalette.vue';
  import BOMTable from './TechPack/BOMTable.vue';
  import FabricSpecs from './TechPack/FabricSpecs.vue';
  import MarkerConsumption from './TechPack/MarkerConsumption.vue';
  import MeasurementChart from './TechPack/MeasurementChart.vue';
  import ConstructionSpecs from './TechPack/ConstructionSpecs.vue';
  import TrimsAccessories from './TechPack/TrimsAccessories.vue';
  import SewingSequence from './TechPack/SewingSequence.vue';
  import LabelingPlacement from './TechPack/LabelingPlacement.vue';
  import PackagingShipping from './TechPack/PackagingShipping.vue';
  import FactoryNotes from './TechPack/FactoryNotes.vue';

  const props = defineProps({
    orderData: { type: Object, required: true },
  });

  // Resolve data structure
  const order = computed(() => {
    if (props.orderData.order) return { ...props.orderData.order, ...props.orderData };
    return props.orderData;
  });

  const formatDate = (date) => {
    return new Intl.DateTimeFormat('en-GB', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    }).format(date);
  };

  const totalQuantity = computed(() => {
    const breakdown =
      order.value?.measurements?.breakdown ||
      order.value?.production_details?.measurements?.breakdown;
    if (!breakdown) return 0;
    return breakdown.reduce((acc, s) => acc + (s.quantity || 0), 0);
  });

  const measurementBreakdown = computed(
    () =>
      order.value?.measurements?.breakdown ||
      order.value?.production_details?.measurements?.breakdown ||
      []
  );
  const measurementPoints = computed(
    () =>
      order.value?.measurements?.points ||
      order.value?.production_details?.measurements?.points ||
      []
  );
  const activeMarker = computed(
    () => order.value?.marker_plan || order.value?.marker_plans?.[0] || null
  );

  const designViews = computed(() => [
    { label: 'FRONT VIEW', src: order.value?.design_front_image },
    { label: 'BACK VIEW', src: order.value?.design_back_image },
    { label: 'TECHNICAL SKETCH', src: order.value?.technical_sketch },
    { label: 'DETAIL ZOOM', src: order.value?.detail_zoom },
  ]);

  const bomData = computed(() => {
    const o = order.value;
    const fab = o.fabric || {};
    const trm = o.trims || {};

    return [
      {
        name: 'Main Fabric',
        desc: fab.type || 'Standard',
        color: o.colors?.[0]?.color_name || 'Specified',
        unit: 'MTR',
      },
      {
        name: 'Construction Thread',
        desc: trm.thread || 'Polyester 120',
        color: 'DTM',
        unit: 'CONE',
      },
      { name: 'Main Zipper', desc: trm.zipper || 'N/A', color: 'Matching', unit: 'PCS' },
      { name: 'Buttons', desc: trm.button || 'N/A', color: 'Matching', unit: 'GROS' },
    ].filter((i) => i.desc !== 'N/A');
  });
</script>

<style scoped>
  /* A4 Page Layout */
  .tech-pack-print {
    background: #525659;
    padding: 40px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 40px;
    font-family: 'Inter', sans-serif;
    color: #000;
  }

  .a4-page {
    width: 210mm;
    min-height: 297mm;
    padding: 15mm;
    background: white;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    position: relative;
  }

  .page-content {
    flex-grow: 1;
  }

  /* Page Footer */
  .page-footer {
    margin-top: 20px;
    padding-top: 10px;
    border-top: 1px solid #eee;
    font-size: 8px;
    font-weight: 700;
    text-align: right;
    color: #999;
    text-transform: uppercase;
  }

  /* Utility Flex Grid */
  .row-flex {
    display: flex;
    gap: 20px;
  }
  .col-40 {
    width: 40%;
  }
  .col-50 {
    width: 50%;
  }
  .col-60 {
    width: 60%;
  }

  .mt-20 {
    margin-top: 20px;
  }

  /* Print Styles */
  @media print {
    .tech-pack-print {
      background: none;
      padding: 0;
      gap: 0;
    }

    .a4-page {
      box-shadow: none;
      margin: 0;
      page-break-after: always;
      width: 210mm;
      height: 296mm; /* Slight adjustment for chrome printing */
    }

    /* Prevent elements from being cut in half */
    .tp-section {
      page-break-inside: avoid;
    }
  }
</style>
