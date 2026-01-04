document.addEventListener("click", e => {
    if (!e.target.classList.contains("react-btn")) return;

    const wrapper = document.querySelector(".idea-reactions");
    if (wrapper.dataset.owner === "1") {
        alert("You cannot react to your own idea.");
        return;
    }

    const ideaId = wrapper.dataset.ideaId;
    const type = e.target.dataset.type;

    fetch("react.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: `idea_id=${ideaId}&reaction=${type}`
    })
    .then(res => res.json())
    .then(data => {
        document.querySelectorAll(".react-btn span").forEach(s => s.innerText = 0);
        data.forEach(r => {
            document.querySelector(
                `.react-btn[data-type="${r.reaction}"] span`
            ).innerText = r.c;
        });
    });
});
