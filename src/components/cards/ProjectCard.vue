<script setup>
import { useModal } from '@/composables/useModal'
import { capitalize } from '@/utils/strings'
import ProjectDetailModal from '@/components/modals/ProjectDetailModal.vue'

/**
 * ProjectCard - one project tile.
 *
 * Replaces buildProjectCard() + bindProjectButtons() from the original
 * projects.js (16 lines of HTML string plus 9 lines of querySelectorAll loop
 * that had to be re-run after every render).
 *
 * Because Vue attaches the @click handler to this component's own markup, it
 * is bound once and forever - no re-binding after each re-render.
 */
defineProps({
  project: { type: Object, required: true },
})

const { openModal } = useModal()

function showDetails(project) {
  // Instead of openModal(htmlString) we hand over a COMPONENT + its props.
  openModal(ProjectDetailModal, { project })
}
</script>

<template>
  <article class="project-card card-enter" :data-category="project.category">
    <div class="project-card__image">
      <span class="project-card__label">{{ project.imageText }}</span>
      <span class="project-card__category">{{ capitalize(project.category) }}</span>
    </div>

    <div class="project-card__body">
      <h3 class="project-card__title">{{ project.name }}</h3>

      <p class="project-card__desc">{{ project.description }}</p>

      <!--
        Inner v-for for the tech chips. Note this is .project-card__tech-item
        (card variant) - the modal uses a different class, .tech-chips span.
      -->
      <div class="project-card__tech">
        <span
          v-for="tech in project.technologies"
          :key="tech"
          class="project-card__tech-item"
        >
          {{ tech }}
        </span>
      </div>

      <button type="button" class="project-card__btn" @click="showDetails(project)">
        View Details &rarr;
      </button>
    </div>
  </article>
</template>
