<script setup>
import { formatDate } from '@/utils/strings'

/**
 * MessageItem - one row in a message list.
 *
 * Used twice: the Overview panel (newest 3) and the Messages panel (all of
 * them). The original had buildMessageItem() returning a template string, and
 * called it from both renderRecentMessagesList() and renderMessagesPanel().
 */
defineProps({
  message: { type: Object, required: true },
})
</script>

<template>
  <div class="message-list__item">
    <div>
      <div class="message-list__info">
        <strong>{{ message.name }} ({{ message.email }})</strong>
        <span>{{ formatDate(message.createdAt) }} &middot; {{ message.subject }}</span>
      </div>
      <p class="message-list__text">{{ message.text }}</p>
    </div>
  </div>
</template>
