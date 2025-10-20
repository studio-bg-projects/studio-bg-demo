export function createAssistantHandlers(assistant) {
  function shareBacklogOverview() {
    const tasks = Object.values(assistant.tasks || {});
    const count = tasks.length;
    const summary = count === 0 ? 'No tasks in the backlog yet.' : `${count} task${count === 1 ? '' : 's'} ready for review.`;
    assistant.log('Sharing current backlog status', summary);
  }

  return {
    getAllTasks() {
      shareBacklogOverview();
      return {
        success: true,
        tasks: assistant.tasks
      };
    },

    changePriority({ id, priority }) {
      if (!assistant.tasks[id]) {
        assistant.log('Priority update failed', `Task #${id} was not found.`);
        return { success: false, error: 'Invalid task ID' };
      }

      assistant.tasks[id].priority = priority;
      assistant.saveState();
      assistant.log(`Priority updated for task #${id}`, `New priority: ${priority}`);

      return { success: true, priority };
    },

    addTask({ text, priority }) {
      assistant.lastId += 1;
      const id = assistant.lastId;

      const newTask = {
        id,
        text,
        priority
      };

      assistant.tasks[id] = newTask;
      assistant.saveState();
      assistant.log(`Task #${id} added`, `"${text}" with priority ${priority}`);
      shareBacklogOverview();

      return { success: true, task: newTask };
    },

    deleteTask({ id }) {
      const task = assistant.tasks[id];
      if (!task) {
        assistant.log('Task removal failed', `Task #${id} was not found.`);
        return { success: false, error: 'Invalid task ID' };
      }

      delete assistant.tasks[id];
      assistant.saveState();

      const message = task.text ? `Removed "${task.text}" from the plan.` : 'Task removed from the plan.';
      assistant.log(`Task #${id} removed`, message);
      shareBacklogOverview();

      return { success: true };
    }
  };
}
