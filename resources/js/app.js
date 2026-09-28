document.addEventListener('DOMContentLoaded', () => {
    const mobileToggle = document.querySelector('[data-mobile-nav-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    const mobileClose = document.querySelector('[data-mobile-close]');
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');
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
        sidebarToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));
    };

    const openSidebar = () => {
        if (sidebarDrawer) sidebarDrawer.classList.remove('-translate-x-full');
        if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');
        sidebarToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'true'));
    };

    if (sidebarToggles.length > 0 && sidebarDrawer) {
        sidebarToggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const isOpen = !sidebarDrawer.classList.contains('-translate-x-full');
                if (isOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    document.querySelectorAll('[data-open-modal]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = document.getElementById(trigger.dataset.openModal);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = document.getElementById(trigger.dataset.closeModal);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileMenu();
            closeSidebar();
        }
    });
});
