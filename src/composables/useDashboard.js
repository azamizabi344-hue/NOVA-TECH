import { ref, computed, watch } from 'vue'
import { readStorage, writeStorage } from '@/utils/storage'
import {
  PROJECTS_KEY,
  initialProjects,
  baseStats,
  sampleMessages,
} from '@/data/dashboard'

/**
 * useDashboard - all the state and logic behind the admin dashboard.
 *
 * The original dashboard.js was ~525 lines, of which roughly 300 were DOM
 * plumbing:
 *
 *   renderOverview()          -> one computed per value, rendered by <template>
 *   renderRecentProjectsTable -> a v-for
 *   renderManageProjects()    -> a v-for; the two querySelectorAll blocks that
 *                                bound every <select> and every delete button
 *                                become @change / @click in the template
 *   renderServicesPanel()     -> a v-for
 *   renderTeamPanel()         -> a v-for
 *   renderIntentsPanel()      -> a v-for
 *   renderMessagesPanel()     -> a v-for
 *   renderUsersPanel()        -> a v-for
 *   getAllMessages()          -> a computed
 *   saveProjects()            -> a watch
 *
 * The important consequence: the original had to remember to call renderOverview()
 * AND renderManageProjects() after every mutation. Here a mutation just changes a
 * ref, and every dependent computed re-evaluates by itself - a forgotten call is
 * no longer possible.
 */

/**
 * Loads the managed project list, falling back to a fresh copy of the seed list.
 *
 * A .slice() is required rather than referencing initialProjects directly: the
 * list is mutated (unshift / status writes / filter), and without the copy those
 * edits would permanently corrupt the seed data for the rest of the session.
 */
function loadProjects() {
  const stored = readStorage(PROJECTS_KEY, null)
  if (Array.isArray(stored)) return stored
  return initialProjects.slice()
}

const projects = ref(loadProjects())

/** Persist whenever the list changes, replacing the original's saveProjects() calls. */
watch(projects, (value) => writeStorage(PROJECTS_KEY, value), { deep: true })

/**
 * Contact-form messages, falling back to the samples when there are none.
 * Reactive, so the Messages panel and the Overview card update together.
 */
const storedMessages = ref(readStorage('novatech_contact_messages', []))

const messages = computed(() => {
  const list = storedMessages.value
  return Array.isArray(list) && list.length > 0 ? list : sampleMessages
})

/**
 * Total Projects = the company base count (150) plus the difference between the
 * current list and the seed list, so adding or deleting a row moves the number
 * live. This is the original's `addedCount` arithmetic, kept deliberately -
 * deleting all six rows therefore reports 144, not 0.
 */
const totalProjects = computed(
  () => baseStats.projects + (projects.value.length - initialProjects.length),
)

/** Every value the six overview cards display, keyed by their `source`. */
const overviewStats = computed(() => ({
  totalProjects: totalProjects.value,
  users: baseStats.users,
  messageCount: messages.value.length,
  team: baseStats.team,
  intents: baseStats.intents,
  revenue: baseStats.revenue,
}))

/** Newest first 5 for the overview table. */
const recentProjects = computed(() => projects.value.slice(0, 5))

/** Newest first 3 for the overview list. */
const recentMessages = computed(() => messages.value.slice(0, 3))

/** Sets a project's status. The original re-rendered two panels here. */
function setProjectStatus(id, status) {
  const project = projects.value.find((item) => item.id === id)
  if (project) project.status = status
}

/** Removes a project by id. */
function deleteProject(id) {
  projects.value = projects.value.filter((project) => project.id !== id)
}

/**
 * Adds a project to the FRONT of the list.
 * @returns the new project.
 */
function addProject({ name, category, client, year, status }) {
  const newProject = {
    id: Date.now(),
    name,
    category,
    client: client || 'Unknown client',
    year,
    status,
  }
  projects.value.unshift(newProject)
  return newProject
}

export function useDashboard() {
  return {
    projects,
    messages,
    overviewStats,
    recentProjects,
    recentMessages,
    setProjectStatus,
    deleteProject,
    addProject,
  }
}
