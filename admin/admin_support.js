function openTicket(ticketId) {
    const modal = document.getElementById("ticketModal");
    const convo = document.getElementById("ticketConversation");
    const input = document.getElementById("ticketId");

    if (!modal || !convo || !input) {
        console.error("Admin support modal elements missing");
        return;
    }

    // Show modal
    modal.style.display = "flex";
    input.value = ticketId;

    // Load ticket conversation
    fetch("support_conversation.php?id=" + ticketId)
        .then(res => res.text())
        .then(html => {
            convo.innerHTML = html;
        })
        .catch(err => {
            convo.innerHTML = "<p>Error loading conversation.</p>";
            console.error(err);
        });
}

function closeTicket() {
    document.getElementById("ticketModal").style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("replyForm");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const data = new FormData(form);

        fetch("support_reply.php", {
            method: "POST",
            body: data
        })
        .then(res => res.text())
        .then(() => {
            openTicket(document.getElementById("ticketId").value);
            form.reset();
        })
        .catch(err => console.error(err));
    });
});
