import './bootstrap';
import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';
import { animate } from 'animejs';

// Exponer objetos globales requeridos por las vistas y scripts del sistema
window.bootstrap = bootstrap;
window.Alpine = Alpine;
window.Swal = Swal;
window.Chart = Chart;
window.animate = animate;

// Inicialización única de Alpine.js
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    animate('.caja', {
        x: '250px',
        rotate: '1turn',
        duration: 1200,
    });
});
