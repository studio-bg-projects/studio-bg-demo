export class MyPersonalAssistant {
  constructor(options) {
    this.model = options.model;
    this.sessionUrl = options.sessionUrl;
    this.csrfToken = options.csrfToken;
    this.audioControlsNode = options.audioControlsNode;
    this.logNode = options.logNode;
    this.tools = options.tools || [];
    this.instructions = options.instructions || '';

    this.loadState();

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
      audioElement.style.height = '2rem';
      this.audioControlsNode.appendChild(audioElement);
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

  handleChangePriority({ id, priority }) {
    if (this.tasks[id]) {
      this.tasks[id].priority = priority;
      this.saveState();
      return { success: true, priority };
    }
    return { success: false, error: 'Invalid task ID' };
  }

  handleAddTask({ text, priority }) {
    this.lastId += 1;
    const id = this.lastId;
    this.tasks[id] = {
      id,
      text,
      priority
    };
    this.saveState();
    return { success: true, task: this.tasks[id] };
  }

  handleDeleteTask({ id }) {
    if (this.tasks[id]) {
      delete this.tasks[id];
      this.saveState();
      return { success: true };
    }
    return { success: false, error: 'Invalid task ID' };
  }

  setToos(tools) {
    this.toolls = tools;
  }

  setInstructions(instructions) {
    this.instructions = instructions;
  }

  configureDataChannel() {
    const event = {
      type: 'session.update',
      session: {
        modalities: ['text', 'audio'],
        tools: this.tools
      }
    };
    this.dataChannel.send(JSON.stringify(event));
  }

  async createSession() {
    const response = await fetch(this.sessionUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': this.csrfToken
      },
      body: JSON.stringify({
        model: this.model,
        instructions: this.instructions,
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
    this.dataChannel.send(JSON.stringify({ type: 'response.create' }));
  }

  startConnectionAndMicrophone() {
    navigator.mediaDevices.getUserMedia({ audio: true })
      .then((stream) => {
        stream.getTracks().forEach((track) => this.peerConnection.addTransceiver(track, { direction: 'sendrecv' }));
        this.peerConnection.createOffer().then((offer) => {
          this.peerConnection.setLocalDescription(offer);
          this.createSession()
            .then((data) => {
              console.log('data', data);

              const ephemeralKey = data.client_secret.value;
              const baseUrl = 'https://api.openai.com/v1/realtime';

              fetch(`${baseUrl}?model=${this.model}`, {
                method: 'POST',
                body: offer.sdp,
                headers: {
                  Authorization: `Bearer ${ephemeralKey}`,
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
