document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("nav-toggle");
    const navMenu = document.getElementById("nav-menu");

    toggleButton.addEventListener("click", function () {
        navMenu.classList.toggle("active");
    });

    document.addEventListener("click", function (event) {
        if (!navMenu.contains(event.target) && event.target !== toggleButton) {
            navMenu.classList.remove("active");
        }
    });
});