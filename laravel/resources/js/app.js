import './bootstrap';
import './app/theme.js';

import { Uploader } from './app/uploader.js';
import { MicBallVisualizer } from '../modules/virtual-project-manager/mic-ball-visualizer.js';
import { MyPersonalAssistant } from '../modules/virtual-project-manager/my-personal-assistant.js';

window.Uploader = Uploader;

// Modules
window.MicBallVisualizer = MicBallVisualizer;
window.MyPersonalAssistant = MyPersonalAssistant;
