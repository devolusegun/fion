/* ===============================
   SUPPORT MODALS CONTROLLER
================================ */

function openReadModal(ticketId) {
    const modal = document.getElementById("readModal");
    const content = document.getElementById("readText");

    if (!modal || !content) return;

    modal.style.display = "flex";
    content.innerHTML = "<div class='loading'>Loading conversation…</div>";

    fetch("ticket_conversation.php?id=" + ticketId)
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;

            // Auto-scroll to latest message
            content.scrollTop = content.scrollHeight;
        })
        .catch(() => {
            content.innerHTML = "<p class='error'>Unable to load conversation.</p>";
        });
}

function openReplyModal(ticketId) {
    const modal = document.getElementById("replyModal");
    const input = document.getElementById("replyTicketID");

    if (!modal || !input) return;

    input.value = ticketId;
    modal.style.display = "flex";
}

function closeModal() {
    const readModal = document.getElementById("readModal");
    const replyModal = document.getElementById("replyModal");

    if (readModal) readModal.style.display = "none";
    if (replyModal) replyModal.style.display = "none";
}

/* ===============================
   UX ENHANCEMENTS
================================ */

// Close modal when clicking outside content
window.addEventListener("click", function (e) {
    if (e.target.classList.contains("modal")) {
        closeModal();
    }
});

// Close modal on ESC key
window.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        closeModal();
    }
});
