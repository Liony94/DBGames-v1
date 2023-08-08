/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// Drowdown menu
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.querySelector('.firstButton');
    const dropdownMenu = document.querySelector('.origin-top-right');

    dropdownButton.addEventListener('click', function(event) {
        event.stopPropagation();
        dropdownMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', function() {
        dropdownMenu.classList.add('hidden');
    });

    dropdownMenu.addEventListener('click', function(event) {
        event.stopPropagation();
    });
});

