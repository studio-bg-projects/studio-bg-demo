@extends('layouts.app')

@section('content')
  <h1>AI Project Manager by Studio.bg</h1>

  <pre id="log"></pre>

  <script>
    class VirtualProjectManagerApp {
      constructor() {
        this.loadState();
        this.logNode = document.getElementById('log');
        this.functionHandlers = {
          getAllTasks: this.handleGetAllTasks.bind(this),
          changePriority: this.handleChangePriority.bind(this),
          addTask: this.handleAddTask.bind(this),
          deleteTask: this.handleDeleteTask.bind(this)
        };
        this.peerConnection = new RTCPeerConnection();
        this.peerConnection.ontrack = (event) => {
          const audioElement = document.createElement('audio');
          audioElement.srcObject = event.streams[0];
          audioElement.autoplay = audioElement.controls = true;
          document.body.appendChild(audioElement);
        };
        this.dataChannel = this.peerConnection.createDataChannel('oai-events');
        this.registerDataChannelEvents();
      }

      loadState() {
        this.tasks = JSON.parse(localStorage.getItem('tasks')) || {};
        const storedId = parseInt(localStorage.getItem('lastTaskId'), 10);
        this.lastId = Number.isInteger(storedId) ? storedId : Object.keys(this.tasks).length;
      }

      saveState() {
        localStorage.setItem('tasks', JSON.stringify(this.tasks));
        localStorage.setItem('lastTaskId', this.lastId.toString());
      }

      handleGetAllTasks() {
        return {
          success: true,
          tasks: this.tasks
        };
      }

      handleChangePriority({id, priority}) {
        if (this.tasks[id]) {
          this.tasks[id].priority = priority;
          this.saveState();
          return {success: true, priority};
        }
        return {success: false, error: 'Invalid task ID'};
      }

      handleAddTask({text, priority}) {
        this.lastId += 1;
        const id = this.lastId;
        this.tasks[id] = {
          id,
          text,
          priority
        };
        this.saveState();
        return {success: true, task: this.tasks[id]};
      }

      handleDeleteTask({id}) {
        if (this.tasks[id]) {
          delete this.tasks[id];
          this.saveState();
          return {success: true};
        }
        return {success: false, error: 'Invalid task ID'};
      }

      getTools() {
        return [
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
        ];
      }

      getDefaultInstructions() {
        return `
      Ти си Project Manager, аз съм Алекс. Ще ми помагаш да си планирам задачите. Ще взимаш мнение и участие в планирането. Ще ми даваш съвети и активно ще ме разпитваш за дтайли, за да съм сигурен, че създавам правилни задачи.
      Аз съм програмист и искам да планирам нещата, точно и ясно.
      `;
      }

      configureDataChannel() {
        const event = {
          type: 'session.update',
          session: {
            modalities: ['text', 'audio'],
            tools: this.getTools()
          }
        };
        this.dataChannel.send(JSON.stringify(event));
      }

      async createSession() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch('{{ route('virtual-project-manager.session') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            model: 'gpt-realtime-mini',
            instructions: this.getDefaultInstructions(),
            voice: 'ash'
          })
        });
        if (!response.ok) {
          throw new Error('Unable to create a new session.');
        }
        const result = await response.json();
        console.log('result', result);
        return result;
      }

      log(...args) {
        console.log(...args);
        if (this.logNode) {
          this.logNode.innerHTML = `${JSON.stringify(args, null, 2)}\n${this.logNode.innerHTML}`;
        }
      }

      registerDataChannelEvents() {
        this.dataChannel.addEventListener('open', (event) => {
          console.log('Opening data channel', event);
          this.configureDataChannel();
        });

        this.dataChannel.addEventListener('message', async (event) => {
          const message = JSON.parse(event.data);
          if (message.type === 'response.function_call_arguments.done') {
            await this.handleFunctionCall(message);
          }
        });
      }

      async handleFunctionCall(message) {
        const handler = this.functionHandlers[message.name];
        if (!handler) {
          return;
        }
        this.log(`Calling local function ${message.name} with ${message.arguments}`);
        const args = JSON.parse(message.arguments);
        const result = await handler(args);
        this.log('result', result);
        const event = {
          type: 'conversation.item.create',
          item: {
            type: 'function_call_output',
            call_id: message.call_id,
            output: JSON.stringify(result)
          }
        };
        this.dataChannel.send(JSON.stringify(event));
        this.dataChannel.send(JSON.stringify({type: 'response.create'}));
      }

      startConnectionAndMicrophone() {
        navigator.mediaDevices.getUserMedia({audio: true})
          .then((stream) => {
            stream.getTracks().forEach((track) => this.peerConnection.addTransceiver(track, {direction: 'sendrecv'}));
            this.peerConnection.createOffer().then((offer) => {
              this.peerConnection.setLocalDescription(offer);
              this.createSession()
                .then((data) => {
                  const EPHEMERAL_KEY = data.result.client_secret.value;
                  const baseUrl = 'https://api.openai.com/v1/realtime';
                  const model = 'gpt-realtime-mini';
                  fetch(`${baseUrl}?model=${model}`, {
                    method: 'POST',
                    body: offer.sdp,
                    headers: {
                      Authorization: `Bearer ${EPHEMERAL_KEY}`,
                      'Content-Type': 'application/sdp'
                    }
                  })
                    .then((response) => response.text())
                    .then((answer) => {
                      this.peerConnection.setRemoteDescription({
                        sdp: answer,
                        type: 'answer'
                      });
                    });
                })
                .catch((error) => {
                  this.log('Session creation failed', error.message);
                });
            });
          })
          .catch((error) => {
            this.log('Microphone access failed', error.message);
          });
      }
    }

    const projectManagerApp = new VirtualProjectManagerApp();
    projectManagerApp.startConnectionAndMicrophone();

  </script>
@endsection
