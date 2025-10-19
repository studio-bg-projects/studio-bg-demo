export class MicBallVisualizer {
  constructor(container, opts = {}) {
    this.el = typeof container === 'string' ? document.getElementById(container) : container;
    if (!this.el) {
      throw new Error('Container not found');
    }

    // Опции (можеш да сменяш цветове/интензитет)
    const d = {
      bg: 'transparent',
      colorInner: '#7c3aed',     // център
      colorOuter: '#60a5fa',     // ръб
      glow: true,
      fftSize: 2048,
      smoothing: 0.85,           // аудио изглаждане
      idleBreath: 0.015,         // амплитуда на „дишане“, когато е тихо
      minRadius: 60,             // px при тишина
      maxRadius: 120,            // px при силен звук
      spring: 0.18,              // колко бързо гони целевия радиус
      damping: 0.15,             // колко бързо се успокоява
      outline: true
    };
    this.o = { ...d, ...opts };

    // Canvas
    this.canvas = document.createElement('canvas');
    this.ctx = this.canvas.getContext('2d');
    Object.assign(this.canvas.style, { width: '100%', height: '100%', display: 'block' });
    this.el.innerHTML = '';
    this.el.appendChild(this.canvas);

    // Състояние
    this.stream = null;
    this.audioCtx = null;
    this.analyser = null;
    this.timeData = null;
    this.running = false;

    // “Физика” на радиуса
    this.r = this.o.minRadius;
    this.rv = 0; // radial velocity

    // bind
    this._onResize = this._onResize.bind(this);
    this._loop = this._loop.bind(this);

    window.addEventListener('resize', this._onResize);
    this._onResize();
    this._rebuildGradient();
  }

  _onResize() {
    const dpr = Math.max(1, window.devicePixelRatio || 1);
    const rect = this.el.getBoundingClientRect();
    this.canvas.width = Math.floor(rect.width * dpr);
    this.canvas.height = Math.floor(rect.height * dpr);
    this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    this._rebuildGradient();
  }

  _rebuildGradient() {
    const { width, height } = this.canvas;
    const w = width / (window.devicePixelRatio || 1);
    const h = height / (window.devicePixelRatio || 1);
    const cx = w / 2, cy = h / 2;
    const maxR = Math.hypot(w, h) / 2;

    const g = this.ctx.createRadialGradient(cx, cy, 0, cx, cy, maxR);
    g.addColorStop(0, this.o.colorInner);
    g.addColorStop(1, this.o.colorOuter);
    this.gradient = g;
  }

  async _setupAudio() {
    if (this.analyser) {
      return;
    }
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    this.stream = stream;
    this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const source = this.audioCtx.createMediaStreamSource(stream);
    const analyser = this.audioCtx.createAnalyser();
    analyser.fftSize = this.o.fftSize;
    analyser.smoothingTimeConstant = this.o.smoothing;
    source.connect(analyser);

    this.analyser = analyser;
    this.timeData = new Uint8Array(analyser.fftSize);
  }

  _volumeRMS() {
    // RMS от time-domain (по-надеждно за „глас“)
    this.analyser.getByteTimeDomainData(this.timeData);
    let sum = 0;
    for (let i = 0; i < this.timeData.length; i++) {
      const v = (this.timeData[i] - 128) / 128; // -1..1
      sum += v * v;
    }
    const rms = Math.sqrt(sum / this.timeData.length); // 0..~1
    return rms;
  }

  async start() {
    try {
      await this._setupAudio();
      if (this.audioCtx.state === 'suspended') {
        await this.audioCtx.resume();
      }
      this.running = true;
      this._loop();
    } catch (e) {
      console.error(e);
      this._drawError('Нужен е достъп до микрофона.');
    }
  }

  stop() {
    this.running = false;
    if (this.stream) {
      this.stream.getTracks().forEach(t => t.stop());
    }
    // няма да затваряме audioCtx, за да може да се стартира пак бързо
  }

  destroy() {
    this.stop();
    window.removeEventListener('resize', this._onResize);
    this.el.innerHTML = '';
  }

  _targetRadius(vol, t) {
    // Меко „дишане“, когато vol е нисък
    const breath = Math.sin(t * 2 * Math.PI * 0.6) * this.o.idleBreath;
    const v = Math.max(vol, 0.02) + breath;        // 0..~0.2 при говор
    const clamped = Math.max(0, Math.min(1, (v - 0.02) / 0.25)); // нормализиране
    return this.o.minRadius + (this.o.maxRadius - this.o.minRadius) * clamped;
  }

  _loop(ts) {
    if (!this.running) {
      return;
    }
    const ctx = this.ctx;
    const w = this.canvas.clientWidth;
    const h = this.canvas.clientHeight;
    ctx.clearRect(0, 0, w, h);

    // фон
    if (this.o.bg !== 'transparent') {
      ctx.fillStyle = this.o.bg;
      ctx.fillRect(0, 0, w, h);
    }

    // целеви радиус според RMS
    const vol = this._volumeRMS();
    const t = (ts || 0) / 1000;
    const target = this._targetRadius(vol, t);

    // пружина (spring) за гладко догонване
    const force = (target - this.r) * this.o.spring;
    this.rv = (this.rv + force) * (1 - this.o.damping);
    this.r += this.rv;

    // рисуване на „меката“ топка (радиален градиент, глоу, ореол)
    const cx = w / 2, cy = h / 2;
    if (this.o.glow) {
      ctx.save();
      ctx.filter = 'blur(18px)';
      ctx.globalAlpha = 0.9;
      ctx.fillStyle = this.gradient;
      ctx.beginPath();
      ctx.arc(cx, cy, this.r * 0.95, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    }

    // основна сфера
    ctx.save();
    ctx.fillStyle = this.gradient;
    ctx.beginPath();
    ctx.arc(cx, cy, this.r, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();

    // деликатни „вълни“ при силен звук
    const waves = Math.min(4, Math.floor((vol - 0.05) * 20));
    for (let i = 1; i <= waves; i++) {
      const alpha = 0.18 - i * 0.03;
      if (alpha <= 0) {
        break;
      }
      ctx.beginPath();
      ctx.arc(cx, cy, this.r + i * 10, 0, Math.PI * 2);
      ctx.strokeStyle = `rgba(124,58,237,${alpha})`;
      ctx.lineWidth = 2;
      ctx.stroke();
    }

    // фина бяла точка за „живост“
    ctx.beginPath();
    ctx.fillStyle = 'rgba(255,255,255,0.9)';
    ctx.arc(cx - this.r * 0.35, cy - this.r * 0.35, Math.max(2, this.r * 0.06), 0, Math.PI * 2);
    ctx.fill();

    // опционален контур
    if (this.o.outline) {
      ctx.beginPath();
      ctx.arc(cx, cy, this.r, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(255,255,255,0.25)';
      ctx.lineWidth = 1;
      ctx.stroke();
    }

    requestAnimationFrame(this._loop);
  }

  _drawError(msg) {
    const ctx = this.ctx;
    ctx.clearRect(0, 0, this.canvas.clientWidth, this.canvas.clientHeight);
    ctx.fillStyle = '#fff';
    ctx.font = '14px system-ui, -apple-system, Segoe UI, Roboto';
    ctx.fillText(msg, 10, 24);
  }
}
