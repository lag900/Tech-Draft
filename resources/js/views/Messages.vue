<template>
  <Layout>
    <div class="messages-view" :class="{ 'rtl': isRtl }">
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('Communication Center', 'مركز التواصل') }}</h1>
          <p class="page-subtitle">{{ t('Manage your production chats with the factory team', 'أدر محادثات الإنتاج مع فريق المصنع') }}</p>
        </div>
      </div>

      <div class="chat-layout">
        <BaseCard class="orders-list-card" no-padding>
           <div class="list-header">
              <span>{{ t('Active Conversations', 'المحادثات النشطة') }}</span>
           </div>
           <div class="orders-scroll">
              <div 
                v-for="order in orders" :key="order.id" 
                class="order-chat-item" 
                :class="{ active: selectedOrderId === order.id }"
                @click="selectOrder(order.id)"
              >
                 <div class="order-avatar">
                   {{ order.order_code.slice(-2) }}
                 </div>
                 <div class="order-chat-info">
                    <div class="top-row">
                       <span class="order-code">#{{ order.order_code }}</span>
                       <span class="chat-time" v-if="order.last_message">{{ formatTimeShort(order.last_message.created_at) }}</span>
                    </div>
                    <div class="order-title-mini">{{ order.title || order.category?.name }}</div>
                    <div class="last-msg-preview" v-if="order.last_message">{{ order.last_message.content }}</div>
                 </div>
              </div>
              <div v-if="orders.length === 0" class="empty-list">
                 {{ t('No active orders found.', 'لا توجد طلبات نشطة.') }}
              </div>
           </div>
        </BaseCard>

        <BaseCard class="chat-area-card" no-padding>
           <template v-if="selectedOrderId">
              <div class="chat-header-main" @click="$router.push('/orders/' + selectedOrderId)">
                 <div class="header-info">
                    <span class="header-code">#{{ selectedOrder?.order_code }}</span>
                    <span class="header-title">{{ selectedOrder?.title || selectedOrder?.category?.name }}</span>
                 </div>
                 <BaseButton variant="ghost" size="sm">{{ t('View Tech Pack', 'عرض الملف الفني') }}</BaseButton>
              </div>

              <div class="message-pool" ref="pool">
                 <div v-for="msg in messages" :key="msg.id" :class="['msg-bubble', msg.sender_id === user.id ? 'mine' : 'theirs']">
                    <div class="bubble-content">
                       <div class="sender-name" v-if="msg.sender_id !== user.id">{{ msg.sender?.name || 'Factory' }}</div>
                       <div class="msg-text">{{ msg.content }}</div>
                       <div class="msg-time">{{ formatTime(msg.created_at) }}</div>
                    </div>
                 </div>
                 <div v-if="messages.length === 0" class="empty-chat-v">
                    <p>{{ t('Send a message to start the conversation for this order.', 'أرسل رسالة لبدء المحادثة لهذا الطلب.') }}</p>
                 </div>
              </div>

              <div class="chat-footer-main">
                 <textarea v-model="newMessage" :placeholder="t('Type your message...', 'اكتب رسالتك...')" @keyup.enter.prevent="sendMessage"></textarea>
                 <BaseButton @click="sendMessage" :disabled="!newMessage.trim() || sending" :loading="sending">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
                 </BaseButton>
              </div>
           </template>
           <template v-else>
              <div class="no-selection">
                 <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                 <p>{{ t('Select an order to start messaging', 'اختر طلباً لبدء المحادثة') }}</p>
              </div>
           </template>
        </BaseCard>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import Layout from '../components/Layout.vue';
import BaseCard from '../components/UI/BaseCard.vue';
import BaseButton from '../components/UI/BaseButton.vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { hasPermission } from '../utils/permissions';

const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('user') || 'null'));
const can = (perm) => hasPermission(user.value, perm);
const isRtl = computed(() => localStorage.getItem('lang') === 'ar');
const t = (en, ar) => isRtl.value ? ar : en;

const orders = ref([]);
const selectedOrderId = ref(null);
const messages = ref([]);
const newMessage = ref('');
const sending = ref(false);
const pool = ref(null);

const selectedOrder = computed(() => orders.value.find(o => o.id === selectedOrderId.value));

onMounted(async () => {
   if (!can('orders.view')) return router.push('/dashboard');
   await fetchOrders();
});

const fetchOrders = async () => {
   try {
      const res = await axios.get('/api/orders', {
         headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      orders.value = res.data;
      // Pre-select first if none selected
      if (orders.value.length && !selectedOrderId.value) {
         // selectOrder(orders.value[0].id);
      }
   } catch (e) {}
};

const selectOrder = async (id) => {
   selectedOrderId.value = id;
   await fetchMessages();
};

const fetchMessages = async () => {
   if (!selectedOrderId.value) return;
   try {
      const res = await axios.get(`/api/orders/${selectedOrderId.value}/messages`, {
         headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      messages.value = res.data;
      scrollToBottom();
   } catch (e) {}
};

const sendMessage = async () => {
   if (!newMessage.value.trim() || sending.value) return;
   sending.value = true;
   try {
      // Logic for receiver_id (if client, send to admin (1), if admin send to client)
      const receiverId = selectedOrder.value.created_by === user.value.id ? 1 : selectedOrder.value.created_by;
      await axios.post('/api/messages', {
         order_id: selectedOrderId.value,
         receiver_id: receiverId,
         content: newMessage.value
      }, {
         headers: { Authorization: `Bearer ${localStorage.getItem('auth_token')}` }
      });
      newMessage.value = '';
      await fetchMessages();
   } finally {
      sending.value = false;
   }
};

const scrollToBottom = () => {
   nextTick(() => { if (pool.value) pool.value.scrollTop = pool.value.scrollHeight; });
};

const formatTime = (date) => new Date(date).toLocaleString(isRtl.value ? 'ar-EG' : 'en-US', { hour: '2-digit', minute: '2-digit' });
const formatTimeShort = (date) => new Date(date).toLocaleTimeString(isRtl.value ? 'ar-EG' : 'en-US', { hour: '2-digit', minute: '2-digit' });

</script>

<style scoped>
.messages-view { height: calc(100vh - 120px); display: flex; flex-direction: column; }
.page-header { margin-bottom: 1.5rem; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #1a3a5f; margin: 0; }
.page-subtitle { color: #64748b; font-size: 0.9375rem; margin-top: 0.25rem; }

.chat-layout { display: grid; grid-template-columns: 340px 1fr; gap: 1.5rem; flex: 1; overflow: hidden; }

.orders-list-card { display: flex; flex-direction: column; overflow: hidden; }
.list-header { padding: 1.25rem; border-bottom: 1px solid #f1f5f9; font-weight: 800; color: #1a3a5f; font-size: 0.9375rem; }
.orders-scroll { flex: 1; overflow-y: auto; }

.order-chat-item { padding: 1.25rem; display: flex; gap: 1rem; cursor: pointer; border-bottom: 1px solid #fafbfc; transition: all 0.2s; }
.order-chat-item:hover { background: #fafbfc; }
.order-chat-item.active { background: #f0f9ff; border-left: 4px solid #0ea5e9; }
.rtl .order-chat-item.active { border-left: none; border-right: 4px solid #0ea5e9; }

.order-avatar { width: 44px; height: 44px; border-radius: 12px; background: #e0f2fe; color: #0ea5e9; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8125rem; flex-shrink: 0; }
.order-chat-info { flex: 1; overflow: hidden; }
.top-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem; }
.order-code { font-weight: 800; color: #1a3a5f; font-size: 0.875rem; font-family: monospace; }
.chat-time { font-size: 0.7rem; color: #94a3b8; font-weight: 600; }
.order-title-mini { font-size: 0.8125rem; font-weight: 700; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.last-msg-preview { font-size: 0.75rem; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 0.25rem; }

.chat-area-card { display: flex; flex-direction: column; overflow: hidden; position: relative; }
.chat-header-main { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.2s; }
.chat-header-main:hover { background: #fcfdfe; }
.header-info { display: flex; flex-direction: column; }
.header-code { font-weight: 800; color: #0ea5e9; font-size: 0.75rem; font-family: monospace; }
.header-title { font-weight: 800; color: #1a3a5f; font-size: 1.125rem; }

.message-pool { flex: 1; overflow-y: auto; padding: 2rem; background: #fafbfc; display: flex; flex-direction: column; gap: 1.25rem; }
.msg-bubble { max-width: 70%; }
.msg-bubble.mine { align-self: flex-end; }
.msg-bubble.theirs { align-self: flex-start; }

.bubble-content { padding: 1rem 1.25rem; border-radius: 18px; position: relative; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.mine .bubble-content { background: #1a3a5f; color: white; border-bottom-right-radius: 4px; }
.theirs .bubble-content { background: white; color: #1e293b; border: 1px solid #eef2f6; border-bottom-left-radius: 4px; }

.sender-name { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; opacity: 0.8; }
.msg-text { font-size: 0.9375rem; line-height: 1.5; white-space: pre-wrap; }
.msg-time { font-size: 0.65rem; opacity: 0.5; text-align: right; margin-top: 6px; }

.chat-footer-main { padding: 1.5rem; border-top: 1px solid #f1f5f9; display: flex; gap: 1rem; }
.chat-footer-main textarea { flex: 1; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.875rem 1.25rem; height: 52px; resize: none; font-size: 0.9375rem; transition: border-color 0.2s; }
.chat-footer-main textarea:focus { border-color: #0ea5e9; outline: none; }

.no-selection { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; color: #94a3b8; }

.rtl .chat-layout { direction: rtl; }
.rtl .msg-bubble.mine { align-self: flex-start; } /* Overridden by LTR container logic sometimes */
.rtl .msg-bubble.theirs { align-self: flex-end; }
</style>
