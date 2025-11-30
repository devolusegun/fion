// Dark/light mode toggle
const modeBtn = document.getElementById("modeToggle");
modeBtn.addEventListener("click", () => {
    document.body.classList.toggle("dark");
    modeBtn.textContent = document.body.classList.contains("dark") ? "☀️" : "🌙";
});
