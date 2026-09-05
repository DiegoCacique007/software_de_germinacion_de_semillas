import './bootstrap';
import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

// Exponer objetos globales requeridos por las vistas y scripts del sistema
window.bootstrap = bootstrap;
window.Alpine = Alpine;
window.Swal = Swal;
window.Chart = Chart;

// Registrar plugins de Alpine antes de iniciar
Alpine.plugin(collapse);

// Inicialización única de Alpine.js
Alpine.start();
