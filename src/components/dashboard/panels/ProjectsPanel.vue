<script setup>
import { ref, reactive, onBeforeUnmount } from 'vue'
import { capitalize } from '@/utils/strings'
import { projectCategories, projectStatuses } from '@/data/dashboard'
import { useDashboard } from '@/composables/useDashboard'

/**
 * ProjectsPanel - add a project, change its status, delete it.
 *
 * The original's renderManageProjects() re-rendered the whole table via
 * innerHTML and then had to re-bind every control afterwards:
 *
 *   const statusSelects = tbody.querySelectorAll('.dash-status-select');
 *   for (const select of statusSelects) select.addEventListener('change', ...)
 *   const deleteButtons = tbody.querySelectorAll('.dash-delete');
 *   for (const button of deleteButtons) button.addEventListener('click', ...)
 *
 * That rebinding loop existed only because innerHTML destroys the nodes it
 * creates, so listeners must be re-attached on every render. A v-for has neither
 * problem: the @change / @click below are attached once and survive re-renders,
 * and :key means only the changed row is patched.
 */
const { projects, setProjectStatus, deleteProject, addProject } = useDashboard()

// The add-project form. `reactive` because it is one object bound to five
// separate inputs.
const form = reactive({
  name: '',
  category: projectCategories[0].value,
  client: '',
  year: '',
  status: projectStatuses[0].value,
})

const successVisible = ref(false)
let successTimer = null

function onSubmit() {
  const name = form.name.trim()
  const year = Number(form.year)

  // The original used two window.alert() calls. Kept as-is: it is the specified
  // behaviour, and swapping it for inline errors would change the flow.
  if (name === '') {
    alert('Please enter a project name.')
    return
  }
  if (!year || year < 2015 || year > 2030) {
    alert('Please enter a valid year between 2015 and 2030.')
    return
  }

  addProject({
    name,
    category: form.category,
    client: form.client.trim(),
    year,
    status: form.status,
  })

  // form.reset() replaced by assigning the defaults - v-model needs a new object
  // to reset every field, and this also clears the reactive state behind them.
  form.name = ''
  form.category = projectCategories[0].value
  form.client = ''
  form.year = ''
  form.status = projectStatuses[0].value

  successVisible.value = true
  clearTimeout(successTimer)
  successTimer = setTimeout(() => {
    successVisible.value = false
  }, 4000)
}

onBeforeUnmount(() => clearTimeout(successTimer))
</script>

<template>
  <div class="panel__header">
    <div>
      <h2 class="panel__title">Project Management</h2>
      <p class="panel__sub">Add new projects or update their status.</p>
    </div>
  </div>

  <!-- Add new project -->
  <div class="panel-card">
    <h2 class="panel-card__title">Add New Project</h2>

    <form class="project-form" novalidate @submit.prevent="onSubmit">
      <div class="form__group">
        <label class="form__label" for="ap-name">Project Name</label>
        <input id="ap-name" v-model="form.name" type="text" class="form__input" placeholder="Project name">
      </div>

      <div class="form__group">
        <label class="form__label" for="ap-category">Category</label>
        <select id="ap-category" v-model="form.category" class="form__select">
          <option v-for="option in projectCategories" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
      </div>

      <div class="form__group">
        <label class="form__label" for="ap-client">Client</label>
        <input id="ap-client" v-model="form.client" type="text" class="form__input" placeholder="Client name">
      </div>

      <div class="form__group">
        <label class="form__label" for="ap-year">Year</label>
        <input
          id="ap-year"
          v-model="form.year"
          type="number"
          class="form__input"
          placeholder="2026"
          min="2015"
          max="2030"
        >
      </div>

      <div class="form__group">
        <label class="form__label" for="ap-status">Status</label>
        <select id="ap-status" v-model="form.status" class="form__select">
          <option v-for="option in projectStatuses" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
      </div>

      <button type="submit" class="btn btn--primary">Add Project</button>
    </form>

    <div v-show="successVisible" class="dash-success">Project added successfully.</div>
  </div>

  <!-- All projects -->
  <div class="panel-card">
    <h2 class="panel-card__title">All Projects</h2>

    <div class="dash-table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th>Project</th>
            <th>Category</th>
            <th>Client</th>
            <th>Year</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in projects" :key="project.id" :data-id="project.id">
            <td><strong>{{ project.name }}</strong></td>
            <td>{{ capitalize(project.category) }}</td>
            <td>{{ project.client }}</td>
            <td>{{ project.year }}</td>
            <td>
              <!--
                @change calls setProjectStatus, which mutates the project in the
                shared list. The overview panel reads the same list, so its badge
                and the Total Projects card update on their own - the original
                had to call renderOverview() explicitly from inside this handler.
              -->
              <select
                class="sort-select dash-status-select"
                :aria-label="`Status for ${project.name}`"
                :value="project.status"
                @change="setProjectStatus(project.id, $event.target.value)"
              >
                <option v-for="option in projectStatuses" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </td>
            <td>
              <button type="button" class="btn btn--ghost btn--sm dash-delete" @click="deleteProject(project.id)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
