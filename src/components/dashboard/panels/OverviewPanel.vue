<script setup>
import Badge from '@/components/ui/Badge.vue'
import MessageItem from '@/components/dashboard/MessageItem.vue'
import { capitalize } from '@/utils/strings'
import { overviewCards } from '@/data/dashboard'
import { useDashboard } from '@/composables/useDashboard'

/**
 * OverviewPanel - the six stat cards, the recent projects table and the recent
 * messages list.
 *
 * renderOverview() in the original assigned each value with
 * document.getElementById('card-x').textContent = ... . Here the cards are
 * declared as data (overviewCards) and each reads its value out of one computed
 * via `stats[card.source]`, so a new card is one line of data, not a new id in
 * the HTML plus a new line in dashboard.js.
 */
const { overviewStats, recentProjects, recentMessages } = useDashboard()
</script>

<template>
  <div class="dash-cards">
    <div v-for="card in overviewCards" :key="card.id" class="dash-card">
      <div class="dash-card__label">{{ card.label }}</div>
      <div class="dash-card__value">{{ overviewStats[card.source] }}</div>
      <div class="dash-card__trend">{{ card.trend }}</div>
    </div>
  </div>

  <div class="panel-card">
    <h2 class="panel-card__title">Recent Projects</h2>

    <div class="dash-table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Project</th>
            <th>Category</th>
            <th>Client</th>
            <th>Year</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in recentProjects" :key="project.id">
            <td><strong>{{ project.name }}</strong></td>
            <td>{{ capitalize(project.category) }}</td>
            <td>{{ project.client }}</td>
            <td>{{ project.year }}</td>
            <td>
              <Badge :label="project.status" :tone="project.status" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel-card">
    <h2 class="panel-card__title">Recent Messages</h2>

    <div class="message-list">
      <MessageItem
        v-for="message in recentMessages"
        :key="message.id ?? message.createdAt + message.email"
        :message="message"
      />
    </div>
  </div>
</template>
