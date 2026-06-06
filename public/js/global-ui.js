/* public/js/global-ui.js */
document.addEventListener("DOMContentLoaded", function () {
    // Drawer Logic (Hamburger)
    const hamburgerBtn = document.getElementById("hamburger-btn");
    const drawer = document.getElementById("drawer");
    const drawerOverlay = document.getElementById("drawer-overlay");

    if (hamburgerBtn && drawer) {
        hamburgerBtn.addEventListener("click", function(e) {
            e.preventDefault(); e.stopPropagation();
            this.classList.toggle("is-open");
            drawer.classList.toggle("is-open");
            if (drawerOverlay) drawerOverlay.classList.toggle("is-open");
        });
    }

    if (drawerOverlay) {
        drawerOverlay.addEventListener("click", function() {
            if (hamburgerBtn) hamburgerBtn.classList.remove("is-open");
            drawer.classList.remove("is-open");
            drawerOverlay.classList.remove("is-open");
        });
    }
}); 