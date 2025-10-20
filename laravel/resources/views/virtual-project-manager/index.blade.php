@extends('layouts.app')

@section('content')
  @include('virtual-project-manager.partials.navbar')

  <h1 class="h3 mb-3">AI Virtual Project Manager</h1>

  <div class="card" id="js-user-preferences">
    <div class="card-body">
      <div class="row gy-2">
        <div class="col-12">
          <h2 class="h5 pb-2 border-bottom border-dashed">Setup your assistant</h2>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <label class="app-form-label required" for="f-name">Your name</label>
          <input class="form-control" id="f-name" type="text" placeholder="Alex Gavazov..." required/>
          <div class="invalid-feedback">Please enter your name.</div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <label class="app-form-label" for="f-role">Your role</label>
          <select class="form-select" id="f-role">
            <option value="engineer">Engineer</option>
            <option value="designer" disabled>Designer (coming soon)</option>
            <option value="qa" disabled>QA (coming soon)</option>
            <option value="other" disabled>Other (coming soon)</option>
          </select>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <label class="app-form-label" for="f-assistantBehaviour">Assistant behaviour</label>
          <select class="form-select" id="f-assistantBehaviour">
            <option value="normal">Sam - Normal person</option>
            <option value="focused">Ray - Straight-to-the-point</option>
            <option value="crazy">Karen - Crazy and nervous</option>
          </select>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
          <label class="app-form-label" for="f-language">Language</label>
          <select class="form-select" id="f-language">
            <option value="english">English</option>
            <option value="bulgarian">Bulgarian</option>
            <option value="german">German (not tested)</option>
          </select>
        </div>

        <div class="col-12">
          <button class="btn btn-primary mt-3" id="js-start-btn" type="button">
            <i class="fa-regular fa-microphone"></i>
            Start
          </button>
        </div>
      </div>
    </div>
  </div>

  <div id="start-conv-message" class="text-body-tertiary text-center fs-4" style="display: none"></div>
  <div id="audio-visualizer" style="width: 100%; height: calc(100vh - 500px);"></div>
  <pre id="action-log"></pre>

  <script type="module">
    const storageKey = 'vpmUserPreferences';
    const $name = $('#f-name');
    const $role = $('#f-role');
    const $assistantBehaviour = $('#f-assistantBehaviour');
    const $language = $('#f-language');
    const $startButton = $('#js-start-btn');
    const $preferencesCard = $('#js-user-preferences');

    function getPreferences() {
      return {
        name: $name.val().trim(),
        role: $role.val(),
        assistantBehaviour: $assistantBehaviour.val(),
        language: $language.val()
      };
    }

    function savePreferences() {
      const preferences = getPreferences();
      window.localStorage.setItem(storageKey, JSON.stringify(preferences));
    }

    function loadPreferences() {
      const storedPreferences = window.localStorage.getItem(storageKey);
      if (!storedPreferences) {
        return;
      }

      try {
        const preferences = JSON.parse(storedPreferences);
        if (preferences.name) {
          $name.val(preferences.name);
          $name.removeClass('is-invalid');
        }
        if (preferences.role) {
          $role.val(preferences.role);
        }
        if (preferences.assistantBehaviour) {
          $assistantBehaviour.val(preferences.assistantBehaviour);
        }
        if (preferences.language) {
          $language.val(preferences.language);
        }
      } catch (error) {
        console.error('Unable to load user preferences', error);
      }
    }

    loadPreferences();

    $name.on('input', function () {
      if ($name.val().trim()) {
        $name.removeClass('is-invalid');
      }
      savePreferences();
    });

    $role.on('change', savePreferences);
    $assistantBehaviour.on('change', savePreferences);
    $language.on('change', savePreferences);

    $startButton.on('click', function () {
      if (!$name.val().trim()) {
        $name.addClass('is-invalid').focus();
        return;
      }

      savePreferences();

      $preferencesCard.hide();
      $('#start-conv-message')
        .text(`${$name.val().trim()}, you can now start the conversation with your AI assistant!`)
        .fadeIn();

      initTheAssistant();
    });
  </script>

  <script type="module">
    window.initTheAssistant = function () {
      const name = $('#f-name').val().trim();

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
          Ти си Project Manager, аз съм ${name}. Ще ми помагаш да си планирам задачите. Ще взимаш мнение и участие в планирането. Ще ми даваш съвети и активно ще ме разпитваш за дтайли, за да съм сигурен, че създавам правилни задачи.
          Аз съм програмист и искам да планирам нещата, точно и ясно.
        `
      });

      const visualizerNode = document.getElementById('audio-visualizer');
      const audioVisualizer = new MicBallVisualizer(visualizerNode, {
        colorInner: '#22d3ee',
        colorOuter: '#8b5cf6',
        minRadius: 52,
        maxRadius: 118,
        glow: true
      });

      //

      projectManagerApp.startConnectionAndMicrophone();
      audioVisualizer.start();
    }
  </script>
@endsection
