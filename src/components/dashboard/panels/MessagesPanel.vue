<script setup>
import MessageItem from '@/components/dashboard/MessageItem.vue'
import { useDashboard } from '@/composables/useDashboard'

/**
 * MessagesPanel - every message from the contact form.
 *
 * Replaces renderMessagesPanel(), which called getAllMessages() and mapped it
 * through buildMessageItem(). `messages` is a computed in useDashboard that
 * already prefers real submissions over the sample data, and it is shared with
 * the Overview panel - so a visitor submitting the contact form updates both the
 * list here and the "Messages" stat card at the same time.
 */
const { messages } = useDashboard()
</script>

<template>
  <div class="panel__header">
    <div>
      <h2 class="panel__title">Messages</h2>
      <p class="panel__sub">Inquiries from the contact form (stored locally).</p>
    </div>
  </div>

  <div class="message-list">
    <MessageItem
      v-for="message in messages"
      :key="message.id ?? message.createdAt + message.email"
      :message="message"
    />
  </div>
</template>
