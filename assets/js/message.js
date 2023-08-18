document.addEventListener('DOMContentLoaded', function() {
    const newMessageButton = document.getElementById('newMessageButton');
    const newMessageModal = document.getElementById('newMessageModal');

    newMessageButton.addEventListener('click', function(event) {
        event.preventDefault();
        newMessageModal.style.display = 'block';
    });

    newMessageModal.addEventListener('click', function(event) {
        if (event.target === newMessageModal) {
            newMessageModal.style.display = 'none';
        }
    });

    const messages = document.querySelectorAll('.message');
    const messageContent = document.querySelector('.message-content');
    const messageUsername = document.querySelector('.message-username');
    const messageEmail = document.querySelector('.message-email');
    const messageTitle = document.getElementById('message-title');

    messages.forEach(function(message) {
        message.addEventListener('click', function(event) {
            event.preventDefault();
            messageContent.textContent = event.currentTarget.getAttribute('data-content');
            messageUsername.textContent = event.currentTarget.getAttribute('data-username');
            messageEmail.textContent = event.currentTarget.getAttribute('data-email');
            messageTitle.textContent = event.currentTarget.getAttribute('data-title');
        });
    });
});

$(document).ready(function() {
    $('#recipient').select2();
});
