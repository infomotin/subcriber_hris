document.addEventListener('DOMContentLoaded', function() {
    // Sidebar responsive toggle helper
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('vertical-menu') || document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    function toggleSidebar() {
        if (window.innerWidth < 992) {
            // Mobile toggle
            sidebar.classList.toggle('show-sidebar');
            
            // Handle overlay creation
            let overlay = document.querySelector('.sidebar-overlay');
            if (sidebar.classList.contains('show-sidebar')) {
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.className = 'sidebar-overlay';
                    document.body.appendChild(overlay);
                    
                    // Clicking overlay closes sidebar
                    overlay.addEventListener('click', function() {
                        toggleSidebar();
                    });
                }
            } else {
                if (overlay) {
                    overlay.remove();
                }
            }
        } else {
            // Desktop toggle (Collapse / Hide sidebar)
            document.body.classList.toggle('sidebar-collapsed');
        }
    }

    // Close mobile sidebar on window resize if switching to desktop view
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            if (sidebar && sidebar.classList.contains('show-sidebar')) {
                sidebar.classList.remove('show-sidebar');
            }
            const overlay = document.querySelector('.sidebar-overlay');
            if (overlay) {
                overlay.remove();
            }
        }
    });
});
