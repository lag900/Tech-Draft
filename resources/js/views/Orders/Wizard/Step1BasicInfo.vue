<template>
  <div class="animate-fade-in space-y-8 pb-24">
    <!-- Header -->
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
      <div>
        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
          Basic Information
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Define the core identity and specifications of your product.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <!-- Auto-generated Date & Creator -->
        <div class="hidden text-right sm:block">
          <p class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
            Created By
          </p>
          <div class="mt-1 flex items-center gap-2">
            <div
              class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300"
            >
              {{ creatorInitials }}
            </div>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{
              creatorName
            }}</span>
          </div>
        </div>
        <div class="hidden h-8 w-px bg-gray-200 sm:block dark:bg-gray-700"></div>
        <div class="hidden text-right sm:block">
          <p class="text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
            Date
          </p>
          <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ currentDate }}</p>
        </div>
      </div>
    </div>

    <!-- Customer Linking -->
    <div
      class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
    >
      <div class="p-6 md:p-8">
        <div class="mb-6 flex items-center gap-3">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 shadow-inner dark:bg-blue-900/30 dark:text-blue-400"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Customer Profile</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              Select or auto-link the associated customer/brand.
            </p>
          </div>
        </div>

        <div class="relative">
          <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300"
            >Customer / Brand Name</label
          >
          <div class="relative">
            <input
              v-model="form.customer_query"
              type="text"
              class="block w-full rounded-xl border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
              placeholder="Type to search or auto-link..."
              @input="searchCustomers"
              @focus="showCustomerDropdown = true"
            />
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
              <svg
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </div>
          </div>
          <!-- Customer Dropdown -->
          <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <div
              v-if="showCustomerDropdown && filteredCustomers.length > 0"
              class="absolute z-10 mt-2 max-h-60 w-full overflow-auto rounded-xl border border-gray-100 bg-white py-2 shadow-xl dark:border-gray-700 dark:bg-gray-800"
            >
              <button
                v-for="c in filteredCustomers"
                :key="c.id"
                type="button"
                class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50"
                @click="selectCustomer(c)"
              >
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-400"
                >
                  {{ c.name.charAt(0) }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ c.name }}</p>
                  <p class="text-xs text-gray-500">{{ c.company || 'Individual' }}</p>
                </div>
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </div>

    <!-- Product Classification Map -->
    <div
      class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
    >
      <div class="p-6 md:p-8">
        <div class="mb-8 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 shadow-inner dark:bg-purple-900/30 dark:text-purple-400"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                Classification Hierarchy
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Category &gt; Product Type &gt; Cut / Style
              </p>
            </div>
          </div>
        </div>

        <div class="relative grid grid-cols-1 gap-6 md:grid-cols-3">
          <!-- Flow Arrows (Desktop) -->
          <div
            class="absolute top-[28px] left-[33%] z-0 hidden h-0.5 w-[33%] bg-gray-200 md:block dark:bg-gray-700"
          ></div>
          <div
            class="absolute top-[28px] left-[66%] z-0 hidden h-0.5 w-[33%] bg-gray-200 md:block dark:bg-gray-700"
          ></div>

          <!-- 1. Category -->
          <div class="relative z-10">
            <label
              class="mb-2 block flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300"
            >
              <span
                class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-100 text-xs text-purple-700 dark:bg-purple-900 dark:text-purple-300"
                >1</span
              >
              Parent Category
            </label>
            <select
              v-model="form.category"
              class="block w-full appearance-none rounded-xl border-gray-200 px-4 py-3 font-medium shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
            >
              <option value="" disabled>Select Category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
          </div>

          <!-- 2. Product Type -->
          <div
            class="relative z-10 transition-opacity duration-300"
            :class="{ 'pointer-events-none opacity-50': !form.category }"
          >
            <label
              class="mb-2 block flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300"
            >
              <span
                class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-100 text-xs text-purple-700 dark:bg-purple-900 dark:text-purple-300"
                >2</span
              >
              Product Type
              <span
                v-if="smartSuggestionsActive"
                class="ml-auto animate-pulse rounded-full bg-green-100 px-2 py-0.5 text-[10px] text-green-700 dark:bg-green-900/50 dark:text-green-400"
                >Smart Adapting</span
              >
            </label>
            <div class="relative">
              <input
                v-model="form.product_type_target"
                type="text"
                class="block w-full appearance-none rounded-xl border-gray-200 px-4 py-3 font-medium shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
                placeholder="Select or type override..."
                @focus="showTypeDropdown = true"
                @input="showTypeDropdown = true"
              />
              <!-- Dropdown for Type -->
              <div
                v-if="showTypeDropdown && form.category"
                class="absolute z-20 mt-2 max-h-48 w-full overflow-auto rounded-xl border border-gray-100 bg-white py-2 shadow-xl dark:border-gray-700 dark:bg-gray-800"
              >
                <button
                  v-for="pt in suggestedProductTypes"
                  :key="pt.id"
                  type="button"
                  class="w-full px-4 py-2 text-left text-sm font-medium transition-colors hover:bg-gray-50 dark:hover:bg-gray-700"
                  @click="selectProductType(pt)"
                >
                  {{ pt.name }}
                </button>
                <div
                  v-if="suggestedProductTypes.length === 0"
                  class="px-4 py-2 text-sm text-gray-500"
                >
                  Manual override active
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Cut / Style -->
          <div
            class="relative z-10 transition-opacity duration-300"
            :class="{ 'pointer-events-none opacity-50': !form.product_type_target }"
          >
            <label
              class="mb-2 block flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300"
            >
              <span
                class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-100 text-xs text-purple-700 dark:bg-purple-900 dark:text-purple-300"
                >3</span
              >
              Cut / Style
            </label>
            <select
              v-model="form.cut_style"
              class="block w-full appearance-none rounded-xl border-gray-200 px-4 py-3 font-medium shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
            >
              <option value="" disabled>Select Cut/Style</option>
              <option v-for="cut in availableCuts" :key="cut.id" :value="cut.id">
                {{ cut.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Season & Year Section -->
    <div
      class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
    >
      <div class="p-6 md:p-8">
        <div class="flex flex-col gap-8 lg:flex-row">
          <!-- Seasons -->
          <div class="flex-1">
            <h3
              class="mb-4 text-sm font-semibold tracking-wider text-gray-900 uppercase dark:text-white"
            >
              Target Seasons
            </h3>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
              <label
                v-for="season in availableSeasons"
                :key="season.value"
                class="group relative cursor-pointer"
              >
                <input
                  v-model="form.seasons"
                  type="checkbox"
                  :value="season.value"
                  class="peer sr-only"
                />
                <div
                  class="rounded-xl border-2 border-gray-100 bg-gray-50 px-4 py-3 text-center transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:shadow-sm hover:bg-white dark:border-gray-700 dark:bg-gray-900/50 dark:peer-checked:bg-orange-900/20 dark:hover:bg-gray-800"
                >
                  <div class="mb-1 text-xl transition-transform group-hover:scale-110">
                    {{ season.icon }}
                  </div>
                  <span
                    class="text-sm font-bold text-gray-600 peer-checked:text-orange-700 dark:text-gray-400 dark:peer-checked:text-orange-400"
                    >{{ season.label }}</span
                  >
                </div>
              </label>
            </div>
          </div>

          <!-- Years -->
          <div class="lg:w-1/3">
            <h3
              class="mb-4 text-sm font-semibold tracking-wider text-gray-900 uppercase dark:text-white"
            >
              Target Year
            </h3>
            <select
              v-model="form.year"
              class="block w-full appearance-none rounded-xl border-gray-200 px-4 py-3 text-center font-bold text-gray-800 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
            >
              <option v-for="y in yearOptions" :key="y" :value="y">
                {{ y }} {{ y === currentYear ? '(Current)' : '' }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Fabric Composition & Details -->
    <div
      class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
    >
      <div class="p-6 md:p-8">
        <div class="mb-6 flex items-center gap-3">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-600 shadow-inner dark:bg-teal-900/30 dark:text-teal-400"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"
              />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Fabric Composition</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              Specify material details and physical properties.
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
          <div class="lg:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300"
              >Primary Fabric Type
              <span class="text-xs font-normal text-gray-400">(Admin Controlled)</span></label
            >
            <select
              v-model="form.fabric_type"
              class="block w-full appearance-none rounded-xl border-gray-200 px-4 py-3 font-medium shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
            >
              <option value="" disabled>Select Fabric Type</option>
              <option v-for="fab in adminFabrics" :key="fab.id" :value="fab.id">
                {{ fab.name }} ({{ fab.composition }})
              </option>
            </select>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300"
              >Weight (GSM)</label
            >
            <div class="relative">
              <input
                v-model="form.fabric_weight"
                type="number"
                class="block w-full rounded-xl border-gray-200 px-4 py-3 pr-12 font-medium shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
                placeholder="Ex: 320"
              />
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                <span class="text-sm font-bold text-gray-400">gsm</span>
              </div>
            </div>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300"
              >Texture <span class="text-xs font-normal text-gray-400">(Optional)</span></label
            >
            <input
              v-model="form.fabric_texture"
              type="text"
              class="block w-full rounded-xl border-gray-200 px-4 py-3 font-medium shadow-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
              placeholder="Ex: Brushed, Ribbed..."
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Advanced Color Picker -->
    <div
      class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
    >
      <div class="p-6 md:p-8">
        <div class="mb-6 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-50 text-pink-600 shadow-inner dark:bg-pink-900/30 dark:text-pink-400"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Color Ways</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Define single or multiple colors with HEX/RGB precision.
              </p>
            </div>
          </div>
          <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl bg-pink-50 px-4 py-2 text-sm font-bold text-pink-700 transition-colors hover:bg-pink-100 dark:bg-pink-900/40 dark:text-pink-300 dark:hover:bg-pink-900/60"
            @click="addColor"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
            Add Color
          </button>
        </div>

        <div class="space-y-4">
          <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <div
              v-for="(color, index) in form.colors"
              :key="color.id"
              class="group flex flex-col items-center gap-4 rounded-xl border border-gray-100 bg-gray-50/50 p-4 transition-all hover:shadow-sm sm:flex-row dark:border-gray-700 dark:bg-gray-800/50"
            >
              <!-- Visual Color Box with Native Color Picker Overlay -->
              <div
                class="relative h-16 w-16 shrink-0 overflow-hidden rounded-xl shadow-inner ring-1 ring-black/10 ring-inset dark:ring-white/10"
                :style="{ backgroundColor: color.hex }"
              >
                <input
                  v-model="color.hex"
                  type="color"
                  class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                />
              </div>

              <!-- Manual Inputs -->
              <div class="grid w-full flex-1 grid-cols-1 gap-3 sm:grid-cols-3">
                <div>
                  <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400"
                    >Color Name</label
                  >
                  <input
                    v-model="color.name"
                    type="text"
                    class="block w-full rounded-lg border-gray-200 px-3 py-2 font-medium shadow-sm focus:ring-2 focus:ring-pink-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
                    placeholder="Ex: Midnight Blue"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400"
                    >HEX Code</label
                  >
                  <input
                    v-model="color.hex"
                    type="text"
                    class="block w-full rounded-lg border-gray-200 px-3 py-2 font-mono uppercase shadow-sm focus:ring-2 focus:ring-pink-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
                    placeholder="#000000"
                    @input="updateRGB(color)"
                  />
                </div>
                <div>
                  <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400"
                    >Pantone / Ref (Opt)</label
                  >
                  <input
                    v-model="color.pantone"
                    type="text"
                    class="block w-full rounded-lg border-gray-200 px-3 py-2 font-medium shadow-sm focus:ring-2 focus:ring-pink-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
                    placeholder="Ex: 19-4052"
                  />
                </div>
              </div>

              <div class="flex shrink-0 flex-col items-center justify-center">
                <span
                  class="mb-2 rounded bg-gray-100 px-2 py-1 font-mono text-[10px] font-bold text-gray-400 shadow-inner dark:bg-gray-800"
                  >{{ hexToRgb(color.hex) }}</span
                >
                <button
                  v-if="form.colors.length > 1"
                  type="button"
                  class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/30"
                  @click="removeColor(index)"
                >
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </TransitionGroup>
        </div>
      </div>
    </div>

    <!-- Dynamic Measurement Table -->
    <div
      class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
    >
      <div class="p-6 md:p-8">
        <div class="mb-6 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div
              class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-600 shadow-inner dark:bg-orange-900/30 dark:text-orange-400"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"
                />
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Measurements Matrix</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                Dimensions (in cm) automatically linked to classification.
              </p>
            </div>
          </div>

          <div
            v-if="suggestedMeasurements"
            class="flex items-center gap-2 rounded-xl border border-green-100 bg-green-50 px-4 py-2 text-sm font-bold text-green-700 dark:border-green-800/30 dark:bg-green-900/20 dark:text-green-400"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 10V3L4 14h7v7l9-11h-7z"
              />
            </svg>
            Smart Auto-filled
          </div>
        </div>

        <div
          v-if="!form.product_type_target"
          class="rounded-xl border border-dashed border-gray-300 bg-gray-50 py-8 text-center dark:border-gray-600 dark:bg-gray-800/50"
        >
          <p class="font-medium text-gray-500 dark:text-gray-400">
            Please select a Product Type &amp; Cut above to load measurement labels.
          </p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-gray-50 text-xs font-bold tracking-wider text-gray-600 uppercase dark:bg-gray-900/50 dark:text-gray-400"
            >
              <tr>
                <th class="rounded-tl-xl px-4 py-4">Point of Measurement (POM)</th>
                <th class="px-4 py-4 text-center">Tolerance (±)</th>
                <th v-for="size in sizeRange" :key="size" class="px-4 py-4 text-center">
                  {{ size }}
                </th>
              </tr>
            </thead>
            <tbody
              class="divide-y divide-gray-100 border-t border-gray-100 dark:divide-gray-800 dark:border-gray-800"
            >
              <tr
                v-for="label in currentMeasurementLabels"
                :key="label.id"
                class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-700/20"
              >
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-200">
                  {{ label.name }}
                </td>
                <td class="w-24 px-4 py-3">
                  <input
                    v-model="form.measurements[label.id]['tolerance']"
                    type="number"
                    step="0.5"
                    class="w-full rounded-lg border-gray-200 px-1 py-2 text-center shadow-sm focus:ring-2 focus:ring-orange-500 sm:text-xs dark:border-gray-600 dark:bg-gray-900"
                  />
                </td>
                <td v-for="size in sizeRange" :key="size" class="w-28 px-4 py-3">
                  <input
                    v-model="form.measurements[label.id][size]"
                    type="number"
                    step="0.5"
                    class="w-full rounded-lg border-gray-200 px-1 py-2 text-center font-mono font-bold shadow-sm focus:ring-2 focus:ring-orange-500 sm:text-sm dark:border-gray-600 dark:bg-gray-900"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, reactive, computed, watch, onMounted } from 'vue';

  // System Data
  const currentUser = ref({ name: 'Admin User', initials: 'AU' }); // Replace with actual user context
  const creatorName = computed(() => currentUser.value.name);
  const creatorInitials = computed(() => currentUser.value.initials);
  const today = new Date();
  const currentDate = computed(() =>
    today.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
  );
  const currentYear = today.getFullYear();

  // Mock Data APIs
  const availableCustomers = ref([
    { id: 1, name: 'Zara Intl', company: 'Inditex' },
    { id: 2, name: 'H&M Studio', company: 'H&M Group' },
    { id: 3, name: 'Local Boutique', company: 'Independent' },
  ]);
  const categories = ref([
    { id: 1, name: 'Menswear' },
    { id: 2, name: 'Womenswear' },
    { id: 3, name: 'Kidswear' },
  ]);
  const allProductTypes = ref([
    { id: 101, category_id: 1, name: 'Casual Pants' },
    { id: 102, category_id: 1, name: 'T-Shirts' },
    { id: 201, category_id: 2, name: 'Dresses' },
    { id: 202, category_id: 2, name: 'Skirts' },
  ]);
  const allCuts = ref([
    { id: 1, type_id: 101, name: 'Slim Fit' },
    { id: 2, type_id: 101, name: 'Relaxed/Wide' },
    { id: 3, type_id: 201, name: 'A-Line' },
    { id: 4, type_id: 201, name: 'Bodycon' },
  ]);
  const availableSeasons = ref([
    { value: 'spring', label: 'Spring', icon: '🌸' },
    { value: 'summer', label: 'Summer', icon: '☀️' },
    { value: 'autumn', label: 'Autumn', icon: '🍂' },
    { value: 'winter', label: 'Winter', icon: '❄️' },
  ]);
  const yearOptions = ref([currentYear, currentYear + 1, currentYear + 2]);

  const adminFabrics = ref([
    { id: 1, name: 'Single Jersey', composition: '100% Cotton' },
    { id: 2, name: 'French Terry', composition: '80% Cotton 20% Poly' },
    { id: 3, name: 'Denim Heavyweight', composition: '98% Cotton 2% Elastane' },
  ]);

  const sizeRange = ref(['S', 'M', 'L', 'XL']);
  const baseMeasurementLabels = ref([
    { id: '1', type_id: 101, name: 'Waist Width' },
    { id: '2', type_id: 101, name: 'Inseam Length' },
    { id: '3', type_id: 101, name: 'Leg Opening' },
    { id: '4', type_id: 201, name: 'Chest/Bust' },
    { id: '5', type_id: 201, name: 'Total Length' },
  ]);

  // Main Form State
  const form = reactive({
    customer_id: null,
    customer_query: '',
    category: '',
    product_type_target: '',
    cut_style: '',
    seasons: [],
    year: currentYear,
    fabric_type: '',
    fabric_weight: '',
    fabric_texture: '',
    colors: [{ id: Date.now(), hex: '#000000', name: 'Black', pantone: '' }],
    measurements: {},
  });

  // UI State
  const showCustomerDropdown = ref(false);
  const showTypeDropdown = ref(false);
  const smartSuggestionsActive = ref(false);
  const suggestedMeasurements = ref(false);

  // Computed & Watchers
  const filteredCustomers = computed(() => {
    if (!form.customer_query) return availableCustomers.value;
    const q = form.customer_query.toLowerCase();
    return availableCustomers.value.filter(
      (c) => c.name.toLowerCase().includes(q) || c.company.toLowerCase().includes(q)
    );
  });

  const suggestedProductTypes = computed(() => {
    if (!form.category) return [];
    return allProductTypes.value.filter((pt) => pt.category_id === form.category);
  });

  const availableCuts = computed(() => {
    if (!form.product_type_target) return [];
    // Find mapped type ID based on name or ID if selected from suggestions
    const pt = allProductTypes.value.find((p) => p.name === form.product_type_target);
    if (pt) return allCuts.value.filter((c) => c.type_id === pt.id);
    return []; // Manual override might mean no exact cuts available, could fallback
  });

  const currentMeasurementLabels = computed(() => {
    if (!form.product_type_target) return [];
    const pt = allProductTypes.value.find((p) => p.name === form.product_type_target);
    if (pt) return baseMeasurementLabels.value.filter((l) => l.type_id === pt.id);
    return [];
  });

  // Watch category to trigger smart product type suggestions
  watch(
    () => form.category,
    (newVal) => {
      form.product_type_target = '';
      form.cut_style = '';
      smartSuggestionsActive.value = !!newVal;
      setTimeout(() => (smartSuggestionsActive.value = false), 3000);
    }
  );

  // Watch type to seed measurement default matrix
  watch(currentMeasurementLabels, (newLabels) => {
    const newMatrix = {};
    newLabels.forEach((label) => {
      newMatrix[label.id] = { tolerance: 0.5 };
      sizeRange.value.forEach((s) => (newMatrix[label.id][s] = 0));
    });
    // Simulate smart auto-fill from past orders if found
    if (newLabels.length > 0) {
      suggestedMeasurements.value = true;
      setTimeout(() => (suggestedMeasurements.value = false), 4000);
    } else {
      suggestedMeasurements.value = false;
    }
    form.measurements = newMatrix;
  });

  // Actions
  const searchCustomers = () => {
    showCustomerDropdown.value = true;
  };
  const selectCustomer = (customer) => {
    form.customer_id = customer.id;
    form.customer_query = customer.name;
    showCustomerDropdown.value = false;
  };
  const selectProductType = (pt) => {
    form.product_type_target = pt.name;
    form.cut_style = '';
    showTypeDropdown.value = false;
  };

  // Color Handling
  const addColor = () => {
    form.colors.push({ id: Date.now(), hex: '#FFFFFF', name: '', pantone: '' });
  };
  const removeColor = (idx) => {
    form.colors.splice(idx, 1);
  };
  const hexToRgb = (hex) => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result
      ? `rgb(${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)})`
      : 'rgb(0,0,0)';
  };
  const updateRGB = (color) => {
    // Logic just rely on hexToRgb mapped directly,
    // but could ensure # is added
    if (!color.hex.startsWith('#') && color.hex.length >= 6) {
      color.hex = '#' + color.hex;
    }
  };

  onMounted(() => {
    // Click outside listener for dropdowns (simplified)
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.relative')) {
        showCustomerDropdown.value = false;
        showTypeDropdown.value = false;
      }
    });
  });
</script>

<style scoped>
  .animate-fade-in {
    animation: fadeIn 0.4s ease-out;
  }
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  /* Tailwind utilities hide scrollbar */
  ::-webkit-scrollbar {
    width: 6px;
    height: 6px;
  }
  ::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .dark ::-webkit-scrollbar-thumb {
    background: #475569;
  }
</style>
