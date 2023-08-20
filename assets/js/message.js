document.addEventListener("DOMContentLoaded", function() {
    let conversationList = document.getElementById("conversationList");
    let messageDisplay = document.getElementById("messageDisplay");

    conversationList.addEventListener("click", function(event) {
        let conversationElement = event.target.closest("li");
        if (!conversationElement) return;

        let conversationId = conversationElement.getAttribute("data-conversation-id");
        localStorage.setItem('lastConversationId', conversationId);
        messageDisplay.querySelector("h3").textContent = conversationElement.querySelector("h3").textContent;

        fetch(`/message/conversation/${conversationId}/messages`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let messagesHtml = '';
                    data.messages.forEach(message => {
                        messagesHtml += `
                            <div class="message">
                                <strong>${message.sender}:</strong>
                                <p>${message.content}</p>
                                <small>${message.sentAt}</small>
                            </div>
                        `;
                    });
                    messageDisplay.querySelector("article").innerHTML = messagesHtml;
                } else {
                    console.error(data.message);
                }
            });

        fetch(`/message/conversation/${conversationId}/mark-as-read`, {
            method: "POST",
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    conversationElement.classList.remove('bg-yellow-100');
                    let unreadCountElement = conversationElement.querySelector('.text-md.bg-red-500.text-white.rounded-full.px-2.py-1.ml-2');
                    if (unreadCountElement) {
                        unreadCountElement.remove();
                    }
                } else {
                    console.error(data.message);
                }
            });
    });

    document.getElementById("sendButton").addEventListener("click", function(event) {
        event.preventDefault();

        const recipientId = document.getElementById("recipient").value;
        const message = document.getElementById("message").value;
        const title = document.getElementById("title").value;

        fetch(`/message/send/${recipientId}`, {
            method: "POST",
            body: JSON.stringify({ message: message, title: title }),
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                location.reload();
            });
    });

    document.getElementById("replyButton").addEventListener("click", function(event) {
        event.preventDefault();

        const replyText = document.getElementById("replyText").value;
        const conversationIdElement = document.getElementById("conversationId");

        if (!conversationIdElement) {
            console.error("Element with ID 'conversationId' not found");
            return;
        }

        const conversationId = conversationIdElement.value;

        if (!conversationId) {
            console.error("No conversation ID provided");
            return;
        }

        fetch(`/message/reply/${conversationId}`, {
            method: "POST",
            body: JSON.stringify({ reply: replyText }),
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                location.reload();
            });
    });

    let lastConversationId = localStorage.getItem('lastConversationId');
    if (lastConversationId) {
        let conversationElement = document.querySelector(`[data-conversation-id="${lastConversationId}"]`);
        if (conversationElement) {
            conversationElement.click();
        }
    }
});
