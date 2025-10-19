@extends('layouts.app')

@section('content')
  <h1>AI Project Manager by Studio.bg</h1>

  <form id="user-form">
    <label for="user-name">Please enter your name before starting the session:</label>
    <input type="text" id="user-name" name="user-name" placeholder="Your name" required>
    <button type="submit" id="start-session">Start Session</button>
  </form>

  <pre id="log"></pre>

  <script>
    const nameInput = document.getElementById('user-name');
    const userForm = document.getElementById('user-form');
    const startButton = document.getElementById('start-session');

    const storedName = localStorage.getItem('userName');
    if (storedName) {
      nameInput.value = storedName;
    }

    function enableNameForm() {
      nameInput.disabled = false;
      startButton.disabled = false;
    }

    function disableNameForm() {
      nameInput.disabled = true;
      startButton.disabled = true;
    }

    window.vpmNameForm = {
      enable: enableNameForm,
      disable: disableNameForm
    };

    userForm.addEventListener('submit', (event) => {
      event.preventDefault();
      const name = nameInput.value.trim();
      if (!name) {
        nameInput.focus();
        return;
      }
      localStorage.setItem('userName', name);
      window.dispatchEvent(new CustomEvent('vpm:name-submitted', {detail: {name}}));
    });
  </script>

  <script>
    // Зареждане на задачите и последното ID от localStorage
    let tasks = JSON.parse(localStorage.getItem('tasks')) || {};

    let lastId = parseInt(localStorage.getItem('lastTaskId')) || Object.keys(tasks).length;

    // Помощни функции за запис
    function saveTasks() {
      localStorage.setItem('tasks', JSON.stringify(tasks));
      localStorage.setItem('lastTaskId', lastId.toString());
    }

    const fns = {
      getAllTasks: () => {
        return {
          success: true,
          tasks
        };
      },
      changePriority: ({id, priority}) => {
        if (tasks[id]) {
          tasks[id].priority = priority;
          saveTasks();
          return {success: true, priority};
        }
        return {success: false, error: 'Invalid task ID'};
      },
      addTask: ({text, priority}) => {
        lastId++;
        const id = lastId;
        tasks[id] = {
          id,
          text,
          priority
        };
        saveTasks();
        return {success: true, task: tasks[id]};
      },
      deleteTask: ({id}) => {
        if (tasks[id]) {
          delete tasks[id];
          saveTasks();
          return {success: true};
        }
        return {success: false, error: 'Invalid task ID'};
      }
    };


    // Create a WebRTC Agent
    const peerConnection = new RTCPeerConnection();

    // On inbound audio add to page
    peerConnection.ontrack = (event) => {
      const el = document.createElement('audio');
      el.srcObject = event.streams[0];
      el.autoplay = el.controls = true;
      document.body.appendChild(el);
    };

    const dataChannel = peerConnection.createDataChannel('oai-events');

    function configureData() {
      console.log('Configuring data channel');
      const event = {
        type: 'session.update',
        session: {
          modalities: ['text', 'audio'],
          // Provide the tools. Note they match the keys in the `fns` object above
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
          ]
        }
      };
      dataChannel.send(JSON.stringify(event));
    }

    const session = async () => {
      const DEFAULT_INSTRUCTIONS = `
      Ти си Project Manager, аз съм Алекс. Ще ми помагаш да си планирам задачите. Ще взимаш мнение и участие в планирането. Ще ми даваш съвети и активно ще ме разпитваш за детайли, за да съм сигурен, че създавам правилни задачи.
      Аз съм програмист и искам да планирам нещата, точно и ясно.
      `;

      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const response = await fetch('{{ route('virtual-project-manager.session') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          model: 'gpt-realtime-mini',
          instructions: DEFAULT_INSTRUCTIONS,
          voice: 'ash'
        })
      });
      if (!response.ok) {
        throw new Error('Unable to create a new session.');
      }
      const result = await response.json();
      console.log('result', result);
      return {result};
    };

    const log = (...arg) => {
      console.log.call(arg);
      const logNode = document.getElementById('log');
      if (logNode) {
        logNode.innerHTML = `${JSON.stringify(arg, null, 2)}\n${logNode.innerHTML}`;
      }
    };

    let connectionStarted = false;

    dataChannel.addEventListener('open', (ev) => {
      console.log('Opening data channel', ev);
      configureData();
    });

    dataChannel.addEventListener('message', async (ev) => {
      const msg = JSON.parse(ev.data);
      // Handle function calls
      if (msg.type === 'response.function_call_arguments.done') {
        const fn = fns[msg.name];
        if (fn !== undefined) {
          log(`Calling local function ${msg.name} with ${msg.arguments}`);
          const args = JSON.parse(msg.arguments);
          const result = await fn(args);
          log('result', result);
          // Let OpenAI know that the function has been called and share it's output
          const event = {
            type: 'conversation.item.create',
            item: {
              type: 'function_call_output',
              call_id: msg.call_id, // call_id from the function_call message
              output: JSON.stringify(result) // result of the function
            }
          };
          dataChannel.send(JSON.stringify(event));
          // Have assistant respond after getting the results
          dataChannel.send(JSON.stringify({type: 'response.create'}));
        }
      }
    });

    function startConnection() {
      if (connectionStarted) {
        return;
      }
      connectionStarted = true;
      if (window.vpmNameForm) {
        window.vpmNameForm.disable();
      }

      navigator.mediaDevices.getUserMedia({audio: true}).then((stream) => {
        // Add microphone to PeerConnection
        stream.getTracks().forEach((track) => peerConnection.addTransceiver(track, {direction: 'sendrecv'}));

        peerConnection.createOffer().then((offer) => {
          peerConnection.setLocalDescription(offer);

          session()
            //.then((tokenResponse) => tokenResponse.json())
            .then((data) => {
              const EPHEMERAL_KEY = data.result.client_secret.value;
              const baseUrl = 'https://api.openai.com/v1/realtime';
              // const model = 'gpt-4o-realtime-preview';
              const model = 'gpt-realtime-mini';
              fetch(`${baseUrl}?model=${model}`, {
                method: 'POST',
                body: offer.sdp,
                headers: {
                  Authorization: `Bearer ${EPHEMERAL_KEY}`,
                  'Content-Type': 'application/sdp'
                }
              })
                .then((r) => r.text())
                .then((answer) => {
                  // Accept answer from Realtime WebRTC API
                  peerConnection.setRemoteDescription({
                    sdp: answer,
                    type: 'answer'
                  });
                });
            })
            .catch((error) => {
              log('Session creation failed', error.message);
            });

          // Send WebRTC Offer to Workers Realtime WebRTC API Relay
        });
      }).catch((error) => {
        connectionStarted = false;
        log('Microphone access failed', error.message);
        if (window.vpmNameForm) {
          window.vpmNameForm.enable();
        }
      });
    }

    window.addEventListener('vpm:name-submitted', () => {
      startConnection();
    });

  </script>
@endsection
