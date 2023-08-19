
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
