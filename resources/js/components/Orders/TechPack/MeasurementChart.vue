<template>
  <div class="mb-4 overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
    <div class="border-b border-neutral-100 bg-neutral-50/50 px-5 py-4">
      <h3 class="m-0 text-[12px] font-bold tracking-widest text-neutral-900 uppercase">
        Measurement Specifications (CM)
      </h3>
    </div>

    <div class="w-full overflow-x-auto">
      <table class="w-full border-collapse text-left whitespace-nowrap">
        <thead>
          <tr>
            <th
              class="border-b border-neutral-200 bg-neutral-50 px-4 py-3 text-[11px] font-semibold tracking-wider text-neutral-500 uppercase"
            >
              Point of Measure
            </th>
            <th
              v-for="size in sizes"
              :key="size.name"
              class="border-b border-neutral-200 bg-neutral-50 px-4 py-3 text-center text-[11px] font-semibold tracking-wider text-neutral-500 uppercase"
            >
              {{ size.name }}
            </th>
            <th
              class="w-20 border-b border-neutral-200 bg-neutral-50 px-4 py-3 text-center text-[11px] font-semibold tracking-wider text-neutral-500 uppercase"
            >
              Spec
            </th>
            <th
              class="w-16 border-b border-neutral-200 bg-neutral-50 px-4 py-3 text-center text-[11px] font-semibold tracking-wider text-neutral-500 uppercase"
            >
              Tol (+/-)
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-neutral-100">
          <tr
            v-for="(point, idx) in points"
            :key="idx"
            class="transition-colors hover:bg-neutral-50 hover:shadow-xs"
          >
            <td class="px-4 py-3 text-[13px] font-semibold text-neutral-900">
              {{ point.point || point.point_of_measure }}
            </td>
            <td
              v-for="size in sizes"
              :key="size.name"
              class="px-4 py-3 text-center text-[13px] text-neutral-600"
            >
              {{
                point.grading &&
                point.grading[size.name] !== undefined &&
                point.grading[size.name] !== ''
                  ? point.grading[size.name]
                  : '---'
              }}
            </td>
            <td
              class="border-r border-l border-blue-100/50 bg-blue-50/50 px-4 py-3 text-center text-[13px] font-bold text-blue-700"
            >
              {{
                point.value !== undefined && point.value !== ''
                  ? point.value
                  : point.dimension_value !== undefined && point.dimension_value !== ''
                    ? point.dimension_value
                    : '---'
              }}
            </td>
            <td class="px-4 py-3 text-center text-[13px] font-medium text-neutral-400">
              {{ point.tol || point.tolerance || '0.5' }}
            </td>
          </tr>
          <tr v-if="!points?.length">
            <td
              :colspan="sizes.length + 3"
              class="px-6 py-12 text-center text-[13px] text-neutral-400 italic"
            >
              No measurement data defined.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    sizes: {
      type: Array,
      default: () => [],
    },
    points: {
      type: Array,
      default: () => [],
    },
  });
</script>
