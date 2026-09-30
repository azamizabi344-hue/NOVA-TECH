<script setup>
import { ref } from 'vue'
import SectionHead from '@/components/ui/SectionHead.vue'
import { faqs } from '@/data/home'

/**
 * FaqSection - the accordion.
 *
 * THE WHOLE POINT OF THIS COMPONENT is the line below. The original needed a
 * closeAllFaqItems() helper that walked every .faq-item, removed .open and
 * reset aria-expanded - 20 lines of DOM bookkeeping to guarantee "only one
 * open at a time".
 *
 * Here "only one open" is just a fact about a single value. If openIndex is 1,
 * item 1 is open. Clicking item 3 sets it to 3, so item 1 closes automatically.
 * There is no way to open two at once, because there is only one place the
 * state lives.
 *
 * openIndex === item.id  -> open that item
 * openIndex === null      -> everything closed
 * clicking the open one   -> set it back to null (toggle off)
 */
const openIndex = ref(null)

function toggle(id) {
  openIndex.value = openIndex.value === id ? null : id
}
</script>

<template>
  <section class="faq section-pad" id="faq">
    <div class="container faq__inner">
      <SectionHead tag="Need Help?" title="Frequently Asked Questions" />

      <div class="faq__list">
        <!--
          No v-if on the answer. The CSS animates it with
          max-height: 0 -> 300px, so the element must stay in the DOM for the
          transition to run. :class="{ open: ... }" is all that changes.
        -->
        <div
          v-for="item in faqs"
          :key="item.id"
          class="faq-item"
          :class="{ open: openIndex === item.id }"
        >
          <button
            type="button"
            class="faq-item__question"
            :aria-expanded="openIndex === item.id"
            :aria-controls="`faq-answer-${item.id}`"
            @click="toggle(item.id)"
          >
            {{ item.question }}
            <span class="faq-item__icon" aria-hidden="true">+</span>
          </button>

          <div :id="`faq-answer-${item.id}`" class="faq-item__answer">
            <p>{{ item.answer }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
