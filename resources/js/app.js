import './bootstrap';
import Alpine from 'alpinejs';
import { animate } from 'animejs';

window.Alpine = Alpine;
window.animate = animate;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    animate('.caja', {
        x: '250px',
        rotate: '1turn',
        duration: 1200,
    });
});
