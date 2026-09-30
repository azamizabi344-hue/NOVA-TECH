<script setup>
import { teamMembers } from '@/data/dashboard'

/**
 * TeamPanel - the seven team members with their initials avatars.
 *
 * Replaces renderTeamPanel(). Note the original passed member.initials through
 * window.escapeHtml() before injecting it as HTML; here {{ }} escapes
 * automatically, so that call is simply gone.
 */
</script>

<template>
  <div class="panel__header">
    <div>
      <h2 class="panel__title">Team Members</h2>
      <p class="panel__sub">Everyone working at NOVA TECH.</p>
    </div>
  </div>

  <div class="dash-team">
    <div v-for="member in teamMembers" :key="member.name" class="dash-team__member">
      <span class="dash-team__avatar">{{ member.initials }}</span>
      <div>
        <h4>{{ member.name }}</h4>
        <p>{{ member.role }}</p>
      </div>
    </div>
  </div>
</template>
