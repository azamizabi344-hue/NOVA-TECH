<script setup>
import Badge from '@/components/ui/Badge.vue'
import { sampleUsers } from '@/data/dashboard'

/**
 * UsersPanel - the demo account table.
 *
 * Replaces renderUsersPanel(). The 'invited' user keeps its own label but borrows
 * the 'pending' colour, exactly as the original's ternary did.
 */
</script>

<template>
  <div class="panel__header">
    <div>
      <h2 class="panel__title">Users</h2>
      <p class="panel__sub">Accounts registered in the system (demo data).</p>
    </div>
  </div>

  <div class="panel-card">
    <div class="dash-table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in sampleUsers" :key="user.email">
            <td><strong>{{ user.name }}</strong></td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>
              <Badge :label="user.status" :tone="user.status === 'active' ? 'active' : 'pending'" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
