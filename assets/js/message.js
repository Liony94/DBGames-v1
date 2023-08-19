document.addEventListener('DOMContentLoaded', function() {
    const newMessageButton = document.getElementById('newMessageButton');
    const newMessageModal = document.getElementById('newMessageModal');
    const parentInput = document.getElementById('parent_id');
    const recipientInput = document.getElementById('reply_recipient_id');
    const replyForm = document.getElementById('replyForm');

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
    const messageTitle = document.querySelector('.message-title');

    messages.forEach(function(message) {
        message.addEventListener('click', function(event) {
            event.preventDefault();
            messageContent.textContent = event.currentTarget.getAttribute('data-content');
            messageUsername.textContent = event.currentTarget.getAttribute('data-username');
            messageEmail.textContent = event.currentTarget.getAttribute('data-email');
            messageTitle.textContent = event.currentTarget.getAttribute('data-title');

            parentInput.value = event.currentTarget.getAttribute('data-id');
            recipientInput.value = event.currentTarget.getAttribute('data-sender-id');

            replyForm.style.display = 'block';
        });
    });
});

$(document).ready(function() {
    $('#recipient').select2();
});
