export function createAssistantHandlers(assistant) {
  const tasksContainer = document.getElementById('tasks-list');
  const listGroup = document.createElement('div');
  listGroup.className = 'list-group';
  const emptyState = document.createElement('div');
  emptyState.className = 'text-body-tertiary fst-italic py-3 text-center';
  emptyState.textContent = 'No tasks in the backlog yet.';

  if (tasksContainer) {
    tasksContainer.innerHTML = '';
    tasksContainer.appendChild(listGroup);
    tasksContainer.appendChild(emptyState);
  }

  assistant.tasks = assistant.tasks || {};
  assistant.lastId = assistant.lastId || 0;

  const endpoint = '/virtual-project-manager/tasks';

  function updateLastId(tasks = []) {
    const maxId = tasks.reduce((max, task) => Math.max(max, task.id || 0), 0);
    assistant.lastId = Math.max(assistant.lastId, maxId);
  }

  function mapTasks(tasks = []) {
    const mapped = {};
    tasks.forEach((task) => {
      mapped[task.id] = task;
    });
    updateLastId(tasks);
    assistant.tasks = mapped;
    renderTasks();
  }

  function getHeaders() {
    return {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': assistant.csrfToken
    };
  }

  async function request(url, options = {}) {
    try {
      const response = await fetch(url, options);
      if (!response.ok) {
        const errorBody = await response.json().catch(() => ({}));
        throw new Error(errorBody.message || 'Unexpected server error');
      }
      return await response.json();
    } catch (error) {
      assistant.log('Tasks request failed', error.message);
      throw error;
    }
  }

  function renderTasks() {
    if (!tasksContainer) {
      return;
    }

    const tasks = Object.values(assistant.tasks || {}).sort((a, b) => {
      if (a.priority === b.priority) {
        return a.id - b.id;
      }
      return a.priority - b.priority;
    });

    listGroup.innerHTML = '';

    if (tasks.length === 0) {
      emptyState.style.display = 'block';
      return;
    }

    emptyState.style.display = 'none';

    tasks.forEach((task) => {
      const item = document.createElement('div');
      item.className = 'list-group-item';

      const wrapper = document.createElement('div');
      wrapper.className = 'd-flex justify-content-between align-items-center';

      const title = document.createElement('span');
      title.textContent = task.title;

      const badge = document.createElement('span');
      badge.className = 'badge-phoenix badge-phoenix-primary badge';
      badge.textContent = task.priority;

      wrapper.appendChild(title);
      wrapper.appendChild(badge);
      item.appendChild(wrapper);
      listGroup.appendChild(item);
    });
  }

  function shareBacklogOverview() {
    const tasks = Object.values(assistant.tasks || {});
    const count = tasks.length;
    const summary = count === 0 ? 'No tasks in the backlog yet.' : `${count} task${count === 1 ? '' : 's'} ready for review.`;
    assistant.log('Sharing current backlog status', summary);
  }

  async function loadTasks() {
    const result = await request(endpoint);
    mapTasks(result.tasks || []);
    shareBacklogOverview();
    return result;
  }

  renderTasks();
  loadTasks().catch(() => {});

  return {
    async getAllTasks() {
      const result = await loadTasks();
      return {
        success: true,
        tasks: assistant.tasks
      };
    },

    async changePriority({ id, priority }) {
      if (!id) {
        assistant.log('Priority update failed', 'Missing task identifier.');
        return { success: false, error: 'Missing task ID' };
      }

      try {
        const result = await request(`${endpoint}/${id}`, {
          method: 'PATCH',
          headers: getHeaders(),
          body: JSON.stringify({ priority })
        });
        assistant.tasks[id] = result.task;
        renderTasks();
        shareBacklogOverview();
        assistant.log(`Priority updated for task #${id}`, `New priority: ${priority}`);
        return { success: true, task: result.task };
      } catch (error) {
        return { success: false, error: error.message };
      }
    },

    async addTask({ text, priority }) {
      if (!text) {
        assistant.log('Task creation failed', 'Missing title for the task.');
        return { success: false, error: 'Title is required' };
      }

      try {
        const result = await request(endpoint, {
          method: 'POST',
          headers: getHeaders(),
          body: JSON.stringify({ title: text, priority })
        });
        assistant.tasks[result.task.id] = result.task;
        updateLastId([result.task]);
        renderTasks();
        shareBacklogOverview();
        assistant.log(`Task #${result.task.id} added`, `"${result.task.title}" with priority ${result.task.priority}`);
        return { success: true, task: result.task };
      } catch (error) {
        return { success: false, error: error.message };
      }
    },

    async deleteTask({ id }) {
      if (!id) {
        assistant.log('Task removal failed', 'Missing task identifier.');
        return { success: false, error: 'Missing task ID' };
      }

      try {
        await request(`${endpoint}/${id}`, {
          method: 'DELETE',
          headers: getHeaders()
        });
        const task = assistant.tasks[id];
        delete assistant.tasks[id];
        renderTasks();
        shareBacklogOverview();
        const message = task?.title ? `Removed "${task.title}" from the plan.` : 'Task removed from the plan.';
        assistant.log(`Task #${id} removed`, message);
        return { success: true };
      } catch (error) {
        return { success: false, error: error.message };
      }
    }
  };
}
