@extends('layouts.app')

@section('content')
  @include('virtual-project-manager.partials.navbar')

  <h1 class="h3 mb-3">AI Virtual Project Manager</h1>

  <div class="card">
    <div class="card-body">
      <div class="row gy-2">
        <div class="col-12">
          <h2 class="h5 pb-2 border-bottom border-dashed">Setup your assistant</h2>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <label class="app-form-label required" for="f-name">Your name</label>
          <input class="form-control form-control-lg" id="f-name" type="text" placeholder="Alex Gavazov..." required/>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <label class="app-form-label" for="f-role">Your role</label>
          <select class="form-select form-select-lg" id="f-role">
            <option value="engineer">Engineer</option>
            <option value="designer" disabled>Designer (coming soon)</option>
            <option value="qa" disabled>QA (coming soon)</option>
            <option value="other" disabled>Other (coming soon)</option>
          </select>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <label class="app-form-label" for="f-assistantBehaviour">Assistant behaviour</label>
          <select class="form-select form-select-lg" id="f-assistantBehaviour">
            <option value="normal">Sam - Normal person</option>
            <option value="focused">Ray - Straight-to-the-point</option>
            <option value="crazy">Karen - Crazy and nervous</option>
          </select>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <label class="app-form-label" for="f-assistantBehaviour">Language</label>
          <select class="form-select form-select-lg" id="f-assistantBehaviour">
            <option value="english">English</option>
            <option value="bulgarian">Bulgarian</option>
            <option value="german">German (not tested)</option>
          </select>
        </div>

        <div class="col-12">
          <button class="btn btn-primary btn-lg mt-3" id="js-start-btn" type="button">Start</button>
        </div>
      </div>
    </div>
  </div>

  <div id="audio-visualizer" style="width: 100%; height: calc(100vh - 500px);"></div>
  <pre id="action-log"></pre>

  <script type="module">
    const projectManagerApp = new MyPersonalAssistant({
      model: 'gpt-realtime-mini',
      sessionUrl: @json(url('/virtual-project-manager/session')),
      csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      audioControlsNode: document.getElementById('audio-controls'),
      logNode: document.getElementById('action-log'),
      tools: [
        {
          type: 'function',
          name: 'getAllTasks',
          description: 'Връща наличните задачи'
        },
        {
          type: 'function',
          name: 'changePriority',
          description: 'Смяна на приоритета на задача',
          parameters: {
            type: 'object',
            properties: {
              id: {type: 'integer', description: 'ID-то на задачата която ще и променим приоритета'},
              priority: {type: 'integer', description: 'Задаване на стойност на приоритета за дадена задача'}
            }
          }
        },
        {
          type: 'function',
          name: 'addTask',
          description: 'Добавяне на нова задача',
          parameters: {
            type: 'object',
            properties: {
              text: {type: 'string', description: 'Текст на задачата'},
              priority: {type: 'integer', description: 'Приоритет на новата задача'}
            }
          }
        },
        {
          type: 'function',
          name: 'deleteTask',
          description: 'Изтриване на задача',
          parameters: {
            type: 'object',
            properties: {
              id: {type: 'integer', description: 'ID-то на задачата която ще бъде изтрита'}
            }
          }
        }
      ],
      instructions: `
      Ти си Project Manager, аз съм Алекс. Ще ми помагаш да си планирам задачите. Ще взимаш мнение и участие в планирането. Ще ми даваш съвети и активно ще ме разпитваш за дтайли, за да съм сигурен, че създавам правилни задачи.
      Аз съм програмист и искам да планирам нещата, точно и ясно.
      **Искам ти да започнеш разговора и да ме питаш как съм!**
    `
    });

    const visualizerNode = document.getElementById('audio-visualizer');
    const ball = new MicBallVisualizer(visualizerNode, {
      colorInner: '#22d3ee',
      colorOuter: '#8b5cf6',
      minRadius: 52,
      maxRadius: 118,
      glow: true
    });

    $('#js-start-btn').click(function () {
      projectManagerApp.startConnectionAndMicrophone();
      ball.start()
    });
  </script>
@endsection
