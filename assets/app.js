import './styles/app.css';

document.addEventListener('DOMContentLoaded', function() {

    const dropdownButtons = document.querySelectorAll('.firstButton, .dropdown-button');

    dropdownButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();

            const associatedDropdown = this.nextElementSibling;

            if (!associatedDropdown.classList.contains('hidden')) {
                associatedDropdown.classList.add('hidden');
            } else {
                const allDropdownMenus = document.querySelectorAll('.dropdown-menu, .origin-top-right');
                allDropdownMenus.forEach(menu => {
                    menu.classList.add('hidden');
                });
                associatedDropdown.classList.remove('hidden');
            }
        });
    });

    document.addEventListener('click', function() {
        const allDropdownMenus = document.querySelectorAll('.dropdown-menu, .origin-top-right');
        allDropdownMenus.forEach(menu => {
            menu.classList.add('hidden');
        });
    });
});



