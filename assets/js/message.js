document.addEventListener("DOMContentLoaded", function() {
    let conversationList = document.getElementById("conversationList");
    let messageDisplay = document.getElementById("messageDisplay");

    conversationList.addEventListener("click", function(event) {
        let conversationElement = event.target.closest("li");
        if (!conversationElement) return;

        let username = conversationElement.querySelector("h3").textContent;
        let title = conversationElement.querySelector(".title").textContent;
        let content = conversationElement.querySelector(".content").textContent;

        messageDisplay.querySelector("h3").textContent = username;
        messageDisplay.querySelector("h1").textContent = title;
        messageDisplay.querySelector("article").textContent = content;
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
            // Handle the response
            console.log(data);
        });
});
