<script setup>
import { useModal } from '@/composables/useModal'
import { capitalize } from '@/utils/strings'
import ArticleDetailModal from '@/components/modals/ArticleDetailModal.vue'

/**
 * BlogCard - one article tile.
 *
 * Replaces buildArticleCard() + bindArticleButtons() from the original blog.js.
 */
defineProps({
  article: { type: Object, required: true },
})

const { openModal } = useModal()

function readMore(article) {
  openModal(ArticleDetailModal, { article })
}
</script>

<template>
  <article class="blog-card card-enter">
    <div class="blog-card__image">
      <span>{{ article.imageText }}</span>
      <span class="blog-card__category">{{ capitalize(article.category) }}</span>
    </div>

    <div class="blog-card__body">
      <h3 class="blog-card__title">{{ article.title }}</h3>

      <div class="blog-card__meta">
        <span class="blog-card__author">
          <span class="blog-card__avatar">{{ article.authorInitials }}</span>
          {{ article.author }}
        </span>
        <span>&bull; {{ article.date }}</span>
        <span>&bull; {{ article.readTime }} min read</span>
      </div>

      <p class="blog-card__desc">{{ article.description }}</p>

      <button type="button" class="blog-card__btn" @click="readMore(article)">
        Read More &rarr;
      </button>
    </div>
  </article>
</template>
