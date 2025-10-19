// Jquery
import { $, jQuery } from 'jquery';

window.$ = $;
window.jQuery = jQuery;

// Bootstrap
import 'bootstrap';

import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Dropzone
import { Dropzone } from 'dropzone';

window.Dropzone = Dropzone;
window.Dropzone.autoDiscover = false;

// Quill
import Quill from 'quill';

window.Quill = Quill;

// Select2
import select2 from 'select2/dist/js/select2';

window.select2 = select2;
select2($); // Hook up select2 to jquery

// Sortable JS
import Sortable from 'sortablejs/modular/sortable.complete.esm.js';

window.Sortable = Sortable;

// Sortable JS
import flatpickr from 'flatpickr';

window.flatpickr = flatpickr;

// echarts
import * as echarts from 'echarts';

window.echarts = echarts;

// GLightbox
import GLightbox from 'glightbox';

window.GLightbox = GLightbox;
