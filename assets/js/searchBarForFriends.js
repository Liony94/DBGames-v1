document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('#query');
    const resultList = document.querySelector('#search-results');

    searchInput.addEventListener('input', function() {
        const query = this.value;

        fetch(`${window.appUserSearchUrl}?query=${query}`)
            .then(response => response.json())
            .then(users => {
                if (resultList) {
                    resultList.innerHTML = '';

                    users.forEach(user => {
                        const card = document.createElement('div');
                        card.className = 'w-full bg-white rounded-lg p-12 flex flex-col justify-center items-center';
                        card.innerHTML = `
                            <div class="mb-8">
                                <img class="object-center object-cover rounded-full h-36 w-36" src="https://cdn-icons-png.flaticon.com/256/206/206853.png" alt="photo">
                            </div>
                            <div class="text-center">
                                <p class="text-xl text-gray-700 font-bold mb-2">${user.username}</p>
                                <form class="add-friend-form" data-username="${user.username}">
                                    <button class="text-indigo-600 text-sm font-semibold" type="submit">Envoyer une demande d'ami</button>
                                </form>
                            </div>
                        `;
                        resultList.appendChild(card);
                    });

                    const addFriendForms = document.querySelectorAll('.add-friend-form');
                    addFriendForms.forEach(form => {
                        form.addEventListener('submit', function(event) {
                            event.preventDefault();
                            const username = form.getAttribute('data-username');
                            sendFriendRequest(username);
                        });
                    });
                }
            });
    });
});

function sendFriendRequest(username) {
    // Construct the URL for sending the friend request
    const url = `/user/friends/${username}`;

    // Perform the fetch request
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // Add this header for Symfony's XMLHttpRequest detection
        }
    })
        .then(response => response.text())
        .then(message => {
            // Handle the response message as needed
            console.log(message); // For example, you can log the response

            // Update the button text to "Demande envoyée"
            const forms = document.querySelectorAll('.add-friend-form');
            forms.forEach(form => {
                if (form.getAttribute('data-username') === username) {
                    const button = form.querySelector('button');
                    button.textContent = 'Demande envoyée';
                    button.disabled = true;
                }
            });

            // Show the flash message with animation
            const flashMessage = document.querySelector('.flash-message');
            flashMessage.classList.add('show');
            setTimeout(() => {
                flashMessage.classList.remove('show');
            }, 3000); // Hide the message after 3 seconds
        })
        .catch(error => {
            console.error('Error sending friend request:', error);
        });
}



