document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("dark-mode-toggle");
    const body = document.body;
    const isDarkMode = localStorage.getItem("dark-mode") === "enabled";

    if (isDarkMode) {
        body.classList.add("dark-mode");
        toggleButton.textContent = "☀️ Light Mode";
    }

    toggleButton.addEventListener("click", function () {
        body.classList.add("transitioning");
        setTimeout(() => {
            if (body.classList.contains("dark-mode")) {
                body.classList.remove("dark-mode");
                localStorage.setItem("dark-mode", "disabled");
                toggleButton.textContent = "🌙 Dark Mode";
            } else {
                body.classList.add("dark-mode");
                localStorage.setItem("dark-mode", "enabled");
                toggleButton.textContent = "☀️ Light Mode";
            }
            body.classList.remove("transitioning");
        }, 300);
    });
});