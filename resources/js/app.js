document.addEventListener('DOMContentLoaded', () => {
    const mobileToggle = document.querySelector('[data-mobile-nav-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    const mobileClose = document.querySelector('[data-mobile-close]');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebarDrawer = document.querySelector('[data-sidebar-drawer]');
    const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');

    const closeMobileMenu = () => {
        if (mobileMenu) mobileMenu.classList.add('hidden');
    };

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    if (mobileClose) {
        mobileClose.addEventListener('click', closeMobileMenu);
    }

    const closeSidebar = () => {
        if (sidebarDrawer) sidebarDrawer.classList.add('-translate-x-full');
        if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
    };

    const openSidebar = () => {
        if (sidebarDrawer) sidebarDrawer.classList.remove('-translate-x-full');
        if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');
    };

    if (sidebarToggle && sidebarDrawer) {
        sidebarToggle.addEventListener('click', () => {
            const isOpen = !sidebarDrawer.classList.contains('-translate-x-full');
            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileMenu();
            closeSidebar();
        }
    });
});
