<template>
  <Layout>
    <div v-if="order" class="order-detail-view" :class="{ 'rtl': isRtl }">
      <!-- Breadcrumbs -->
      <div class="breadcrumb-mini no-print">
        <router-link to="/orders">{{ t('Orders', 'الطلبات') }}</router-link>
        <span class="sep">/</span>
        <span class="active">{{ order.order_code }}</span>
      </div>

      <!-- 1. Product Preview Card -->
      <BaseCard class="product-preview-card no-print">
        <div class="preview-flex">
          <div class="preview-img">
            <img :src="order.design_front_image ? '/storage/' + order.design_front_image : '/images/product-placeholder.png'" 
                 alt="product"
                 @error="(e) => e.target.src = '/images/product-placeholder.png'"
            >
          </div>
          <div class="preview-info">
            <h1 class="preview-title">{{ order.title || order.category?.name || t('Untitled Product', 'منتج بدون اسم') }}</h1>
            <div class="preview-meta-grid">
              <div class="p-meta">
                <span class="p-label">{{ t('Order Code', 'كود الطلب') }}</span>
                <span class="p-val">#{{ order.order_code }}</span>
              </div>
              <div class="p-meta">
                <span class="p-label">{{ t('Client', 'العميل') }}</span>
                <span class="p-val">{{ order.client?.brand_name || order.creator?.name }}</span>
              </div>
              <div class="p-meta">
                <span class="p-label">{{ t('Quantity', 'الكمية') }}</span>
                <span class="p-val">{{ order.production_details?.quantity || '---' }}</span>
              </div>
              <div class="p-meta">
                <span class="p-label">{{ t('Season', 'الموسم') }}</span>
                <span class="p-val">{{ t(order.season, seasonsAr[order.season]) }} {{ order.year }}</span>
              </div>
            </div>
            <div class="preview-status-row">
              <span class="status-pill-large" :class="order.status">
                 {{ t(statusMap[order.status]?.en, statusMap[order.status]?.ar) }}
              </span>
              <BaseButton v-if="can('orders.approve')" variant="ghost" size="sm" @click="showStatusModal = true">
                {{ t('Update Status', 'تحديث الحالة') }}
              </BaseButton>
            </div>
          </div>
          <div class="preview-actions">
            <BaseButton v-if="['admin', 'superadmin', 'manager'].includes(currentUser?.role)" variant="primary" @click="scrollToAdminSection">
              <template #icon-left>
                 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
              </template>
              {{ t('Fill Production Specs', 'تعبئة مواصفات الإنتاج') }}
            </BaseButton>

          </div>
        </div>
      </BaseCard>

      <!-- 2. Mobile Tabs Navigation -->
      <div class="mobile-tabs-nav no-print">
        <button 
          :class="{ active: activeTab === 'production' }" 
          @click="setTab('production')"
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/><path d="m17 7-5-5-5 5M17 17l-5 5-5-5"/></svg>
          {{ t('Production', 'الإنتاج') }}
        </button>
        <button 
          :class="{ active: activeTab === 'client' }" 
          @click="setTab('client')"
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
          {{ t('Specs', 'المواصفات') }}
        </button>
        <button 
          :class="{ active: activeTab === 'chat' }" 
          @click="setTab('chat')"
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          {{ t('Messages', 'المراسلات') }}
        </button>
      </div>

      <div class="detail-grid">
        <div class="detail-main">
          <!-- ====== SECTION 1: CLIENT SPECIFICATIONS ====== -->
          <div v-if="activeTab === 'client' || !isMobile" class="workflow-section">
            <h2 class="workflow-title">{{ t('CLIENT SPECIFICATIONS', 'مواصفات العميل') }}</h2>
            
            <BaseCard class="info-card read-only-card" :title="t('Technical Specifications', 'المواصفات الفنية')">
              <div class="spec-list">
                <!-- Group 1 -->
                <div class="spec-row">
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Item Category', 'تصنيف القطعة') }}</span>
                    <span class="spec-value">{{ order.category?.name || '---' }}</span>
                  </div>
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Item Type', 'نوع القطعة') }}</span>
                    <span class="spec-value">{{ order.production_details?.item_type || '---' }}</span>
                  </div>
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Fit / Style', 'القصة / الستايل') }}</span>
                     <span class="spec-value">{{ order.production_details?.fit || t('Regular', 'عادي') }}</span>
                  </div>
                </div>
                <div class="divider"></div>
                <!-- Group 2 -->
                <div class="spec-row">
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Fabric Type', 'نوع القماش') }}</span>
                    <span class="spec-value">{{ order.fabric_details?.type || '---' }}</span>
                  </div>
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Fabric Weight', 'وزن القماش') }}</span>
                    <span class="spec-value">{{ order.fabric_details?.weight ? order.fabric_details.weight + ' gsm' : '---' }}</span>
                  </div>
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Composition', 'التكوين') }}</span>
                     <span class="spec-value">{{ order.fabric_details?.composition || t('Standard', 'قياسي') }}</span>
                  </div>
                </div>
                <div class="divider"></div>
                <!-- Group 3 -->
                <div class="spec-row">
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Total Quantity', 'إجمالي الكمية') }}</span>
                    <span class="spec-value">{{ order.production_details?.quantity || '---' }}</span>
                  </div>
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Sizes', 'المقاسات') }}</span>
                    <span class="spec-value">{{ formatSizes(order.production_details?.sizes) }}</span>
                  </div>
                  <div class="spec-item">
                    <span class="spec-label">{{ t('Season / Year', 'الموسم / السنة') }}</span>
                    <span class="spec-value">{{ t(order.season, seasonsAr[order.season]) }} {{ order.year }}</span>
                  </div>
                </div>
              </div>
            </BaseCard>

            <BaseCard v-if="order.measurements && Object.keys(order.measurements).length" class="info-card read-only-card" :title="t('Measurement Table (cm)', 'جدول القياسات (سم)')">
              <div class="measurements-flex">
                <div v-for="(val, label) in order.measurements" :key="label" class="m-box">
                  <span class="m-label">{{ label }}</span>
                  <span class="m-val">{{ val }}</span>
                </div>
              </div>
            </BaseCard>

            <BaseCard v-if="order.colors && order.colors.length" class="info-card read-only-card" :title="t('Color Palette', 'لوحة الألوان')">
              <div class="colors-flex">
                <div v-for="(color, idx) in order.colors" :key="idx" class="color-item">
                  <div class="color-circle" :style="{ background: color.hex }"></div>
                  <div class="color-meta">
                     <div class="hex">{{ color.name || color.hex }}</div>
                     <div class="pantone">{{ color.pantone || color.hex }}</div>
                  </div>
                </div>
              </div>
            </BaseCard>

            <!-- ====== SECTION: ADMIN PRODUCTION SETUP ====== -->
            <div v-if="(activeTab === 'production' || !isMobile) && ['admin', 'superadmin', 'manager'].includes(currentUser?.role)" id="admin-production-setup" class="workflow-section">
              <h2 class="workflow-title">{{ t('Admin Production Setup', 'إعداد إنتاج المسؤول') }}</h2>
              <p class="admin-section-subtitle mb-1">{{ t('Factory Production Details', 'تفاصيل إنتاج المصنع') }}</p>
              
              <!-- Fabric Details (Collapsible) -->
              <BaseCard class="info-card admin-spec-card collapsible-card" :class="{ 'collapsed': collapsed.fabric }">
                <template #header>
                  <div class="collapsible-header" @click="collapsed.fabric = !collapsed.fabric">
                    <span class="collapsible-title">{{ t('Fabric Production Details', 'تفاصيل إنتاج القماش') }}</span>
                    <svg class="chevron" :class="{ 'open': !collapsed.fabric }" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                  </div>
                </template>
                <div class="collapsible-body">
                  <div class="admin-form-grid">
                    <BaseInput v-model="adminForm.fabric_code" :label="t('Fabric Code', 'كود القماش')" :placeholder="t('e.g. FC-2024-001', 'مثال: FC-2024-001')" />
                    <BaseInput v-model="adminForm.fabric_supplier" :label="t('Fabric Supplier', 'مورد القماش')" :placeholder="t('Supplier name', 'اسم المورد')" />
                    <BaseInput v-model="adminForm.fabric_width" type="number" :label="t('Fabric Width (cm)', 'عرض القماش (سم)')" placeholder="150" />
                    <BaseInput v-model="adminForm.dye_method" :label="t('Dye Method', 'طريقة الصباغة')" :placeholder="t('Reactive, Vat, Pigment...', 'تفاعلية، حوض، بجمنت...')" />
                    <BaseInput v-model="adminForm.fabric_finish" :label="t('Fabric Finish', 'تشطيب القماش')" :placeholder="t('Peach, Brushed, Bio-wash...', 'خوخي، مشطوف، بيو ووش...')" />
                  </div>
                </div>
              </BaseCard>

              <!-- Trims (Collapsible) -->
              <BaseCard class="info-card admin-spec-card collapsible-card" :class="{ 'collapsed': collapsed.trims }">
                <template #header>
                  <div class="collapsible-header" @click="collapsed.trims = !collapsed.trims">
                    <span class="collapsible-title">{{ t('Trims & Accessories', 'الإضافات والإكسسوارات') }}</span>
                    <svg class="chevron" :class="{ 'open': !collapsed.trims }" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                  </div>
                </template>
                <div class="collapsible-body">
                  <div class="admin-form-grid">
                    <BaseInput v-model="adminForm.zipper_type" :label="t('Zipper Type', 'نوع السحاب')" :placeholder="t('YKK #5 Metal', 'YKK #5 معدني')" />
                    <BaseInput v-model="adminForm.button_type" :label="t('Button Type', 'نوع الزراير')" :placeholder="t('4-hole, Snap, Shank...', '4 ثقوب، كبسولة...')" />
                    <BaseInput v-model="adminForm.cord_type" :label="t('Cord Type', 'نوع الحبل')" :placeholder="t('Round, Flat...', 'دائري، مسطح...')" />
                    <BaseInput v-model="adminForm.rib_type" :label="t('Rib Type', 'نوع الريب')" :placeholder="t('1x1, 2x2...', '1×1، 2×2...')" />
                    <BaseInput v-model="adminForm.thread_type" :label="t('Thread Type', 'نوع الخيط')" :placeholder="t('Polyester 40/2', 'بوليستر 40/2')" />
                  </div>
                </div>
              </BaseCard>

              <!-- Stitch & Construction (Collapsible) -->
              <BaseCard class="info-card admin-spec-card collapsible-card" :class="{ 'collapsed': collapsed.stitch }">
                <template #header>
                  <div class="collapsible-header" @click="collapsed.stitch = !collapsed.stitch">
                    <span class="collapsible-title">{{ t('Stitch & Construction', 'الخياطة والتصنيع') }}</span>
                    <svg class="chevron" :class="{ 'open': !collapsed.stitch }" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                  </div>
                </template>
                <div class="collapsible-body">
                  <div class="admin-form-grid">
                    <BaseInput v-model="adminForm.stitch_type" :label="t('Stitch Type', 'نوع الغرزة')" :placeholder="t('Lock stitch, Chain stitch...', 'غرزة قفل، غرزة سلسلة...')" />
                    <BaseInput v-model="adminForm.seam_type" :label="t('Seam Type', 'نوع الدرزة')" :placeholder="t('Flat, French, Overlock...', 'مسطحة، فرنسية، أوفرلوك...')" />
                    <BaseInput v-model="adminForm.top_stitch" :label="t('Top Stitch', 'الغرزة العلوية')" :placeholder="t('1/4 inch, Double needle...', '¼ بوصة، إبرة مزدوجة...')" />
                    <BaseInput v-model="adminForm.reinforcement" :label="t('Reinforcement', 'التعزيز')" :placeholder="t('Bar tack, Binding...', 'بار تاك، تطعيم...')" />
                  </div>
                </div>
              </BaseCard>

              <!-- Label Production (Collapsible) -->
              <BaseCard class="info-card admin-spec-card collapsible-card" :class="{ 'collapsed': collapsed.labels }">
                <template #header>
                  <div class="collapsible-header" @click="collapsed.labels = !collapsed.labels">
                    <span class="collapsible-title">{{ t('Label Production', 'إنتاج الليبلات') }}</span>
                    <svg class="chevron" :class="{ 'open': !collapsed.labels }" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                  </div>
                </template>
                <div class="collapsible-body">
                  <div class="admin-form-grid">
                    <BaseInput v-model="adminForm.main_label_type" :label="t('Main Label Type', 'نوع الليبل الرئيسي')" :placeholder="t('Woven, Printed, Silicon...', 'منسوج، مطبوع، سيليكون...')" />
                    <BaseInput v-model="adminForm.size_label_type" :label="t('Size Label Type', 'نوع ليبل المقاس')" placeholder="Woven" />
                    <BaseInput v-model="adminForm.care_label_type" :label="t('Care Label Type', 'نوع ليبل العناية')" :placeholder="t('Satin, Taffeta...', 'ساتان، تافتا...')" />
                    <BaseInput v-model="adminForm.label_position" :label="t('Label Position', 'موضع الليبل')" :placeholder="t('Center back neck, Side seam...', 'وسط الرقبة الخلفية، الدرزة الجانبية...')" />
                  </div>
                </div>
              </BaseCard>

              <!-- Packaging Details (Collapsible) -->
              <BaseCard class="info-card admin-spec-card collapsible-card" :class="{ 'collapsed': collapsed.packaging }">
                <template #header>
                  <div class="collapsible-header" @click="collapsed.packaging = !collapsed.packaging">
                    <span class="collapsible-title">{{ t('Packaging Details', 'تفاصيل التغليف') }}</span>
                    <svg class="chevron" :class="{ 'open': !collapsed.packaging }" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                  </div>
                </template>
                <div class="collapsible-body">
                  <div class="admin-form-grid">
                    <BaseInput v-model="adminForm.packaging_type" :label="t('Packaging Type', 'نوع التغليف')" :placeholder="t('Polybag, Box, Hanger...', 'كيس بلاستيك، صندوق، شماعة...')" />
                    <BaseInput v-model="adminForm.folding_method" :label="t('Folding Method', 'طريقة الطي')" :placeholder="t('Standard fold, Roll...', 'طي قياسي، لف...')" />
                    <BaseInput v-model="adminForm.carton_qty" type="number" :label="t('Carton Quantity', 'كمية الكرتون')" placeholder="12" />
                    <div class="checkbox-field">
                      <label class="input-label">{{ t('Barcode Required', 'باركود مطلوب') }}</label>
                      <div class="check-row">
                        <input type="checkbox" v-model="adminForm.barcode_required" id="barcode-check" />
                        <label for="barcode-check">{{ t('Yes, barcode is required', 'نعم، الباركود مطلوب') }}</label>
                      </div>
                    </div>
                  </div>
                </div>
              </BaseCard>

              <!-- Internal Factory Notes -->
              <BaseCard class="info-card admin-spec-card" :title="t('Internal Factory Notes', 'ملاحظات المصنع الداخلية')">
                <textarea 
                  v-model="adminForm.factory_notes" 
                  class="factory-notes-textarea" 
                  :placeholder="t('Internal instructions for the factory...', 'تعليمات داخلية للمصنع...')"
                  rows="5"
                ></textarea>
              </BaseCard>

              <!-- Save Button -->
              <div class="admin-save-footer mt-1 mb-2">
                <BaseButton size="lg" variant="primary" @click="saveProductionDetails" :loading="savingAdmin" block>
                  <template #icon-left>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                  </template>
                  {{ t('Save Production Details', 'حفظ تفاصيل الإنتاج') }}
                </BaseButton>
              </div>
            </div>


            <BaseCard v-if="order.images && order.images.length" class="info-card read-only-card" :title="t('Design Visuals', 'صور التصميم')">
              <div class="gallery-grid">
                 <div v-for="img in order.images" :key="img.id" class="gallery-img-box" @click="zoomImage(img.file_path)">
                    <img :src="'/storage/' + img.file_path" alt="design">
                    <div class="img-overlay">
                       <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                    </div>
                 </div>
              </div>
            </BaseCard>
          </div>










          <!-- Notes -->
          <BaseCard class="info-card" :title="t('Additional Notes', 'ملاحظات إضافية')">
             <div class="notes-content">
                {{ order.notes || t('No specific notes provided.', 'لا توجد ملاحظات محددة.') }}
             </div>
          </BaseCard>

          <!-- Order Activity Timeline -->
          <BaseCard class="info-card activity-card" :title="t('Order Activity', 'سجل النشاط')">
            <div v-if="order.status_history?.length" class="timeline">
              <div v-for="log in order.status_history" :key="log.id" class="timeline-item">
                <div class="timeline-dot-wrapper">
                  <div class="timeline-dot" :class="log.new_status"></div>
                  <div class="timeline-line"></div>
                </div>
                <div class="timeline-content">
                  <div class="timeline-header">
                    <span class="changer-name">{{ log.changer?.name }}</span>
                    <span class="action-time">{{ formatFullDate(log.created_at) }}</span>
                  </div>
                  <div class="action-desc">
                    {{ t('Moved order from', 'قام بنقل الطلب من') }} 
                    <span class="status-badge-small" :class="log.old_status">{{ t(statusMap[log.old_status]?.en || 'Pending', statusMap[log.old_status]?.ar || 'قيد الانتظار') }}</span>
                    {{ t('to', 'إلى') }}
                    <span class="status-badge-small" :class="log.new_status">{{ t(statusMap[log.new_status]?.en, statusMap[log.new_status]?.ar) }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="empty-timeline">
              <p>{{ t('No status changes recorded yet.', 'لم يتم تسجيل أي تغييرات في الحالة بعد.') }}</p>
            </div>
          </BaseCard>
        </div>

        <aside v-if="activeTab === 'chat' || !isMobile" class="detail-sidebar">
          <!-- Chat Section -->
          <BaseCard class="chat-card">
             <template #header>
                <div class="chat-header">
                   <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                   <span>{{ t('Direct Messaging', 'المحادثة المباشرة') }}</span>
                </div>
             </template>
             
             <div class="message-container" ref="messageBox">
                <div v-for="msg in messages" :key="msg.id" :class="['message-blob', msg.sender_id === currentUser.id ? 'mine' : 'theirs']">
                   <div class="blob-content">
                      <div class="blob-sender" v-if="msg.sender_id !== currentUser.id">{{ msg.sender?.name }}</div>
                      <div class="blob-text">{{ msg.content }}</div>
                      <div class="blob-time">{{ formatTime(msg.created_at) }}</div>
                   </div>
                </div>
                <div v-if="messages.length === 0" class="empty-chat">
                   <p>{{ t('Start a conversation with the production team.', 'ابدأ المحادثة مع فريق الإنتاج.') }}</p>
                </div>
             </div>

             <template #footer>
                <div class="chat-input">
                   <textarea v-model="newMessage" :placeholder="t('Type message...', 'اكتب رسالة...')" @keyup.enter.prevent="sendMessage"></textarea>
                   <BaseButton @click="sendMessage" :disabled="sending || !newMessage.trim()" class="send-btn-icon">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
                   </BaseButton>
                </div>
             </template>
          </BaseCard>
          
          <!-- Quick Status Actions (Admin Only) -->
          <BaseCard v-if="['admin', 'superadmin', 'manager'].includes(currentUser?.role)" :title="t('Admin Controls', 'تحكم المسؤول')">
            <div class="admin-actions">
                <BaseButton v-if="order.status === 'pending'" variant="secondary" block size="sm" @click="updateOrderStatus('in_review')" :loading="updating && statusTarget === 'in_review'">
                   {{ t('Move to Review', 'نقل للمراجعة') }}
                </BaseButton>
                <BaseButton v-if="order.status === 'in_review'" variant="secondary" block size="sm" @click="updateOrderStatus('sampling')" :loading="updating && statusTarget === 'sampling'">
                   {{ t('Approve for Sampling', 'الموافقة على العينات') }}
                </BaseButton>
                <BaseButton v-if="order.status === 'sampling'" variant="secondary" block size="sm" @click="updateOrderStatus('approved')" :loading="updating && statusTarget === 'approved'">
                   {{ t('Approve Sample', 'الموافقة على العينة') }}
                </BaseButton>
                <BaseButton v-if="order.status === 'approved'" variant="secondary" block size="sm" @click="updateOrderStatus('production')" :loading="updating && statusTarget === 'production'">
                   {{ t('Start Production', 'بدء التصنيع') }}
                </BaseButton>
                <BaseButton v-if="order.status === 'production'" variant="secondary" block size="sm" @click="updateOrderStatus('completed')" :loading="updating && statusTarget === 'completed'">
                   {{ t('Mark as Completed', 'تم الإكمال') }}
                </BaseButton>
                <BaseButton v-if="order.status !== 'completed' && order.status !== 'cancelled'" variant="ghost" block size="sm" class="mt-1" @click="updateOrderStatus('cancelled')" :loading="updating && statusTarget === 'cancelled'">
                   {{ t('Cancel Order', 'إلغاء الطلب') }}
                </BaseButton>
             </div>
          </BaseCard>
        </aside>
      </div>
    </div>

    <!-- Status Change Modal -->
    <div v-if="showStatusModal" class="modal-overlay" @click="showStatusModal = false">
       <div class="modal-card" @click.stop>
          <h3 class="modal-title-custom">{{ t('Update Order Status', 'تحديث حالة الطلب') }}</h3>
          <div class="status-options">
             <button v-for="(val, key) in statusMap" :key="key" 
                     @click="updateOrderStatus(key)"
                     :class="['status-opt', key, { active: order.status === key }]"
             >
                {{ t(val.en, val.ar) }}
             </button>
          </div>
       </div>
    </div>
    <!-- Marker Update Modal -->
    <div v-if="showMarkerModal" class="modal-overlay" @click="showMarkerModal = false">
       <div class="modal-card" @click.stop>
          <h3 class="modal-title-custom">{{ t('Update Marker Planning', 'تحديث تخطيط الماركر') }}</h3>
          <div class="form-grid-2">
             <BaseInput v-model="markerForm.marker_length" type="number" step="0.01" :label="t('Marker Length (m)', 'طول الماركر (متر)')" />
             <BaseInput v-model="markerForm.fabric_width" type="number" step="0.01" :label="t('Fabric Width (m)', 'عرض القماش (متر)')" />
             <BaseInput v-model="markerForm.efficiency" type="number" step="0.1" :label="t('Efficiency (%)', 'الكفاءة (%)')" />
             <BaseInput v-model="markerForm.part_count" type="number" :label="t('Number of Parts', 'عدد الأجزاء')" />
          </div>
          <div class="mt-2 flex gap-1">
             <BaseButton block @click="saveMarker" :loading="savingMarker">{{ t('Save Changes', 'حفظ التغييرات') }}</BaseButton>
             <BaseButton block variant="white" @click="showMarkerModal = false">{{ t('Cancel', 'إلغاء') }}</BaseButton>
          </div>
       </div>
    </div>

    <!-- Technical Specs Modal -->
    <div v-if="showTechModal" class="modal-overlay" @click="showTechModal = false">
       <div class="modal-card tech-modal" @click.stop>
          <h3 class="modal-title-custom">{{ t('Finalize Production Tech Specs', 'إكمال المواصفات الفنية للإنتاج') }}</h3>
          <div class="tech-modal-body">
             <h5 class="section-divider-title">{{ t('Fabric Details', 'تفاصيل القماش') }}</h5>
             <div class="form-grid-2 mb-2">
                <BaseInput v-model="techForm.fabric_details.type" :label="t('Fabric Type', 'نوع القماش')" />
                <BaseInput v-model="techForm.fabric_details.weight" type="number" :label="t('GSM (Weight)', 'وزن القماش')" />
                <BaseInput v-model="techForm.fabric_details.composition" :label="t('Composition', 'التكوين')" />
                <BaseInput v-model="techForm.fabric_details.code" :label="t('Fabric Code', 'كود القماش')" />
             </div>

             <h5 class="section-divider-title">{{ t('Trims & Technical Details', 'الإضافات والتفاصيل الفنية') }}</h5>
             <div class="form-grid-2 mb-2">
                <BaseInput v-model="techForm.zipper_type" :label="t('Zipper Type', 'نوع السحاب')" />
                <BaseInput v-model="techForm.button_type" :label="t('Button Type', 'نوع الزراير')" />
                <BaseInput v-model="techForm.rib_type" :label="t('Rib Type', 'نوع الريب')" />
                <BaseInput v-model="techForm.stitching_type" :label="t('Stitching Type', 'نوع الخياطة')" />
             </div>

             <h5 class="section-divider-title">{{ t('Branding & Packaging', 'البراندنج والتغليف') }}</h5>
             <div class="form-grid-2">
                <BaseInput v-model="techForm.main_label_type" :label="t('Main Label', 'الليبل الرئيسي')" />
                <BaseInput v-model="techForm.size_label_type" :label="t('Size Label', 'ليبل المقاس')" />
                <BaseInput v-model="techForm.packaging_type" :label="t('Packaging Type', 'نوع التغليف')" />
                <BaseInput v-model="techForm.folding_method" :label="t('Folding Method', 'طريقة الطي')" />
             </div>
          </div>
          <div class="mt-2 flex gap-1">
             <BaseButton block @click="saveTechSpecs" :loading="savingTech">{{ t('Save & Set Ready', 'حفظ واعتماد الجاهزية') }}</BaseButton>
             <BaseButton block variant="white" @click="showTechModal = false">{{ t('Close', 'إغلاق') }}</BaseButton>
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

import { ref, reactive, onMounted, nextTick, computed, watch, onBeforeUnmount } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { hasPermission } from '../../utils/permissions';

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
const statusTarget = ref('');
const activeTab = ref('production'); // 'production', 'client', 'chat'
const isMobile = ref(false);

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
const savingAdmin = ref(false);

const collapsed = reactive({
  fabric: false,
  trims: true,
  stitch: true,
  labels: true,
  marker: false,
  packaging: true,
});

const scrollToAdminSection = () => {
  if (isMobile.value) {
    activeTab.value = 'production';
  }
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
      const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
      if (activeMarker.value) {
         await axios.put(`/api/marker-plans/${activeMarker.value.id}`, markerForm, { headers });
      } else {
         await axios.post('/api/marker-plans', { ...markerForm, order_id: order.value.id }, { headers });
      }
      await fetchData();
      showMarkerModal.value = false;
   } catch(e) { alert('Error saving marker'); }
   finally { savingMarker.value = false; }
};

const isRtl = computed(() => localStorage.getItem('lang') === 'ar');
const t = (en, ar) => isRtl.value ? ar : en;

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
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
    order.value.status = newStatus;
    showStatusModal.value = false;
  } catch (e) {
    alert(t('Update failed', 'فشل التحديث'));
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
  savingAdmin.value = true;
  try {
    const headers = { Authorization: `Bearer ${localStorage.getItem('auth_token')}` };
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
    await axios.post(`/api/orders/${order.value.id}/production`, payload, { headers });
    await fetchData();
    alert(t('Production details saved successfully!', 'تم حفظ تفاصيل الإنتاج بنجاح!'));
  } catch (e) {
    console.error('Save error:', e.response?.data || e);
    alert(t('Failed to save production details.', 'فشل حفظ تفاصيل الإنتاج.'));
  } finally {
    savingAdmin.value = false;
  }
};

const fetchData = async () => {
  if (!can('orders.view')) return router.push('/dashboard');
  const token = localStorage.getItem('auth_token');
  const headers = { Authorization: `Bearer ${token}` };
  try {
    const resOrder = await axios.get(`/api/orders/${route.params.id}`, { headers });
    order.value = resOrder.data;
    populateAdminForm(resOrder.data);
    const resMessages = await axios.get(`/api/orders/${route.params.id}/messages`, { headers });
    messages.value = resMessages.data;
    scrollToBottom();
  } catch (e) {}
};

onMounted(fetchData);

const sendMessage = async () => {
  if (!order.value || !currentUser.value) return;
  if (!newMessage.value.trim() || sending.value) return;
  sending.value = true;
  try {
    const receiverId = order.value.created_by === currentUser.value.id ? 1 : order.value.created_by;
    await axios.post('/api/messages', {
      order_id: order.value.id,
      receiver_id: receiverId,
      content: newMessage.value
    }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
    });
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
const printTechPack = () => {
   window.print();
};
</script>

<style scoped>
/* UI/UX & Mobile Optimizations */
.mobile-tabs-nav {
  display: none;
  background: white;
  padding: 0.5rem;
  border-radius: 14px;
  margin-bottom: 1.5rem;
  border: 1px solid #f1f5f9;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
  position: sticky;
  top: 0.5rem;
  z-index: 100;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}
.mobile-tabs-nav button {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 0.75rem 0.25rem;
  border: none;
  background: transparent;
  color: #94a3b8;
  font-size: 0.7rem;
  font-weight: 800;
  border-radius: 10px;
  transition: all 0.2s;
  cursor: pointer;
}
.mobile-tabs-nav button svg { width: 18px; height: 18px; }
.mobile-tabs-nav button.active {
  background: #f0f9ff;
  color: #0ea5e9;
}

@media (max-width: 768px) {
  .mobile-tabs-nav { display: grid; }
  .preview-flex { flex-direction: column; align-items: stretch; gap: 1.5rem; text-align: center; }
  .preview-img { margin: 0 auto; width: 100px; height: 100px; }
  .preview-meta-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; width: 100%; text-align: left; }
  .rtl .preview-meta-grid { text-align: right; }
  .preview-actions { width: 100%; }
  .workflow-title { font-size: 0.75rem; }
  .detail-main { padding-bottom: 8rem; }
  
  /* Fixed Footer for Save Button */
  .admin-save-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    padding: 1rem;
    box-shadow: 0 -10px 15px -3px rgba(0,0,0,0.1);
    z-index: 1000;
    margin-top: 0;
  }
  
  /* Make collapsed headers bigger for thumbs */
  .collapsible-header { padding: 1.25rem; }
  .spec-row { grid-template-columns: 1fr 1fr; }
  .m-box { min-width: 80px; flex: 1; }
}

.order-detail-view {
  max-width: 1400px;
  margin: 0 auto;
  padding-bottom: 5rem;
}

.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; }
.header-left { flex: 1; }
.breadcrumb-mini { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 0.5rem; }
.breadcrumb-mini a { color: inherit; text-decoration: none; }
.breadcrumb-mini .sep { opacity: 0.5; }
.breadcrumb-mini .active { color: #0ea5e9; }

.page-title { font-size: 2rem; font-weight: 800; color: #1a3a5f; margin: 0; letter-spacing: -0.02em; }
.meta-row { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }
.meta-item { font-size: 0.875rem; color: #64748b; font-weight: 600; }
.clickable-brand { cursor: pointer; color: #0ea5e9; font-weight: 800; }
.clickable-brand:hover { text-decoration: underline; }
.dot { width: 4px; height: 4px; border-radius: 50%; background: #e2e8f0; }

.header-actions { display: flex; align-items: center; gap: 1rem; }
/* 1. Product Preview Enhancement */
.product-preview-card {
  margin-bottom: 2rem;
  border: 1px solid #eef2f6;
  background: white;
}
.preview-flex {
  display: flex;
  gap: 2rem;
  align-items: center;
}
.preview-img {
  width: 120px;
  height: 120px;
  border-radius: 12px;
  overflow: hidden;
  border: 4px solid #f8fafc;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  flex-shrink: 0;
}
.preview-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.preview-info {
  flex: 1;
}
.preview-title {
  font-size: 1.5rem;
  font-weight: 800;
  color: #1a3a5f;
  margin: 0 0 0.75rem 0;
  letter-spacing: -0.01em;
}
.preview-meta-grid {
  display: grid;
  grid-template-columns: repeat(4, auto);
  gap: 2rem;
  margin-bottom: 1rem;
}
.p-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.p-label {
  font-size: 0.65rem;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.p-val {
  font-size: 0.875rem;
  font-weight: 700;
  color: #1e293b;
}
.preview-status-row {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.preview-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  min-width: 200px;
}

/* 2. Workflow Structure */
.workflow-section {
  margin-bottom: 3rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.workflow-title {
  font-size: 0.875rem;
  font-weight: 900;
  color: #64748b;
  letter-spacing: 0.15em;
  padding: 0 0.5rem;
  border-left: 4px solid #3b82f6;
  margin-bottom: 0.5rem;
}
.rtl .workflow-title {
  border-left: none;
  border-right: 4px solid #3b82f6;
}

/* 3. Collapsible Logic */
.collapsible-card {
  transition: all 0.3s ease;
  overflow: hidden;
}
.collapsible-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  cursor: pointer;
  background: #fcfdfe;
  user-select: none;
}
.collapsible-header:hover {
  background: #f8fafc;
}
.collapsible-title {
  font-weight: 800;
  color: #1a3a5f;
  font-size: 0.9375rem;
}
.chevron {
  transition: transform 0.3s ease;
  color: #94a3b8;
}
.chevron.open {
  transform: rotate(180deg);
}
.collapsible-body {
  padding: 1.5rem;
  border-top: 1px solid #f1f5f9;
}
.collapsed .collapsible-body {
  display: none;
}

.read-only-card {
  background: #fafbfc;
  border: 1px dashed #e2e8f0;
}

.status-pill-large { padding: 0.5rem 1.25rem; border-radius: 10px; font-size: 0.8125rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid transparent; }

.status-pill-large.pending { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }
.status-pill-large.in_review { background: #eff6ff; color: #1e40af; border-color: #dbeafe; }
.status-pill-large.sampling { background: #f5f3ff; color: #6d28d9; border-color: #ede9fe; }
.status-pill-large.approved { background: #f0fdf4; color: #15803d; border-color: #dcfce7; }
.status-pill-large.production { background: #fff7ed; color: #c2410c; border-color: #ffedd5; }
.status-pill-large.completed { background: #ecfdf5; color: #064e3b; border-color: #d1fae5; }
.status-pill-large.cancelled { background: #fef2f2; color: #991b1b; border-color: #fee2e2; }

.detail-grid { display: grid; grid-template-columns: 1fr 380px; gap: 2rem; }
@media (max-width: 1024px) { .detail-grid { grid-template-columns: 1fr; } }

.info-card { margin-bottom: 2rem; }
.spec-list { padding-top: 0.5rem; }
.spec-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
.spec-item { display: flex; flex-direction: column; gap: 0.35rem; }
.spec-label { font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
.spec-value { font-size: 0.9375rem; font-weight: 700; color: #1a3a5f; }
.divider { height: 1px; background: #f1f5f9; margin: 1.25rem 0; }

.measurements-flex { display: flex; flex-wrap: wrap; gap: 0.75rem; }
.m-box { background: #f8fafc; border: 1px solid #f1f5f9; padding: 0.875rem 1rem; border-radius: 12px; text-align: center; min-width: 100px; }
.m-label { display: block; font-size: 0.65rem; font-weight: 700; color: #94a3b8; margin-bottom: 4px; }
.m-val { font-size: 1.125rem; font-weight: 800; color: #1a3a5f; }

.colors-flex { display: flex; flex-wrap: wrap; gap: 1rem; }
.color-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem; background: #fcfdfe; border: 1px solid #f1f5f9; border-radius: 12px; min-width: 160px; }
.color-circle { width: 44px; height: 44px; border-radius: 10px; border: 2px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.color-meta .hex { font-size: 0.8125rem; font-weight: 800; color: #1a3a5f; }
.color-meta .pantone { font-size: 0.65rem; font-weight: 600; color: #94a3b8; }

.gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.25rem; }
.gallery-img-box { position: relative; aspect-ratio: 1; border-radius: 14px; overflow: hidden; border: 1px solid #f1f5f9; cursor: pointer; }
.gallery-img-box img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
.img-overlay { position: absolute; inset: 0; background: rgba(14, 165, 233, 0.2); display: flex; align-items: center; justify-content: center; opacity: 0; color: white; transition: all 0.2s; }
.gallery-img-box:hover img { transform: scale(1.1); }
.gallery-img-box:hover .img-overlay { opacity: 1; }

.notes-content { padding: 1.25rem; background: #f8fafc; border-radius: 12px; color: #475569; line-height: 1.6; font-size: 0.9375rem; white-space: pre-wrap; border: 1px solid #f1f5f9; }

.marker-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem; }
.m-stat { background: #fafbfc; padding: 1rem; border-radius: 12px; border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 4px; }
.m-value { font-size: 1.125rem; font-weight: 800; color: #1a3a5f; }

.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.p-1-5 { padding: 1.5rem; }
.mt-1 { margin-top: 1rem; }
.mt-2 { margin-top: 2rem; }
.flex { display: flex; }
.gap-1 { gap: 1rem; }

/* Chat Section */
.chat-card { height: 600px; display: flex; flex-direction: column; position: sticky; top: 2rem; }
.chat-header { display: flex; align-items: center; gap: 0.625rem; font-weight: 800; color: #1a3a5f; font-size: 0.9375rem; }
.message-container { flex: 1; overflow-y: auto; padding: 1.25rem; background: #fafbfc; display: flex; flex-direction: column; gap: 1rem; }
.message-blob { max-width: 85%; }
.message-blob.mine { align-self: flex-end; }
.message-blob.theirs { align-self: flex-start; }
.blob-content { padding: 0.875rem 1rem; border-radius: 14px; position: relative; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.mine .blob-content { background: #1a3a5f; color: white; border-bottom-right-radius: 4px; }
.theirs .blob-content { background: white; color: #1e293b; border: 1px solid #eef2f6; border-bottom-left-radius: 4px; }
.blob-sender { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; opacity: 0.8; }
.blob-text { font-size: 0.875rem; line-height: 1.5; white-space: pre-wrap; }
.blob-time { font-size: 0.625rem; opacity: 0.5; text-align: right; margin-top: 4px; }

.chat-input { display: flex; gap: 0.75rem; padding: 1.25rem; border-top: 1px solid #f1f5f9; }
.chat-input textarea { flex: 1; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.75rem 1rem; height: 44px; resize: none; font-size: 0.875rem; transition: border-color 0.2s; }
.chat-input textarea:focus { border-color: #0ea5e9; outline: none; }
.send-btn-icon { width: 44px; height: 44px; padding: 0 !important; flex-shrink: 0; }

.admin-actions { display: grid; gap: 0.75rem; }

/* Modal Styles */
.modal-overlay { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 200; }
.modal-card { background: white; width: 420px; border-radius: 20px; padding: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
.modal-title-custom { color: #1a3a5f; margin: 0 0 1.5rem; font-weight: 800; }
.status-options { display: flex; flex-direction: column; gap: 0.75rem; }
.status-opt { padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; background: white; font-weight: 700; color: #475569; text-align: left; transition: all 0.2s; cursor: pointer; display: flex; align-items: center; justify-content: space-between; }
.status-opt:hover { background: #f8fafc; border-color: #0ea5e9; }
.status-opt.active { border-color: #0ea5e9; background: #f0f9ff; color: #0ea5e9; }

.status-opt.submitted::after { background: #64748b; }
.status-opt.pending::after { background: #94a3b8; }
.status-opt.technical_ready::after { background: #0ea5e9; }
.status-opt.in_review::after { background: #3b82f6; }
.status-opt.sampling::after { background: #8b5cf6; }
.status-opt.approved::after { background: #10b981; }
.status-opt.production::after { background: #f97316; }
.status-opt.completed::after { background: #064e3b; }
.status-opt.cancelled::after { background: #ef4444; }

.tech-modal { width: 600px; }
.tech-modal-body { max-height: 60vh; overflow-y: auto; padding-right: 10px; }
.section-divider-title { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin: 1.5rem 0 1rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.5rem; }
.mb-2 { margin-bottom: 2rem; }

.rtl .page-header, .rtl .status-opt { text-align: right; }
.rtl .status-pill-large { margin-right: 0; }
.rtl .header-actions { flex-direction: row-reverse; }
.rtl .status-opt::after { margin-right: auto; margin-left: 0; }

/* Timeline Styles */
.activity-card { margin-top: 1rem; }
.timeline { display: flex; flex-direction: column; padding: 0.5rem 0; }
.timeline-item { display: flex; gap: 1.5rem; }
.timeline-dot-wrapper { display: flex; flex-direction: column; align-items: center; width: 24px; }
.timeline-dot { width: 12px; height: 12px; border-radius: 50%; background: #e2e8f0; border: 2px solid white; box-shadow: 0 0 0 4px #f8fafc; z-index: 2; margin-top: 6px; }
.timeline-line { flex: 1; width: 2px; background: #f1f5f9; margin-top: -4px; z-index: 1; }
.timeline-item:last-child .timeline-line { display: none; }

.timeline-content { flex: 1; padding-bottom: 2rem; }
.timeline-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.changer-name { font-size: 0.875rem; font-weight: 800; color: #1e293b; }
.action-time { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
.action-desc { font-size: 0.8125rem; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.status-badge-small { padding: 2px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }

/* Status-specific timeline dots */
.timeline-dot.pending { background: #94a3b8; }
.timeline-dot.in_review { background: #3b82f6; }
.timeline-dot.sampling { background: #8b5cf6; }
.timeline-dot.approved { background: #10b981; }
.timeline-dot.production { background: #f97316; }
.timeline-dot.completed { background: #064e3b; }
.timeline-dot.cancelled { background: #ef4444; }

/* status badge small colors */
.status-badge-small.pending { background: #f1f5f9; color: #475569; }
.status-badge-small.in_review { background: #eff6ff; color: #1e40af; }
.status-badge-small.sampling { background: #f5f3ff; color: #6d28d9; }
.status-badge-small.approved { background: #f0fdf4; color: #15803d; }
.status-badge-small.production { background: #fff7ed; color: #c2410c; }
.status-badge-small.completed { background: #ecfdf5; color: #064e3b; }
.status-badge-small.cancelled { background: #fef2f2; color: #991b1b; }

.empty-timeline { padding: 2rem; text-align: center; color: #94a3b8; font-weight: 600; }

/* ===== Admin Production Spec Styles ===== */
.admin-section-divider {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  margin: 2.5rem 0 2rem;
}
.divider-line {
  flex: 1;
  height: 2px;
  background: linear-gradient(90deg, transparent, #0ea5e9, transparent);
}
.divider-label {
  font-size: 0.75rem;
  font-weight: 800;
  color: #0ea5e9;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  white-space: nowrap;
}

.admin-section-subtitle {
  font-size: 0.875rem;
  color: #64748b;
  margin: -1rem 0 2.5rem;
  font-weight: 600;
  text-align: center;
}

.admin-production-wrapper {
  scroll-margin-top: 2rem;
}

.admin-spec-card {
  border-left: 3px solid #0ea5e9;
}

.admin-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.checkbox-field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.check-row {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  margin-top: 0.5rem;
}
.check-row input[type="checkbox"] {
  width: 1.2rem;
  height: 1.2rem;
  accent-color: #0ea5e9;
  cursor: pointer;
}
.check-row label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
}

.factory-notes-textarea {
  width: 100%;
  min-height: 120px;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: #fcfdfe;
  color: #1e293b;
  font-size: 0.9375rem;
  font-family: inherit;
  resize: vertical;
  transition: all 0.2s;
  line-height: 1.6;
}
.factory-notes-textarea:focus {
  border-color: #0ea5e9;
  outline: none;
  background: white;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.05);
}

.admin-save-footer {
  margin: 1.5rem 0 3rem;
}

@media (max-width: 768px) {
  .admin-form-grid {
    grid-template-columns: 1fr;
  }
  .header-actions {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  .page-header {
    flex-direction: column;
    gap: 1rem;
  }
  .spec-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
</style>

