import { createAssistantHandlers } from './assistant-handlers';

export class MyPersonalAssistant {
  constructor(options) {
    this.model = options.model;
    this.sessionUrl = options.sessionUrl;
    this.csrfToken = options.csrfToken;
    this.audioControlsNode = options.audioControlsNode;
    this.logNode = options.logNode;
    this.tools = options.tools || [];
    this.instructions = options.instructions || '';
    this.voice = options.voice || 'ash';

    this.loadState();

    this.functionHandlers = createAssistantHandlers(this);

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
        voice: this.voice
      })
    });
    if (!response.ok) {
      throw new Error('Unable to create a new session.');
    }
    const result = await response.json();
    this.log('Realtime session created successfully.');
    return result;
  }

  log(message, ...details) {
    const timestamp = new Date().toLocaleTimeString();
    const formattedDetails = details
      .map((detail) => {
        if (typeof detail === 'string') {
          return detail;
        }
        try {
          return JSON.stringify(detail, null, 2);
        } catch (error) {
          return String(detail);
        }
      })
      .filter(Boolean)
      .join(' ');
    const entry = `[${timestamp}] ${message}${formattedDetails ? ` — ${formattedDetails}` : ''}`;
    if (this.logNode) {
      const entryElement = document.createElement('div');
      entryElement.textContent = entry;
      this.logNode.insertBefore(entryElement, this.logNode.firstChild);
    }
  }

  registerDataChannelEvents() {
    this.dataChannel.addEventListener('open', () => {
      this.log('Data channel opened. Configuring session tools.');
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
    this.log(`Received request to execute "${message.name}"`, message.arguments);

    const args = JSON.parse(message.arguments);
    const result = await handler(args);

    this.log(`Completed "${message.name}"`, result);

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
        this.log('Microphone access granted. Preparing audio stream.');
        stream.getTracks().forEach((track) => this.peerConnection.addTransceiver(track, { direction: 'sendrecv' }));
        this.peerConnection.createOffer().then((offer) => {
          this.peerConnection.setLocalDescription(offer);
          this.createSession()
            .then((data) => {
              this.log('Session details received from backend. Requesting realtime connection.');

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
                  this.log('Realtime connection established. Listening for tasks.');
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
