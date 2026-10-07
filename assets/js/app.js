// ===== RESPONSIVE SIDEBAR TOGGLE =====
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('sidebarToggle');
    var sidebar   = document.getElementById('sidebarMenu');
    var overlay   = document.getElementById('sidebarOverlay');

    if (!toggleBtn || !sidebar || !overlay) return;

    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.classList.add('sidebar-open');
    }
    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }
    function toggleSidebar() {
        sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
    }

    toggleBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar automatically when a menu link is tapped (mobile/tablet)
    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 992) closeSidebar();
        });
    });

    // Reset state when resizing back to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 992) closeSidebar();
    });
});
