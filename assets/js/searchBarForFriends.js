document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('#query');
    const resultList = document.querySelector('#search-results');
    let timeout = null;

    // Fetch and display the initial 9 users
    fetchUsers();

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            const query = this.value;
            fetchUsers(query);
        }, 1500);
    });
});

function fetchUsers(query = '') {
    fetch(`${window.appUserSearchUrl}?query=${query}`)
        .then(response => response.json())
        .then(users => {
            displayUsers(users);
        });
}

function displayUsers(users) {
    const resultList = document.querySelector('#search-results');
    if (resultList) {
        resultList.innerHTML = '';

        users.forEach(user => {
            const card = document.createElement('div');
            card.className = 'w-full bg-white rounded-lg p-12 flex flex-col justify-center items-center';

            fetch(`/api/avatar/${user.username}`)
                .then(response => response.json())
                .then(data => {
                    card.innerHTML = `
                        <div class="mb-8">
                            <img class="object-center object-cover rounded-full h-36 w-36" src="${data.avatarUrl}" alt="photo">
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
}
