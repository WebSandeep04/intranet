   document.getElementById("toggleSidebar").addEventListener("click", function () {
    const sidebar = document.querySelector(".sidebar");
    const content = document.querySelector(".content");

    sidebar.classList.toggle("collapsed");
    content.classList.toggle("expanded");
});






