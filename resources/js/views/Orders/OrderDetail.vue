<template>
  <Layout>
  <div class="full-page-wrapper">
    <div v-if="order" class="order-detail-view" :class="{ 'rtl': isRtl }">
      <!-- Header Area -->
      <div class="page-top-header">
        <div class="header-left">
          <div class="breadcrumb-mini no-print">
            <router-link to="/orders" class="back-link">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M19 12H5m7 7l-7-7 7-7"/></svg>
            </router-link>
            <span class="active-code">#{{ order.order_code }}</span>
          </div>
          <h1 class="mobile-only-title">{{ order.title || order.category?.name }}</h1>
        </div>

        <div class="actions-right no-print">
            <BaseButton v-if="order.can?.export" class="tech-pack-top-btn" @click="router.push(`/orders/${order.id}/tech-pack`)">
              <template #icon-left>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
              </template>
              {{ t('Professional Tech Pack', 'الملف الفني الاحترافي') }}
            </BaseButton>
            
            <BaseButton v-if="isAdmin && !isEditing" class="btn-gradient" @click="isEditing = true">
              <template #icon-left>
                 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
              </template>
              {{ t('Edit Order', 'تعديل الطلب') }}
            </BaseButton>
            <div v-else-if="isAdmin && isEditing" class="editing-actions">
                <BaseButton class="btn-gradient-success" @click="saveTechPackData" :loading="isSavingAdminData">
                   <template #icon-left><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg></template>
                   {{ t('Save', 'حفظ') }}
                </BaseButton>
                <BaseButton variant="white" @click="isEditing = false" :disabled="isSavingAdminData">
                   {{ t('Cancel', 'إلغاء') }}
                </BaseButton>
            </div>
<BaseButton variant="outline" @click="router.push(`/orders/create?reorder_id=${order.id}`)">
              <template #icon-left>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
              </template>
              {{ t('Reorder', 'إعادة طلب') }}
            </BaseButton>
        </div>
      </div>

      <!-- Main Dashboard Grid -->
      <div class="dashboard-grid">
        <!-- Sidebar / Overview Info -->
        <aside class="dashboard-sidebar">
          <div class="saas-card profile-card text-center">
            <div class="profile-img-wrap">
                <img :src="order.design_front_image ? '/storage/' + order.design_front_image : '/images/product-placeholder.png'" 
                     alt="product"
                     @error="(e) => e.target.src = '/images/product-placeholder.png'"
                >
            </div>
            <h2 class="auth-title mt-2">{{ order.title || order.category?.name || t('Untitled Product', 'منتج بدون اسم') }}</h2>
            <p class="text-gray-400 font-bold mb-2">#{{ order.order_code }}</p>
            <span class="status-pill-large" :class="order.status">
                {{ t(statusMap[order.status]?.en, statusMap[order.status]?.ar) }}
            </span>
            <div class="mt-4 pt-4 border-t border-dashed border-gray-200 text-left rtl-text-right">
               <div class="mb-3">
                  <span class="text-xs font-bold text-gray-400">{{ t('Client', 'العميل') }}</span>
                  <div class="font-bold text-gray-800">{{ order.client?.brand_name || order.creator?.name }}</div>
               </div>
               <div class="mb-3">
                  <span class="text-xs font-bold text-gray-400">{{ t('Quantity', 'الكمية') }}</span>
                  <div class="font-bold text-gray-800">{{ order.production_details?.quantity || '---' }}</div>
               </div>
               <div class="mb-3">
                  <span class="text-xs font-bold text-gray-400">{{ t('Season', 'الموسم') }}</span>
                  <div class="font-bold text-gray-800">{{ t(order.season, seasonsAr[order.season]) }} {{ order.year }}</div>
               </div>
            </div>
          </div>
          
          <OrderActions 
            :order="order" 
            :updating="updating" 
            :statusTarget="statusTarget" 
            :isAdmin="isAdmin"
            @updateStatus="updateOrderStatus"
            @scrollToAdmin="scrollToAdminSection"
            @viewTechPack="showTechPack = true"
          />
        </aside>

        <!-- Main Content Area -->
        <div class="dashboard-main">
           <!-- Modern Tabs -->
           <div class="saas-tabs-nav mb-4">
              <button :class="{ active: activeTab === 'overview' }" @click="activeTab = 'overview'">{{ t('Overview', 'نظرة عامة') }}</button>
              <button v-if="isAdmin" :class="{ active: activeTab === 'materials' }" @click="activeTab = 'materials'">{{ t('Materials', 'المواد') }}</button>
              <button v-if="isAdmin" :class="{ active: activeTab === 'construction' }" @click="activeTab = 'construction'">{{ t('Construction', 'التصنيع') }}</button>
              <button v-if="isAdmin" :class="{ active: activeTab === 'production' }" @click="activeTab = 'production'">{{ t('Production', 'الإنتاج') }}</button>
              <button v-if="isAdmin" :class="{ active: activeTab === 'packaging' }" @click="activeTab = 'packaging'">{{ t('Packaging', 'التغليف') }}</button>
              <button :class="{ active: activeTab === 'chat' }" @click="activeTab = 'chat'">{{ t('Chat', 'المحادثة') }}</button>
           </div>
           
           <!-- Tab 1: Overview -->
           <div v-show="activeTab === 'overview'" class="tab-pane fade-in">
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Technical Specifications', 'المواصفات الفنية') }}</h3>
                 <div class="info-grid">
                    <div class="info-item">
                        <label>{{ t('Item Category', 'تصنيف القطعة') }}</label>
                        <div class="val">{{ order.category?.name || '---' }}</div>
                    </div>
                    <div class="info-item">
                        <label>{{ t('Item Type', 'نوع القطعة') }}</label>
                        <div class="val">{{ order.production_details?.item_type || '---' }}</div>
                    </div>
                    <div class="info-item">
                        <label>{{ t('Fit / Style', 'القصة / الستايل') }}</label>
                        <div class="val">{{ order.production_details?.fit || t('Regular', 'عادي') }}</div>
                    </div>
                    <div class="info-item">
                        <label>{{ t('Sizes', 'المقاسات') }}</label>
                        <div class="val">{{ formatSizes(order.production_details?.sizes) }}</div>
                    </div>
                 </div>
              </div>
              
              <div class="saas-card mb-4" v-if="order.colors && order.colors.length">
                 <h3 class="card-title">{{ t('Color Palette', 'لوحة الألوان') }}</h3>
                 <div class="colors-flex">
                    <div v-for="(color, idx) in order.colors" :key="idx" class="color-item shadow-sm">
                      <div class="color-circle" :style="{ background: color.hex }"></div>
                      <div class="color-meta">
                         <div class="hex">{{ color.name || color.hex }}</div>
                         <div class="pantone">{{ color.pantone || color.hex }}</div>
                      </div>
                    </div>
                 </div>
              </div>
              
              <div class="saas-card mb-4" v-if="order.images && order.images.length">
                 <h3 class="card-title">{{ t('Design Visuals', 'صور التصميم') }}</h3>
                 <div class="gallery-grid">
                     <div v-for="img in order.images" :key="img.id" class="gallery-img-box" @click="zoomImage(img.file_path)">
                        <img :src="'/storage/' + img.file_path" alt="design">
                        <div class="img-overlay">
                           <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                        </div>
                     </div>
                 </div>
              </div>
           </div>
           
           <!-- Tab 2: Materials -->
           <div v-show="isAdmin && activeTab === 'materials'" class="tab-pane fade-in">
              <div class="saas-card mb-4" id="admin-production-setup">
                 <h3 class="card-title">{{ t('Fabric Specifications', 'مواصفات القماش') }}</h3>
                 <div v-if="!isEditing" class="info-grid">
                    <div class="info-item"><label>Fabric Type</label><div class="val">{{ techPack.fabric?.code || order.fabric_details?.type || '---' }}</div></div>
                    <div class="info-item"><label>Supplier</label><div class="val">{{ techPack.fabric?.supplier || order.fabric_supplier || '---' }}</div></div>
                    <div class="info-item"><label>Width</label><div class="val">{{ techPack.fabric?.width ? techPack.fabric.width + ' cm' : '---' }}</div></div>
                    <div class="info-item"><label>Dye Method</label><div class="val">{{ techPack.fabric?.dye || '---' }}</div></div>
                    <div class="info-item"><label>Finish</label><div class="val">{{ techPack.fabric?.finish || '---' }}</div></div>
                    <div class="info-item"><label>Weight</label><div class="val">{{ order.fabric_details?.weight ? order.fabric_details.weight + ' gsm' : '---' }}</div></div>
                 </div>
                 <div v-else class="edit-grid">
                    <div class="form-group"><label>Fabric Code/Type</label><input v-model="techPack.fabric.code" class="saas-input"/></div>
                    <div class="form-group"><label>Supplier</label><input v-model="techPack.fabric.supplier" class="saas-input"/></div>
                    <div class="form-group"><label>Width (cm)</label><input v-model="techPack.fabric.width" type="number" class="saas-input"/></div>
                    <div class="form-group"><label>Dye Method</label><input v-model="techPack.fabric.dye" class="saas-input"/></div>
                    <div class="form-group"><label>Fabric Finish</label><input v-model="techPack.fabric.finish" class="saas-input"/></div>
                 </div>
              </div>
              
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Trims & Accessories', 'الإضافات والإكسسوارات') }}</h3>
                 <div v-if="!isEditing" class="info-grid">
                    <div class="info-item"><label>Zipper</label><div class="val">{{ techPack.trims?.zipper || '---' }}</div></div>
                    <div class="info-item"><label>Button</label><div class="val">{{ techPack.trims?.button || '---' }}</div></div>
                    <div class="info-item"><label>Cord</label><div class="val">{{ techPack.trims?.cord || '---' }}</div></div>
                    <div class="info-item"><label>Rib</label><div class="val">{{ techPack.trims?.rib || '---' }}</div></div>
                    <div class="info-item"><label>Thread</label><div class="val">{{ techPack.trims?.thread || '---' }}</div></div>
                 </div>
                 <div v-else class="edit-grid">
                    <div class="form-group"><label>Zipper Type</label><input v-model="techPack.trims.zipper" class="saas-input"/></div>
                    <div class="form-group"><label>Button Type</label><input v-model="techPack.trims.button" class="saas-input"/></div>
                    <div class="form-group"><label>Cord Type</label><input v-model="techPack.trims.cord" class="saas-input"/></div>
                    <div class="form-group"><label>Rib Type</label><input v-model="techPack.trims.rib" class="saas-input"/></div>
                    <div class="form-group"><label>Thread Type</label><input v-model="techPack.trims.thread" class="saas-input"/></div>
                 </div>
              </div>
           </div>
           
           <!-- Tab 3: Construction -->
           <div v-show="isAdmin && activeTab === 'construction'" class="tab-pane fade-in">
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Stitch & Construction', 'الخياطة والتصنيع') }}</h3>
                 <div v-if="!isEditing" class="info-grid">
                    <div class="info-item"><label>Stitch Type</label><div class="val">{{ techPack.stitch?.stitch || '---' }}</div></div>
                    <div class="info-item"><label>Seam Type</label><div class="val">{{ techPack.stitch?.seam || '---' }}</div></div>
                    <div class="info-item"><label>SPI</label><div class="val">{{ techPack.stitch?.spi || '---' }}</div></div>
                    <div class="info-item"><label>Reinforcement</label><div class="val">{{ techPack.stitch?.reinforcement || '---' }}</div></div>
                    <div class="info-item"><label>Seam Allowance</label><div class="val">{{ techPack.stitch?.allowance || '---' }}</div></div>
                 </div>
                 <div v-else class="edit-grid">
                    <div class="form-group"><label>Stitch Type</label><input v-model="techPack.stitch.stitch" class="saas-input"/></div>
                    <div class="form-group"><label>Seam Type</label><input v-model="techPack.stitch.seam" class="saas-input"/></div>
                    <div class="form-group"><label>SPI</label><input v-model="techPack.stitch.spi" class="saas-input"/></div>
                    <div class="form-group"><label>Reinforcement</label><input v-model="techPack.stitch.reinforcement" class="saas-input"/></div>
                    <div class="form-group"><label>Seam Allowance</label><input v-model="techPack.stitch.allowance" class="saas-input"/></div>
                 </div>
              </div>
              
              <div class="saas-card mb-4">
                 <h3 class="card-title flex justify-between">{{ t('Model Anatomy (Parts List)', 'أجزاء القطعة') }}</h3>
                 <div v-if="!isEditing" class="overflow-x-auto">
                    <table class="saas-table text-left">
                      <thead><tr><th>Piece</th><th>Part Name</th><th class="text-center">Qty</th><th class="text-center">Ref</th></tr></thead>
                      <tbody>
                        <tr v-for="(p, i) in techPack.parts" :key="i">
                          <td><span class="font-bold">{{ p.piece }}</span></td>
                          <td>{{ p.part }}</td>
                          <td class="text-center"><span class="badge-blue">{{ p.qty }}</span></td>
                          <td class="text-center">
                            <img v-if="p.img" :src="p.img" style="width: 32px; height: 32px; object-fit: cover; border-radius: 6px; border:1px solid #e2e8f0; display:inline-block;" />
                            <span v-else class="text-gray-300">-</span>
                          </td>
                        </tr>
                        <tr v-if="!techPack.parts?.length"><td colspan="4" class="empty-state">No parts defined.</td></tr>
                      </tbody>
                    </table>
                 </div>
                 <div v-else class="dynamic-list-container">
                    <div v-for="(p, i) in techPack.parts" :key="i" class="dynamic-edit-card">
                       <div class="edit-card-header">
                           <span class="font-bold text-gray-500 text-sm w-full">{{ t('Part #', 'جزء رقم ') }}{{ i + 1 }}</span>
                           <button @click="techPack.parts.splice(i, 1)" class="btn-delete-card"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
                       </div>
                       <div class="edit-card-grid">
                           <div class="form-group mb-0">
                               <label>{{ t('Piece', 'القطعة') }}</label>
                               <input v-model="p.piece" class="saas-input" placeholder="e.g. Front" />
                           </div>
                           <div class="form-group mb-0 md-col-span-2">
                               <label>{{ t('Part / Description', 'الجزء / الوصف') }}</label>
                               <input v-model="p.part" class="saas-input" placeholder="e.g. Left Pocket" />
                           </div>
                           <div class="form-group mb-0">
                               <label>{{ t('Quantity', 'الكمية') }}</label>
                               <input v-model="p.qty" type="number" class="saas-input text-center" placeholder="e.g. 2" />
                           </div>
                           <div class="form-group mb-0">
                               <label>{{ t('Reference Image', 'صورة مرجعية') }}</label>
                               <label class="modern-file-upload">
                                  <input type="file" @change="e => handleImageUpload(e, p, 'img')" accept="image/*" class="hidden-input" />
                                  <div class="upload-content" :class="{ 'has-file': p.img }">
                                     <img v-if="p.img" :src="p.img.startsWith('data:') || p.img.startsWith('http') ? p.img : ('/storage/' + p.img)" class="preview-thumb" />
                                     <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                     <span class="truncate">{{ p.img ? t('Change Image', 'تغيير الصورة') : t('Upload Image', 'رفع صورة') }}</span>
                                  </div>
                               </label>
                           </div>
                       </div>
                    </div>
                    <button @click="techPack.parts.push({ piece: '', part: '', qty: 1, img: '' })" class="btn-add-modern">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        {{ t('Add New Part', 'إضافة جزء جديد') }}
                    </button>
                 </div>
              </div>
              
              <div class="saas-card mb-4" style="overflow-x: auto;">
                 <h3 class="card-title">{{ t('Measurement Table', 'جدول القياسات') }}</h3>
                 <table class="saas-table" v-if="!isEditing">
                  <thead><tr><th class="text-left">POM</th><th class="text-center">Value</th><th class="text-center">Tol(+/-)</th></tr></thead>
                  <tbody>
                    <tr v-for="(m, i) in techPack.measurements" :key="i">
                      <td class="font-bold text-left">{{ m.point }}</td><td class="text-center text-blue-600 font-bold">{{ m.value }}</td><td class="text-center text-gray-500">{{ m.tol }}</td>
                    </tr>
                    <tr v-if="!techPack.measurements?.length"><td colspan="3" class="empty-state">No measurements defined.</td></tr>
                  </tbody>
                 </table>
                 <div v-else class="dynamic-list-container">
                    <div v-for="(m, i) in techPack.measurements" :key="i" class="dynamic-edit-card">
                       <div class="edit-card-header">
                           <span class="font-bold text-gray-500 text-sm w-full">{{ t('Measurement #', 'قياس رقم ') }}{{ i + 1 }}</span>
                           <button @click="techPack.measurements.splice(i, 1)" class="btn-delete-card"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
                       </div>
                       <div class="edit-card-grid">
                           <div class="form-group mb-0 md-col-span-2">
                               <label>{{ t('Point of Measure (POM)', 'نقطة القياس') }}</label>
                               <input v-model="m.point" class="saas-input" placeholder="e.g. Chest Width" />
                           </div>
                           <div class="form-group mb-0">
                               <label>{{ t('Value', 'القيمة') }}</label>
                               <input v-model="m.value" class="saas-input text-center font-bold text-blue-600" placeholder="e.g. 50" />
                           </div>
                           <div class="form-group mb-0">
                               <label>{{ t('Tolerance (+/-)', 'نسبة التفاوت') }}</label>
                               <input v-model="m.tol" class="saas-input text-center text-gray-500" placeholder="e.g. 0.5 cm" />
                           </div>
                       </div>
                    </div>
                    <button @click="techPack.measurements.push({ point: '', value: '', tol: '0.5 cm' })" class="btn-add-modern">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        {{ t('Add Point of Measure', 'إضافة نقطة قياس') }}
                    </button>
                 </div>
              </div>
           </div>
           
           <!-- Tab 4: Production -->
           <div v-show="isAdmin && activeTab === 'production'" class="tab-pane fade-in">
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Sewing Operation Sequence', 'تسلسل الخياطة') }}</h3>
                 <div v-if="!isEditing" class="flex flex-col gap-3">
                    <div v-for="(step, i) in techPack.steps" :key="i" class="flex items-start gap-3 bg-gray-50/50 p-3 rounded-xl border border-gray-100">
                       <div class="flex-shrink-0 bg-blue-100 text-blue-600 font-bold w-8 h-8 rounded-full flex items-center justify-center text-sm shadow-sm">{{ i + 1 }}</div>
                       <div class="text-gray-700 font-medium leading-relaxed pt-1">{{ step || t('Empty Step', 'خطوة فارغة') }}</div>
                    </div>
                    <div v-if="!techPack.steps?.length" class="empty-state">No sewing steps defined.</div>
                 </div>
                 <div v-else class="dynamic-list-container">
                    <div v-for="(step, i) in techPack.steps" :key="i" class="dynamic-edit-card flex max-md:flex-col gap-3">
                      <div class="step-num-icon flex-shrink-0 bg-blue-100 text-blue-600 font-bold w-10 h-10 rounded-full flex items-center justify-center">{{ i + 1 }}</div>
                      <div class="form-group mb-0 flex-1 w-full">
                         <label class="md:hidden">{{ t('Step Description', 'وصف الخطوة') }}</label>
                         <input v-model="techPack.steps[i]" @keyup.enter="techPack.steps.push('')" class="saas-input" placeholder="e.g. Join front and back panels (Press Enter to add new)" />
                      </div>
                      <button @click="techPack.steps.splice(i, 1)" class="btn-delete-card self-center mt-2 md:mt-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
                    </div>
                    <button @click="techPack.steps.push('')" class="btn-add-modern">
                       <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                       {{ t('Add Sewing Step', 'إضافة خطوة خياطة') }}
                    </button>
                 </div>
              </div>
              
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Detail Zoom Image', 'صورة التكبير والتفاصيل') }}</h3>
                 <div v-if="!isEditing">
                   <div v-if="techPack.detail_zoom" class="img-zoom-wrap" @click="zoomImage(techPack.detail_zoom)">
                       <img :src="techPack.detail_zoom.startsWith('data:') || techPack.detail_zoom.startsWith('http') ? techPack.detail_zoom : ('/storage/' + techPack.detail_zoom)" class="w-full max-w-sm rounded-xl shadow-sm object-cover" />
                   </div>
                   <div v-else class="empty-state">No zoom image provided.</div>
                 </div>
                 <div v-else class="img-upload-zone">
                   <div v-if="techPack.detail_zoom" class="preview-zoom-card">
                     <img :src="techPack.detail_zoom.startsWith('data:') || techPack.detail_zoom.startsWith('http') ? techPack.detail_zoom : ('/storage/' + techPack.detail_zoom)" class="preview-img" />
                     <button @click="techPack.detail_zoom = ''" class="btn-remove-zoom">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                     </button>
                   </div>
                   <label v-else class="modern-dropzone">
                       <input type="file" accept="image/*" @change="e => handleImageUpload(e, techPack, 'detail_zoom')" class="hidden-input" />
                       <div class="dropzone-content">
                           <div class="upload-icon-circle">
                               <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                           </div>
                           <h4 class="font-bold text-gray-800">{{ t('Upload Reference Image', 'رفع صورة مرجعية') }}</h4>
                           <span class="text-xs font-semibold text-gray-400 mt-1">{{ t('Drag & Drop or Click to browse (PNG, JPG)', 'اسحب الصورة هنا أو اضغط للاختيار') }}</span>
                       </div>
                   </label>
                 </div>
              </div>
           </div>
           
           <!-- Tab 5: Packaging -->
           <div v-show="isAdmin && activeTab === 'packaging'" class="tab-pane fade-in">
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Label Production', 'إنتاج الليبلات') }}</h3>
                 <div v-if="!isEditing" class="info-grid">
                    <div class="info-item"><label>Main Label</label><div class="val">{{ techPack.labels?.main || '---' }}</div></div>
                    <div class="info-item"><label>Size Label</label><div class="val">{{ techPack.labels?.size || '---' }}</div></div>
                    <div class="info-item"><label>Care Label</label><div class="val">{{ techPack.labels?.care || '---' }}</div></div>
                    <div class="info-item"><label>Placement</label><div class="val">{{ techPack.labels?.pos || '---' }}</div></div>
                 </div>
                 <div v-else class="edit-grid">
                    <div class="form-group"><label>Main Label Type</label><input v-model="techPack.labels.main" class="saas-input"/></div>
                    <div class="form-group"><label>Size Label Type</label><input v-model="techPack.labels.size" class="saas-input"/></div>
                    <div class="form-group"><label>Care Label Type</label><input v-model="techPack.labels.care" class="saas-input"/></div>
                    <div class="form-group"><label>Label Position</label><input v-model="techPack.labels.pos" class="saas-input"/></div>
                 </div>
              </div>
              
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Marker & Consumption', 'تخطيط الماركر') }}</h3>
                 <div v-if="!isEditing" class="info-grid">
                    <div class="info-item"><label>Marker Length</label><div class="val">{{ techPack.marker?.length || '---' }}</div></div>
                    <div class="info-item"><label>Fabric Width</label><div class="val">{{ techPack.marker?.width || '---' }}</div></div>
                    <div class="info-item"><label>Efficiency</label><div class="val">{{ techPack.marker?.eff ? techPack.marker.eff + '%' : '---' }}</div></div>
                    <div class="info-item"><label>Parts Count</label><div class="val">{{ techPack.marker?.parts || '---' }}</div></div>
                 </div>
                 <div v-else class="edit-grid">
                    <div class="form-group"><label>Marker Length</label><input v-model="techPack.marker.length" class="saas-input"/></div>
                    <div class="form-group"><label>Fabric Width</label><input v-model="techPack.marker.width" class="saas-input"/></div>
                    <div class="form-group"><label>Efficiency (%)</label><input v-model="techPack.marker.eff" class="saas-input"/></div>
                    <div class="form-group"><label>Parts Count</label><input v-model="techPack.marker.parts" class="saas-input"/></div>
                 </div>
              </div>
              
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Packaging Details', 'تفاصيل التغليف') }}</h3>
                 <div v-if="!isEditing" class="info-grid">
                    <div class="info-item"><label>Packaging Type</label><div class="val">{{ techPack.packaging?.type || '---' }}</div></div>
                    <div class="info-item"><label>Folding Method</label><div class="val">{{ techPack.packaging?.fold || '---' }}</div></div>
                    <div class="info-item"><label>Carton Qty</label><div class="val">{{ techPack.packaging?.qty || '---' }}</div></div>
                    <div class="info-item"><label>Barcode Required</label><div class="val"><span class="badge-blue" v-if="techPack.packaging?.barcode">Yes</span><span v-else class="text-gray-400">No</span></div></div>
                 </div>
                 <div v-else class="edit-grid">
                    <div class="form-group"><label>Packaging Type</label><input v-model="techPack.packaging.type" class="saas-input"/></div>
                    <div class="form-group"><label>Folding Method</label><input v-model="techPack.packaging.fold" class="saas-input"/></div>
                    <div class="form-group"><label>Carton Quantity</label><input v-model="techPack.packaging.qty" type="number" class="saas-input"/></div>
                    <div class="form-group"><label>Barcode Required</label><input type="checkbox" v-model="techPack.packaging.barcode" class="checkbox-saas" /></div>
                 </div>
              </div>
              
              <div class="saas-card mb-4">
                 <h3 class="card-title">{{ t('Internal Factory Notes', 'ملاحظات المصنع الداخلية') }}</h3>
                 <div v-if="!isEditing" class="notes-display">{{ techPack.notes || order.factory_notes || order.notes_factory || '---' }}</div>
                 <textarea v-else v-model="techPack.notes" class="saas-textarea" placeholder="Internal instructions for the factory..."></textarea>
              </div>
           </div>
           
           <!-- Tab 6: Chat -->
           <div v-show="activeTab === 'chat'" class="tab-pane fade-in">
              <div class="saas-card chat-wrapper p-0 overflow-hidden" style="height:600px; display:flex; flex-direction:column;">
                 <div class="bg-gray-50 border-b border-gray-100 p-4 font-bold text-gray-800 flex items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> {{ t('Direct Messaging', 'المحادثة المباشرة') }}
                 </div>
                 <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-4 bg-gray-50/50" ref="messageBox">
                    <div v-for="msg in messages" :key="msg.id" :class="['message-bubble', msg.sender_id === currentUser.id ? 'my-msg' : 'their-msg']">
                       <div class="bubble-sender" v-if="msg.sender_id !== currentUser.id">{{ msg.sender?.name }}</div>
                       <div class="bubble-text">{{ msg.content }}</div>
                       <div class="bubble-time">{{ formatTime(msg.created_at) }}</div>
                    </div>
                    <div v-if="messages.length === 0" class="empty-state" style="margin:auto;">
                       {{ t('Start a conversation with the production team.', 'ابدأ المحادثة مع فريق الإنتاج.') }}
                    </div>
                 </div>
                 <div class="p-4 border-t border-gray-100 flex gap-2 bg-white">
                    <textarea v-model="newMessage" :placeholder="t('Type message...', 'اكتب رسالة...')" @keyup.enter.prevent="sendMessage" class="saas-textarea !min-h-[44px] !h-[44px] !mb-0"></textarea>
                    <BaseButton @click="sendMessage" :disabled="sending || !newMessage.trim()" class="btn-gradient !w-12 !h-[44px] !p-0 mx-auto justify-center">
                       <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </BaseButton>
                 </div>
              </div>
           </div>

        </div>
      </div>
    </div>
    
    <!-- Floating Admin Save Bar / Mobile FAB -->
    <div v-if="isAdmin && isEditing" class="floating-wrapper fade-in">
       <!-- Desktop Full Bar -->
       <div class="floating-save-bar desktop-save-bar">
           <div class="floating-save-container">
              <div class="flex-1 rtl-text-right">
                 <h3 class="font-bold text-gray-800 text-lg">{{ t('Unsaved Changes', 'تغييرات لم يتم حفظها') }}</h3>
                 <p class="text-xs font-semibold text-gray-500">{{ t('Please save your modifications before leaving.', 'يرجى حفظ التعديلات قبل المغادرة.') }}</p>
              </div>
              <div class="flex gap-3 justify-end">
                 <BaseButton variant="outline" class="bg-gray-100 hover:bg-gray-200 border-none text-gray-700 min-w-[100px]" @click="isEditing = false" :disabled="isSavingAdminData">
                    {{ t('Discard', 'إلغاء') }}
                 </BaseButton>
                 <BaseButton class="btn-gradient-success" @click="saveTechPackData" :loading="isSavingAdminData">
                    <template #icon-left><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg></template>
                    {{ t('Save Improvements', 'حفظ التعديلات') }}
                 </BaseButton>
              </div>
           </div>
       </div>

       <!-- Mobile Floating Action Buttons (FAB) -->
       <div class="mobile-fab-container">
          <button class="fab-btn fab-cancel" @click="isEditing = false" :disabled="isSavingAdminData" title="Discard">
             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
          <button class="fab-btn fab-save" @click="saveTechPackData" :disabled="isSavingAdminData" title="Save Improvements">
             <div v-if="isSavingAdminData" class="loader-spinner"></div>
             <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          </button>
       </div>
    </div>

    <AlertModal :show="alertModal.show" :title="alertModal.title" :message="alertModal.message" :type="alertModal.type" :isRtl="isRtl" @close="alertModal.show = false" />
    <div v-if="showStatusModal" class="modal-overlay" @click="showStatusModal = false">
       <div class="modal-card" @click.stop>
          <h3 class="font-bold text-gray-800 text-lg mb-4">{{ t('Update Order Status', 'تحديث حالة الطلب') }}</h3>
          <div class="flex flex-col gap-2">
             <button v-for="(val, key) in statusMap" :key="key" 
                     @click="updateOrderStatus(key)"
                     class="text-left px-4 py-3 rounded-xl border border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all font-bold text-gray-600 flex justify-between items-center"
                     :class="{ '!border-blue-500 !bg-blue-50 !text-blue-600': order.status === key }"
             >
                {{ t(val.en, val.ar) }}
                <div class="w-2 h-2 rounded-full" :class="'bg-' + (order.status === key ? 'blue' : 'gray') + '-500'"></div>
             </button>
          </div>
       </div>
    </div>
    
  </div>
  </Layout>
</template>

<script setup>
import Layout from '../../components/Layout.vue';
import BaseCard from '../../components/UI/BaseCard.vue';
import BaseButton from '../../components/UI/BaseButton.vue';
import BaseInput from '../../components/UI/BaseInput.vue';
import AlertModal from '../../components/UI/AlertModal.vue';
import ModelAnatomyTable from '../../components/Orders/TechPack/ModelAnatomyTable.vue';
import MarkerConsumption from '../../components/Orders/TechPack/MarkerConsumption.vue';
import OrderActions from './Components/OrderActions.vue';

import { ref, reactive, onMounted, nextTick, computed, watch, onBeforeUnmount } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { hasPermission } from '../../utils/permissions';
import { useLang } from '../../composables/useLang';

const props = defineProps({
  id: { type: [String, Number], required: true }
});

const { t, isRtl } = useLang();

// Auth header helper — single localStorage read per call
const authHeaders = () => ({ Authorization: `Bearer ${localStorage.getItem('auth_token')}` });

const route = useRoute();
const router = useRouter();
const order = ref(null);
const messages = ref([]);
const newMessage = ref('');
const currentUser = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => currentUser.value ? hasPermission(currentUser.value, perm) : false;
const messageBox = ref(null);
const sending = ref(false);
const updating = ref(false);
const isSavingAdminData = ref(false);
const statusTarget = ref('');
const activeTab = ref('overview'); // 'production', 'client', 'chat'
const isMobile = ref(false);

const isEditing = ref(false);
const techPack = ref({
   fabric: {}, trims: { zipper: '', button: '', cord: '', rib: '', thread: '' }, stitch: {}, labels: {}, marker: {}, packaging: {}, steps: [], parts: [], measurements: [], notes: '', detail_zoom: ''
});

const handleImageUpload = (event, targetObj, key) => {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        targetObj[key] = e.target.result;
    };
    reader.readAsDataURL(file);
};

const saveTechPackData = async () => {
    isSavingAdminData.value = true;
    try {
        await axios.post(`/api/orders/${route.params.id}/tech-pack`, techPack.value, {
            headers: authHeaders()
        });
        isEditing.value = false;
        await fetchData();
        showAlert(t('Tech Pack data has been successfully saved!', 'تم حفظ بيانات الملف الفني بنجاح!'), '', 'success');
    } catch {
        showAlert(t('Error saving Tech Pack.', 'خطأ في حفظ الملف الفني.'), '', 'error');
    } finally {
        isSavingAdminData.value = false;
    }
};

const checkMobile = () => {
  isMobile.value = window.innerWidth <= 768;
};

onMounted(() => {
  checkMobile();
  window.addEventListener('resize', checkMobile);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkMobile);
});

watch(isMobile, (newVal) => {
  if (!newVal) { // If no longer mobile, reset to production tab
    activeTab.value = 'production';
  }
});

const setTab = (t) => {
  activeTab.value = t;
  window.scrollTo({ top: 0, behavior: 'smooth' });
};
const showStatusModal = ref(false);
const showMarkerModal = ref(false);
const showTechModal = ref(false);
const savingMarker = ref(false);
const savingTech = ref(false);

const alertModal = reactive({
  show: false,
  title: '',
  message: '',
  type: 'info'
});

const showAlert = (message, title = '', type = 'info') => {
  alertModal.message = message;
  alertModal.title = title || (type === 'error' ? t('Error', 'خطأ') : t('Notification', 'تنبيه'));
  alertModal.type = type;
  alertModal.show = true;
};

const collapsed = reactive({
  fabric: false,
  trims: true,
  stitch: true,
  labels: true,
  marker: false,
  packaging: true,
});

const scrollToAdminSection = () => {
  activeTab.value = 'materials';
  isEditing.value = true;
  nextTick(() => {
    const el = document.getElementById('admin-production-setup');
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' });
    }
  });
};

const adminForm = reactive({
  fabric_code: '',
  fabric_supplier: '',
  fabric_width: '',
  dye_method: '',
  fabric_finish: '',
  zipper_type: '',
  button_type: '',
  cord_type: '',
  rib_type: '',
  thread_type: '',
  stitch_type: '',
  seam_type: '',
  top_stitch: '',
  reinforcement: '',
  main_label_type: '',
  size_label_type: '',
  care_label_type: '',
  label_position: '',
  packaging_type: '',
  folding_method: '',
  carton_qty: '',
  barcode_required: false,
  factory_notes: ''
});

const formatSizes = (sizes) => {
  if (!sizes) return '---';
  if (typeof sizes === 'string') return sizes;
  return Object.entries(sizes).map(([size, qty]) => `${size}: ${qty}`).join(', ');
};

const activeMarker = computed(() => order.value?.marker_plans?.[0] || null);
const markerForm = reactive({
   marker_length: '',
   fabric_width: '',
   efficiency: '',
   part_count: ''
});

watch(activeMarker, (newVal) => {
   if (newVal) {
      markerForm.marker_length = newVal.marker_length;
      markerForm.fabric_width = newVal.fabric_width;
      markerForm.efficiency = newVal.efficiency;
      markerForm.part_count = newVal.part_count;
   }
}, { immediate: true });

const saveMarker = async () => {
   savingMarker.value = true;
   try {
      const headers = authHeaders();
      if (activeMarker.value) {
         await axios.put(`/api/marker-plans/${activeMarker.value.id}`, markerForm, { headers });
      } else {
         await axios.post('/api/marker-plans', { ...markerForm, order_id: order.value.id }, { headers });
      }
      await fetchData();
      showMarkerModal.value = false;
   } catch {
      showAlert(t('Error saving marker', 'خطأ في حفظ الماركر'), '', 'error');
   } finally {
      savingMarker.value = false;
   }
};

const isAdmin = computed(() => ['admin', 'superadmin', 'manager'].includes(currentUser.value?.role));

const statusMap = {
  submitted: { en: 'Submitted', ar: 'تم الإرسال' },
  pending: { en: 'Pending', ar: 'قيد الانتظار' },
  technical_ready: { en: 'Technical Ready', ar: 'جاهز فنياً' },
  in_review: { en: 'In Review', ar: 'قيد المراجعة' },
  sampling: { en: 'Sampling', ar: 'أخذ عينات' },
  approved: { en: 'Approved', ar: 'مقبول' },
  production: { en: 'Production', ar: 'في التصنيع' },
  completed: { en: 'Completed', ar: 'مكتمل' },
  cancelled: { en: 'Cancelled', ar: 'ملغي' }
};

const seasonsAr = {
  Summer: 'صيف',
  Winter: 'شتاء',
  Spring: 'ربيع',
  Autumn: 'خريف'
};

const updateOrderStatus = async (newStatus) => {
  statusTarget.value = newStatus;
  updating.value = true;
  try {
    await axios.patch(`/api/orders/${order.value.id}/status`, { status: newStatus }, {
      headers: authHeaders()
    });
    order.value.status = newStatus;
    showStatusModal.value = false;
  } catch {
    showAlert(t('Update failed', 'فشل التحديث'), '', 'error');
  } finally {
    updating.value = false;
  }
};

const populateAdminForm = (orderData) => {
  if (!orderData) return;
  const prod = orderData.production || {};
  const fd = orderData.fabric_details || {};
  const pd = orderData.production_details || {};
  
  adminForm.fabric_code = prod.fabric_code || fd.code || orderData.fabric_code || '';
  adminForm.fabric_supplier = prod.fabric_supplier || fd.supplier || orderData.fabric_supplier || '';
  adminForm.fabric_width = prod.fabric_width || fd.width || pd.fabric_width || '';
  adminForm.dye_method = prod.dye_method || fd.dye_method || pd.dye_method || '';
  adminForm.fabric_finish = prod.fabric_finish || fd.finish || pd.fabric_finish || '';
  adminForm.zipper_type = prod.zipper_type || orderData.zipper_type || pd.zipper_type || '';
  adminForm.button_type = prod.button_type || orderData.button_type || pd.button_type || '';
  adminForm.cord_type = prod.cord_type || orderData.cord_type || pd.cord_type || '';
  adminForm.rib_type = prod.rib_type || orderData.rib_type || pd.rib_type || '';
  adminForm.thread_type = prod.thread_type || pd.thread_type || '';
  adminForm.stitch_type = prod.stitch_type || orderData.stitching_type || pd.stitch_type || '';
  adminForm.seam_type = prod.seam_type || pd.seam_type || '';
  adminForm.top_stitch = prod.top_stitch || pd.top_stitch || '';
  adminForm.reinforcement = prod.reinforcement || pd.reinforcement || '';
  adminForm.main_label_type = prod.label_type || orderData.main_label_type || pd.main_label_type || '';
  adminForm.size_label_type = orderData.size_label_type || pd.size_label_type || '';
  adminForm.care_label_type = orderData.care_label_type || pd.care_label_type || '';
  adminForm.label_position = prod.label_position || pd.label_position || '';
  adminForm.packaging_type = prod.packaging_type || orderData.packaging_type || pd.packaging_type || '';
  adminForm.folding_method = orderData.folding_method || pd.folding_method || '';
  adminForm.carton_qty = prod.carton_quantity || pd.carton_qty || '';
  adminForm.barcode_required = orderData.barcode_required || pd.barcode_required || false;
  adminForm.factory_notes = prod.factory_notes || pd.factory_notes || orderData.notes_factory || '';
};

const saveProductionDetails = async () => {
  isSavingAdminData.value = true;
  try {
    const payload = {
      fabric_code: adminForm.fabric_code,
      fabric_supplier: adminForm.fabric_supplier,
      fabric_width: adminForm.fabric_width,
      dye_method: adminForm.dye_method,
      fabric_finish: adminForm.fabric_finish,
      zipper_type: adminForm.zipper_type,
      button_type: adminForm.button_type,
      cord_type: adminForm.cord_type,
      rib_type: adminForm.rib_type,
      thread_type: adminForm.thread_type,
      stitch_type: adminForm.stitch_type,
      seam_type: adminForm.seam_type,
      top_stitch: adminForm.top_stitch,
      reinforcement: adminForm.reinforcement,
      label_type: adminForm.main_label_type,
      label_position: adminForm.label_position,
      packaging_type: adminForm.packaging_type,
      carton_quantity: adminForm.carton_qty,
      factory_notes: adminForm.factory_notes,
    };
    await axios.post(`/api/orders/${order.value.id}/production`, payload, { headers: authHeaders() });
    await fetchData();
    showAlert(t('Production details saved successfully!', 'تم حفظ تفاصيل الإنتاج بنجاح!'), '', 'success');
  } catch {
    showAlert(t('Failed to save production details.', 'فشل حفظ تفاصيل الإنتاج.'), '', 'error');
  } finally {
    isSavingAdminData.value = false;
  }
};

const fetchData = async () => {
  if (!can('orders.view')) return router.push('/dashboard');
  const headers = authHeaders();
  try {
    const resOrder = await axios.get(`/api/orders/${route.params.id}`, { headers });
    order.value = resOrder.data;
    populateAdminForm(resOrder.data);

    // Fetch tech pack (admin only) and messages in parallel
    const promises = [
      axios.get(`/api/orders/${route.params.id}/messages`, { headers })
    ];
    if (isAdmin.value) {
      promises.unshift(axios.get(`/api/orders/${route.params.id}/tech-pack`, { headers }).catch(() => null));
    }

    const results = await Promise.all(promises);
    const tpRes = isAdmin.value ? results[0] : null;
    const msgRes = isAdmin.value ? results[1] : results[0];

    if (tpRes) {
      const tpData = order.value?.tech_pack || {};
      
      techPack.value = {
         fabric: tpData.fabric ?? {},
         trims: tpData.trims ?? {},
         stitch: tpData.stitch ?? {},
         labels: tpData.labels ?? {},
         marker: tpData.marker ?? {},
         packaging: tpData.packaging ?? {},
         steps: Array.isArray(tpData.steps) ? tpData.steps : [],
         parts: Array.isArray(tpData.parts) ? tpData.parts : [],
         measurements: Array.isArray(tpData.measurements) ? tpData.measurements : [],
         notes: tpData.notes ?? '',
         detail_zoom: tpData.detail_zoom ?? ''
      };

      // Fallback to order relationships if tech pack JSON is empty
      const isTpEmpty = !Object.keys(techPack.value.fabric).length && !Object.keys(techPack.value.trims).length;
      if (isTpEmpty && order.value) {
         const po = order.value;
         const prod = po.production || {};
         techPack.value.fabric = {
           code: prod.fabric_code || po.fabric_details?.code || po.fabric_type || '', 
           supplier: prod.fabric_supplier || po.fabric_details?.supplier || '',
           width: prod.fabric_width || po.fabric_details?.width || '', 
           dye: prod.dye_method || po.fabric_details?.dye_method || '', 
           finish: prod.fabric_finish || po.fabric_details?.finish || ''
         };
         techPack.value.trims = {
           zipper: prod.zipper_type || '', button: prod.button_type || '',
           cord: prod.cord_type || po.drawcord_type || '', rib: prod.rib_type || '', thread: prod.thread_type || ''
         };
         techPack.value.stitch = { 
           stitch: prod.stitching_type || prod.stitch_type || '', 
           seam: prod.seam_type || '', 
           spi: prod.spi || '', 
           thread: prod.thread_type || '', 
           reinforcement: prod.reinforcement || '', 
           allowance: prod.seam_allowance || '' 
         };
         techPack.value.labels = { 
           main: prod.main_label_type || '', 
           size: prod.size_label_type || '', 
           care: prod.care_label_type || '', 
           pos: prod.label_position || '' 
         };
         techPack.value.marker = { 
           length: po.markerPlans?.[0]?.marker_length || '', 
           width: po.markerPlans?.[0]?.fabric_width || '', 
           eff: po.markerPlans?.[0]?.efficiency || '', 
           parts: po.markerPlans?.[0]?.part_count || '' 
         };
         techPack.value.packaging = { 
           type: prod.packaging_type || '', 
           fold: prod.folding_method || '', 
           qty: prod.carton_quantity || '', 
           barcode: !!prod.barcode_required 
         };
         techPack.value.notes = prod.factory_notes || po.notes_factory || '';
         techPack.value.steps = Array.isArray(prod.sewing_sequence) ? prod.sewing_sequence : (Array.isArray(po.steps) ? po.steps : []);
         
         const pts = po.order_measurements || [];
         if (pts.length) {
            techPack.value.measurements = pts.map(p => ({ point: p.point_of_measure, value: p.dimension_value || '', tol: p.tolerance || '' }));
         }
         
         const anatomies = po.anatomies || [];
         if (anatomies.length) {
            techPack.value.parts = anatomies.map(p => ({ piece: p.piece_name || '', part: p.part_name || '', qty: p.count || 1, img: p.image_path || '' }));
         }
         
         techPack.value.detail_zoom = po.detail_zoom || '';
      }
    }

    messages.value = msgRes?.data ?? [];
    scrollToBottom();
  } catch { /* errors surfaced via alert in sub-calls */ }
};

onMounted(fetchData);

const sendMessage = async () => {
  if (!order.value || !currentUser.value || !newMessage.value.trim() || sending.value) return;
  sending.value = true;
  try {
    const receiverId = order.value.created_by === currentUser.value.id ? 1 : order.value.created_by;
    await axios.post('/api/messages', {
      order_id: order.value.id,
      receiver_id: receiverId,
      content: newMessage.value
    }, { headers: authHeaders() });
    newMessage.value = '';
    await fetchData();
  } finally {
    sending.value = false;
  }
};

const scrollToBottom = () => {
  nextTick(() => { if (messageBox.value) messageBox.value.scrollTop = messageBox.value.scrollHeight; });
};

const formatDate = (date) => new Date(date).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', { day: 'numeric', month: 'long', year: 'numeric' });
const formatFullDate = (date) => new Date(date).toLocaleDateString(isRtl.value ? 'ar-EG' : 'en-US', { 
   day: 'numeric', 
   month: 'short', 
   year: 'numeric',
   hour: '2-digit',
   minute: '2-digit'
});
const formatTime = (date) => new Date(date).toLocaleTimeString(isRtl.value ? 'ar-EG' : 'en-US', { hour: '2-digit', minute: '2-digit' });
const zoomImage = (path) => window.open('/storage/' + path, '_blank');
</script>

<style scoped>
.order-detail-view {
  background: #f8f9fc;
  min-height: 100vh;
  padding: 2rem 5%;
  font-family: 'Inter', system-ui, sans-serif;
  color: #1e293b;
}

/* Header */
.page-top-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}
.breadcrumb-mini {
  font-size: 0.875rem;
  font-weight: 700;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.breadcrumb-mini a {
  color: #0ea5e9;
  text-decoration: none;
  transition: opacity 0.2s;
}
.breadcrumb-mini a:hover { opacity: 0.8; }
.breadcrumb-mini .active { color: #0f172a; }

.actions-right {
  display: flex;
  gap: 1rem;
}
.btn-gradient {
  background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
  color: white;
  border: none;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-gradient:hover {
  box-shadow: 0 6px 16px rgba(14, 165, 233, 0.4);
  transform: translateY(-1px);
}

.tech-pack-top-btn {
  background: linear-gradient(135deg, #1e293b, #0f172a);
  color: white;
  border: none;
  font-weight: 700;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.tech-pack-top-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
}
.btn-gradient-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  transition: all 0.3s;
}

/* Layout Grid */
.dashboard-grid {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 2rem;
  align-items: start;
}

/* Sidebar */
.saas-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 10px 15px -3px rgba(0,0,0,0.02), 0 0 0 1px rgba(15, 23, 42, 0.03);
}
.profile-img-wrap {
  width: 140px;
  height: 140px;
  margin: 0 auto 1.25rem;
  border-radius: 20px;
  overflow: hidden;
  border: 4px solid #f8fafc;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}
.profile-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
.auth-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; }
.status-pill-large {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background: #f1f5f9;
  color: #475569;
}
.status-pill-large.approved { background: #dcfce7; color: #166534; }
.status-pill-large.production { background: #ffedd5; color: #9a3412; }
.status-pill-large.completed { background: #ccfbf1; color: #115e59; }

/* Tabs Navbar */
.saas-tabs-nav {
  display: flex;
  background: white;
  padding: 0.5rem;
  border-radius: 16px;
  gap: 0.5rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  overflow-x: auto;
}
.saas-tabs-nav button {
  flex: 1;
  padding: 0.875rem 1rem;
  border: none;
  background: transparent;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 700;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.saas-tabs-nav button:hover { background: #f8fafc; color: #0f172a; }
.saas-tabs-nav button.active {
  background: #0f172a;
  color: white;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.2);
}

/* Forms & Grids */
.card-title {
  font-size: 1.125rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f1f5f9;
}
.info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}
.edit-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.25rem;
}
.info-item label {
  display: block;
  font-size: 0.75rem;
  color: #94a3b8;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.25rem;
}
.info-item .val {
  font-size: 0.9375rem;
  font-weight: 800;
  color: #1e293b;
}

.saas-input {
  width: 100%;
  padding: 0.875rem 1rem;
  font-size: 0.9375rem;
  font-weight: 600;
  color: #1e293b;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  transition: all 0.2s;
}
.saas-input:focus {
  background: white;
  border-color: #0ea5e9;
  box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
  outline: none;
}
.saas-textarea {
  width: 100%;
  padding: 1rem;
  min-height: 120px;
  font-size: 0.9375rem;
  font-weight: 500;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  resize: vertical;
}

/* Colors & Gallery */
.colors-flex { display: flex; gap: 1rem; flex-wrap: wrap; }
.color-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 12px; background: white; border: 1px solid #f1f5f9; }
.color-circle { width: 36px; height: 36px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.color-meta .hex { font-size: 0.8125rem; font-weight: 800; color: #1e293b; margin-bottom: 2px; }
.color-meta .pantone { font-size: 0.7rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; }

.gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; }
.gallery-img-box { position: relative; aspect-ratio: 1; border-radius: 16px; overflow: hidden; cursor: pointer; border: 1px solid #e2e8f0; }
.gallery-img-box img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.gallery-img-box:hover img { transform: scale(1.05); }
.img-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; color: white; }
.gallery-img-box:hover .img-overlay { opacity: 1; }

/* Dynamic Arrays */
.dynamic-row { display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: center; }
.btn-delete {
  background: #fef2f2; color: #ef4444; border: none; border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; transition: all 0.2s;
}
.btn-delete:hover { background: #fee2e2; color: #b91c1c; transform: scale(1.05); }
.btn-add {
  display: block; width: 100%; padding: 0.875rem; background: transparent; border: 2px dashed #cbd5e1; color: #64748b; font-weight: 800; border-radius: 12px; cursor: pointer; transition: all 0.2s;
}
.btn-add:hover { border-color: #0ea5e9; color: #0ea5e9; background: #f0f9ff; }

/* Modern Edit Cards */
.dynamic-list-container {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
.dynamic-edit-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.25rem;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
  transition: border-color 0.2s;
}
.dynamic-edit-card:focus-within {
  border-color: #0ea5e9;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.08);
}
.edit-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px dashed #e2e8f0;
}
.btn-delete-card {
  background: #fef2f2;
  color: #ef4444;
  border: none;
  border-radius: 10px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-delete-card:hover {
  background: #fee2e2;
  transform: scale(1.05);
}
.edit-card-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}
@media (min-width: 768px) {
  .edit-card-grid { grid-template-columns: repeat(4, 1fr); }
  .md-col-span-2 { grid-column: span 2; }
}
.modern-file-upload {
  display: block;
  cursor: pointer;
  height: 48px;
}
.hidden-input { display: none; }
.upload-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  height: 100%;
  padding: 0 1rem;
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
  border-radius: 12px;
  color: #64748b;
  font-weight: 700;
  font-size: 0.8125rem;
  transition: all 0.2s;
}
.upload-content:hover {
  background: #f0f9ff;
  border-color: #0ea5e9;
  color: #0ea5e9;
}
.upload-content.has-file {
  background: #f0fdf4;
  border-color: #22c55e;
  border-style: solid;
  color: #15803d;
}
.preview-thumb {
  width: 24px;
  height: 24px;
  border-radius: 6px;
  object-fit: cover;
}
.btn-add-modern {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  padding: 1rem;
  background: #f8fafc;
  border: 2px dashed #cbd5e1;
  color: #475569;
  font-weight: 800;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-add-modern:hover {
  border-color: #0ea5e9;
  color: #0ea5e9;
  background: #f0f9ff;
}

.modern-dropzone {
  display: block;
  width: 100%;
  border: 2px dashed #cbd5e1;
  border-radius: 16px;
  background: #f8fafc;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 2.5rem 1rem;
}
.modern-dropzone:hover {
  border-color: #0ea5e9;
  background: #f0f9ff;
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.1);
}
.dropzone-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.upload-icon-circle {
  width: 56px;
  height: 56px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0ea5e9;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  margin-bottom: 1rem;
  border: 1px solid #e0f2fe;
}
.modern-dropzone:hover .upload-icon-circle {
  background: #0ea5e9;
  color: white;
}
.preview-zoom-card {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  border: 4px solid #f1f5f9;
  display: inline-block;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}
.preview-img {
  max-width: 100%;
  max-height: 350px;
  object-fit: contain;
  display: block;
}
.btn-remove-zoom {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(255, 255, 255, 0.9);
  color: #ef4444;
  border: none;
  border-radius: 12px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  backdrop-filter: blur(4px);
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: all 0.2s;
}
.btn-remove-zoom:hover {
  background: #ef4444;
  color: white;
  transform: scale(1.1);
}

.floating-save-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  border-top: 1px solid #e2e8f0;
  padding: 1rem 0;
  z-index: 1000;
  box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.05);
}
.floating-save-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 5%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

/* Mobile Floating Action Buttons */
.mobile-fab-container {
  position: fixed;
  bottom: 24px;
  right: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  z-index: 1005;
}
.rtl .mobile-fab-container {
  left: 24px;
  right: auto;
}
.fab-btn {
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  color: white;
}
.fab-btn:active {
  transform: scale(0.92);
}
.fab-cancel {
  background: white;
  color: #64748b;
  border: 1px solid #e2e8f0;
  width: 44px;
  height: 44px;
}
.fab-save {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  width: 60px;
  height: 60px;
}

@media (min-width: 768px) {
  .floating-save-container { flex-direction: row; justify-content: space-between; }
  .order-detail-view { padding-bottom: 100px; } /* Create space for floating bar */
  .mobile-fab-container { display: none !important; }
}
@media (max-width: 767px) {
  .order-detail-view { padding-bottom: 80px; }
  .floating-save-bar.desktop-save-bar { display: none !important; }
}

.saas-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.saas-table th { padding: 1rem; font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 800; border-bottom: 1px solid #f1f5f9; }
.saas-table td { padding: 1rem; font-size: 0.875rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
.badge-blue { background: #f0f9ff; color: #0ea5e9; padding: 4px 10px; border-radius: 8px; font-weight: 800; font-size: 0.75rem; }

/* Chat Bubbles */
.message-bubble { max-width: 80%; padding: 1rem 1.25rem; border-radius: 16px; margin-bottom: 0.5rem; position: relative; }
.my-msg { align-self: flex-end; background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%); color: white; border-bottom-right-radius: 4px; margin-left: auto; }
.their-msg { align-self: flex-start; background: white; color: #1e293b; border: 1px solid #e2e8f0; border-bottom-left-radius: 4px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.bubble-sender { font-size: 0.7rem; font-weight: 800; margin-bottom: 0.25rem; opacity: 0.8; }
.bubble-text { font-size: 0.9375rem; line-height: 1.5; font-weight: 500;}
.bubble-time { font-size: 0.65rem; text-align: right; margin-top: 0.5rem; opacity: 0.7; font-weight: 600; }

.fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Responsive Optimizations */
@media (max-width: 768px) {
  .order-detail-view {
    padding: 1rem 4%;
  }

  .page-top-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .header-left { width: 100%; }
  .mobile-only-title {
    display: block;
    font-size: 1.5rem;
    font-weight: 900;
    color: #0f172a;
    margin-top: 0.5rem;
  }
  .profile-card .auth-title { display: none; } /* Hide duplicate title in sidebar on mobile */

  .breadcrumb-mini { gap: 0.75rem; }
  .back-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: white;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    color: #0ea5e9;
  }

  .actions-right {
    width: 100%;
    flex-direction: column;
    gap: 0.75rem;
  }
  .actions-right button, .editing-actions {
    width: 100% !important;
    display: flex;
    justify-content: center;
  }
  .editing-actions { gap: 10px; }

  .dashboard-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .saas-tabs-nav {
    margin: 0 -4% 1.5rem;
    padding: 0.5rem;
    border-radius: 0;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 100;
    background: white;
    scrollbar-width: none;
  }
  .saas-tabs-nav::-webkit-scrollbar { display: none; }
  
  .saas-tabs-nav button {
    padding: 0.75rem 1.25rem;
    font-size: 0.8125rem;
    min-width: 100px;
  }

  .info-grid, .edit-grid {
    grid-template-columns: 1fr;
    gap: 1.25rem;
  }

  .gallery-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .dynamic-row {
    flex-direction: column;
    align-items: stretch;
    background: #f8fafc;
    padding: 1rem;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
  }
  .dynamic-row input { width: 100% !important; }
  .btn-delete { width: 100%; margin-top: 0.5rem; }

  .message-bubble { max-width: 92%; }
  
  .saas-card { padding: 1.25rem; }
}

@media (min-width: 769px) {
  .mobile-only-title { display: none; }
}

.header-left { display: flex; flex-direction: column; }
.active-code { font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; font-weight: 800; color: #64748b; }
.editing-actions { display: flex; gap: 10px; width: auto; }
</style>
