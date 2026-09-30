<script setup>
import { capitalize } from '@/utils/strings'

/**
 * ArticleDetailModal - the CONTENT of the shared modal for one article.
 *
 * Replaces openArticleModal() from the original blog.js.
 */
defineProps({
  article: { type: Object, required: true },
})
</script>

<template>
  <div class="modal-detail">
    <span class="modal-detail__category">{{ capitalize(article.category) }}</span>

    <h3>{{ article.title }}</h3>

    <div class="modal-detail__meta">
      <span><strong>Author:</strong> {{ article.author }}</span>
      <span><strong>Date:</strong> {{ article.date }}</span>
      <span><strong>Read:</strong> {{ article.readTime }} min</span>
    </div>

    <p>{{ article.content }}</p>

    <div>
      <!-- contact.html becomes the /contact route -->
      <router-link to="/contact" class="btn btn--primary">Discuss This Topic</router-link>
    </div>
  </div>
</template>
