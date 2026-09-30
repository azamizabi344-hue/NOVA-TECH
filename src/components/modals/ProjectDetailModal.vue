<script setup>
import { useModal } from '@/composables/useModal'
import { capitalize } from '@/utils/strings'

/**
 * ProjectDetailModal - the CONTENT of the shared modal for a project.
 *
 * The original openProjectModal() built a 19-line template string with 7 calls
 * to window.escapeHtml() and handed it to window.openModal(html).
 *
 * Here the markup is a real template. Vue's {{ }} interpolation escapes text
 * automatically, so escapeHtml is not just unnecessary - calling it would be a
 * bug (it would double-encode & into &amp;amp;). No innerHTML anywhere.
 *
 * Opened from ProjectCard.vue like this:
 *   openModal(ProjectDetailModal, { project })
 */
defineProps({
  project: { type: Object, required: true },
})

// The CTA link navigates away, so close the modal on click - otherwise it
// would stay open on top of the new page.
const { closeModal } = useModal()
</script>

<template>
  <!--
    .modal-detail is the class family styled at style.css:2136-2183. It must be
    a direct child of .modal__content for those selectors to apply, which it is.
  -->
  <div class="modal-detail">
    <span class="modal-detail__category">{{ capitalize(project.category) }}</span>

    <h3>{{ project.name }}</h3>

    <div class="modal-detail__meta">
      <span><strong>Client:</strong> {{ project.client }}</span>
      <span><strong>Year:</strong> {{ project.year }}</span>
    </div>

    <p>{{ project.longDescription }}</p>

    <h4>Technologies</h4>

    <!--
      Nested v-for: one chip per technology. In the original this was
      project.technologies.map(t => '<span>' + escapeHtml(t) + '</span>').join('')
    -->
    <div class="tech-chips">
      <span v-for="tech in project.technologies" :key="tech">{{ tech }}</span>
    </div>

    <div>
      <router-link to="/contact" class="btn btn--primary" @click="closeModal">
        Start A Similar Project
      </router-link>
    </div>
  </div>
</template>
