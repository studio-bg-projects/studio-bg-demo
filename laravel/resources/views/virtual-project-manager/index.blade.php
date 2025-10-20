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

        <div class="col-12 col-md-6 col-xl-4">
          <label class="app-form-label required" for="f-name">Your name</label>
          <input class="form-control" id="f-name" type="text" placeholder="Alex Gavazov..." required/>
          <div class="invalid-feedback">Please enter your name.</div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
          <label class="app-form-label" for="f-assistantBehaviour">Assistant behaviour</label>
          <select class="form-select" id="f-assistantBehaviour">
            <option value="normal">Sam - Normal person</option>
            <option value="focused">Ray - Straight-to-the-point</option>
            <option value="crazy">Karen - Crazy and nervous</option>
          </select>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
          <label class="app-form-label" for="f-language">Language</label>
          <select class="form-select" id="f-language">
            <option value="english">English</option>
            <option value="bulgarian">Bulgarian</option>
            <option value="german">German (not tested at all)</option>
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
    const $assistantBehaviour = $('#f-assistantBehaviour');
    const $language = $('#f-language');
    const $startButton = $('#js-start-btn');
    const $preferencesCard = $('#js-user-preferences');

    function getPreferences() {
      return {
        name: $name.val().trim(),
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
    const behaviourConfigs = {
      normal: {
        persona: 'Sam',
        description:
          'Keep a friendly, collaborative tone. Offer encouragement while making sure the plan stays realistic and organised.'
      },
      focused: {
        persona: 'Ray',
        description:
          'Be concise, direct, and structured. Quickly get to the heart of the task details and push for actionable next steps.'
      },
      crazy: {
        persona: 'Karen',
        description:
          'Turn the energy up to eleven. Be wildly enthusiastic, loud, and funny with dramatic flair, plenty of exclamations, playful asides, and occasional ALL-CAPS emphasis while still delivering helpful planning guidance.'
      }
    };

    const languageConfigs = {
      english: {
        label: 'English',
        instructions:
          'Always respond entirely in English, including task names, summaries, and follow-up questions.'
      },
      bulgarian: {
        label: 'Bulgarian',
        instructions:
          'Always respond entirely in Bulgarian. Translate any task details you reference or create unless explicitly told otherwise.'
      },
      german: {
        label: 'German',
        instructions:
          'Always respond entirely in German. Make sure technical terms are clear for a software development context.'
      }
    };

    function buildInstructions(preferences) {
      const behaviour = behaviourConfigs[preferences.assistantBehaviour] || behaviourConfigs.normal;
      const language = languageConfigs[preferences.language] || languageConfigs.english;

      return [
        `You are ${behaviour.persona}, an AI project manager helping ${preferences.name} plan software development work.`,
        behaviour.description,
        `Maintain awareness of ${preferences.name}'s goals and ask targeted follow-up questions to clarify requirements, edge cases, and priorities.`,
        'Provide concise action items, highlight risks, and suggest next steps that keep the project moving forward.',
        language.instructions,
        'Reference the available tools when you need to inspect, update, create, or remove tasks, and explain why you are invoking them.'
      ].join('\n');
    }

    window.initTheAssistant = function () {
      const preferences = {
        name: $('#f-name').val().trim(),
        assistantBehaviour: $('#f-assistantBehaviour').val(),
        language: $('#f-language').val()
      };

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
            description: 'Retrieve the list of available tasks so you can review the current backlog.'
          },
          {
            type: 'function',
            name: 'changePriority',
            description: 'Update the priority of a specific task when the plan needs to be adjusted.',
            parameters: {
              type: 'object',
              properties: {
                id: {type: 'integer', description: 'Identifier of the task whose priority should change.'},
                priority: {type: 'integer', description: 'New priority value that should be applied to the task.'}
              }
            }
          },
          {
            type: 'function',
            name: 'addTask',
            description: 'Create a new task for the plan when fresh work items are identified.',
            parameters: {
              type: 'object',
              properties: {
                text: {type: 'string', description: 'The task description capturing the planned work.'},
                priority: {type: 'integer', description: 'Priority value assigned to the new task.'}
              }
            }
          },
          {
            type: 'function',
            name: 'deleteTask',
            description: 'Remove a task from the backlog when it is no longer needed.',
            parameters: {
              type: 'object',
              properties: {
                id: {type: 'integer', description: 'Identifier of the task that should be removed.'}
              }
            }
          }
        ],
        instructions: buildInstructions(preferences)
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
