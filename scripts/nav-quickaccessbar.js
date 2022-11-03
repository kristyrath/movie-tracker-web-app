

// COLLAPSIBLE NAV AND QUICK ACCESS BAR
// appears and collapses based on click
const nav = document.querySelector(".fa-bars");
const quickAccessBar = document.querySelector("#quickaccess-bar");

window.addEventListener("DOMContentLoaded", () => {


    // remove class hidden if it exists, else do the opposite
    quickAccessBar.addEventListener("click", () => {
        const quickAccessContent = document.querySelector(".quickaccess-acc-content");
        !quickAccessContent.classList.contains("hidden") ? quickAccessContent.classList.add("hidden") : quickAccessContent.classList.remove("hidden");
    });

    // remove class hidden if it exists, else do the opposite
    nav.addEventListener("click", () => {
        const navContent = document.querySelector(".nav-content");
        !navContent.classList.contains("hidden") ? navContent.classList.add("hidden") : navContent.classList.remove("hidden");
    });

});