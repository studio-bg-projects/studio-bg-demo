@extends('layouts.app')

@section('content')
  <h1 class="h3">AI Virtual Project Manager</h1>

  <button class="btn btn-primary" id="js-start-btn">START</button>

  <div id="audio-visualizer" style="width: 100%; height: 500px;"></div>
  <div id="audio-controls"></div>
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
