<script setup>
import { useModal } from '@/composables/useModal'
import { capitalize } from '@/utils/strings'
import MemberDetailModal from '@/components/modals/MemberDetailModal.vue'

/**
 * TeamCard - one team member tile.
 *
 * Replaces buildTeamCard() + bindTeamButtons() from the original team.js.
 * The click handler is bound once by Vue, so it never needs re-binding after
 * a re-render.
 */
defineProps({
  member: { type: Object, required: true },
})

const { openModal } = useModal()

function showProfile(member) {
  openModal(MemberDetailModal, { member })
}
</script>

<template>
  <article class="team-card card-enter">
    <!--
      Not an <img>: the project has no photos, so the initials are shown as
      large text, exactly as the original did.
    -->
    <div class="team-card__photo">{{ member.initials }}</div>

    <div class="team-card__info">
      <span class="team-card__badge">{{ capitalize(member.department) }}</span>

      <h3 class="team-card__name">{{ member.name }}</h3>

      <div class="team-card__role">{{ member.role }}</div>

      <p class="team-card__bio">{{ member.bio }}</p>

      <button type="button" class="team-card__btn" @click="showProfile(member)">
        View Profile &rarr;
      </button>
    </div>
  </article>
</template>
