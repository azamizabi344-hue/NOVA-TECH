<script setup>
import { computed } from 'vue'
import { useModal } from '@/composables/useModal'
import { capitalize } from '@/utils/strings'
import { socialLabels } from '@/data/team'

/**
 * MemberDetailModal - the CONTENT of the shared modal for a team member.
 *
 * Replaces openMemberModal(), which built the same markup as a string plus a
 * for...in loop over member.social to produce the three social links.
 */
const props = defineProps({
  member: { type: Object, required: true },
})

// Object.entries turns { linkedin: '#', ... } into
// [{ key: 'linkedin', value: '#' }, { key: 'twitter', value: '#' }, ...]
// which is exactly what v-for needs. This replaces the for...in loop.
const socialLinks = computed(() =>
  Object.entries(props.member.social).map(([key, value]) => ({
    key,
    href: value,
    label: socialLabels[key] ?? key,
  })),
)
</script>

<template>
  <div class="modal-detail">
    <span class="modal-detail__category">{{ capitalize(member.department) }}</span>

    <h3>{{ member.name }}</h3>

    <div class="modal-detail__meta">
      <span><strong>Position:</strong> {{ member.role }}</span>
    </div>

    <p>{{ member.bio }}</p>

    <h4>Skills</h4>

    <div class="skill-chips">
      <span v-for="skill in member.skills" :key="skill">{{ skill }}</span>
    </div>

    <div class="modal-detail__socials">
      <a
        v-for="social in socialLinks"
        :key="social.key"
        :href="social.href"
        :aria-label="social.key"
      >
        {{ social.label }}
      </a>
    </div>
  </div>
</template>
