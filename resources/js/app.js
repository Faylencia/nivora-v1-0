import { bootAuthForms } from './auth-forms';
import { bootLivePages } from './live-pages';
import { bootPos } from './pos';
import { bootProducts } from './products';
import { bootPromotions } from './promotions';
import { bootCashierPromos } from './cashier-promos';
import { bootMemberPages } from './member';
import { guardDashboardRole } from './nivora-api';

function initializeThemePreference() {
    const root = document.documentElement;
    const options = document.querySelectorAll('[data-theme-option]');
    const systemPreference = window.matchMedia('(prefers-color-scheme: dark)');
    let preference = root.dataset.themePreference || 'light';

    const applyPreference = (nextPreference, persist = true) => {
        preference = nextPreference;
        const isDark = preference === 'system' ? systemPreference.matches : preference === 'dark';
        root.dataset.theme = isDark ? 'dark' : 'light';
        root.dataset.themePreference = preference;

        if (persist) {
            try {
                window.localStorage.setItem('nivora.theme', preference);
            } catch {
                // The selected theme still applies for this page when storage is unavailable.
            }
        }

        options.forEach((option) => {
            const selected = option.dataset.themeOption === preference;
            option.setAttribute('aria-pressed', String(selected));
            option.classList.toggle('is-selected', selected);
        });

        const description = document.querySelector('[data-theme-description]');
        if (description) {
            description.textContent = preference === 'system'
                ? `Mengikuti tampilan perangkat (${isDark ? 'gelap' : 'terang'}).`
                : `Tampilan ${isDark ? 'gelap' : 'terang'} aktif di situs ini.`;
        }
    };

    options.forEach((option) => {
        option.addEventListener('click', () => applyPreference(option.dataset.themeOption));
    });

    systemPreference.addEventListener('change', () => {
        if (preference === 'system') {
            applyPreference('system', false);
        }
    });

    applyPreference(preference, false);
}

function initializePublicAnimations() {
    if (!document.body.hasAttribute('data-public-layout') || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const animatedElements = document.querySelectorAll('[data-public-animate], .public-product-animate');
    if (!animatedElements.length || !('IntersectionObserver' in window)) {
        return;
    }

    document.documentElement.classList.add('public-motion-ready');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -36px 0px' });

    animatedElements.forEach((element) => observer.observe(element));
}

function initializePublicHeroCarousel() {
    const carousel = document.querySelector('[data-public-hero-carousel]');
    const image = carousel?.querySelector('[data-public-hero-image]');

    if (!carousel || !image) {
        return;
    }

    const imageSources = (carousel.dataset.heroImages || '').split('|').filter(Boolean);
    const imageLabels = (carousel.dataset.heroLabels || '').split('|');

    if (imageSources.length < 2) {
        return;
    }

    imageSources.slice(1).forEach((source) => {
        const preloadImage = new Image();
        preloadImage.src = source;
    });

    const label = carousel.querySelector('[data-public-hero-label]');
    const indicators = document.createElement('div');
    indicators.className = 'public-hero-indicators';
    indicators.setAttribute('aria-label', 'Pilih foto banner');
    indicators.setAttribute('role', 'group');
    let activeIndex = 0;
    let timer = null;

    const showSlide = (index) => {
        activeIndex = index;
        image.classList.add('is-switching');
        indicators.querySelectorAll('button').forEach((indicator, indicatorIndex) => {
            indicator.classList.toggle('is-active', indicatorIndex === activeIndex);
            indicator.setAttribute('aria-pressed', String(indicatorIndex === activeIndex));
        });

        window.setTimeout(() => {
            image.alt = imageLabels[activeIndex] || 'Pilihan produk bisnis lokal';

            if (label) {
                label.textContent = imageLabels[activeIndex] || 'Pilihan lokal';
            }

            image.src = imageSources[activeIndex];
            Promise.resolve()
                .then(() => (typeof image.decode === 'function' ? image.decode() : undefined))
                .catch(() => {})
                .finally(() => image.classList.remove('is-switching'));
        }, 260);
    };

    imageSources.forEach((source, index) => {
        const indicator = document.createElement('button');
        indicator.type = 'button';
        indicator.className = `public-hero-indicator${index === 0 ? ' is-active' : ''}`;
        indicator.setAttribute('aria-label', `Tampilkan foto ${imageLabels[index] || `pilihan ${index + 1}`}`);
        indicator.setAttribute('aria-pressed', String(index === 0));
        indicator.addEventListener('click', () => showSlide(index));
        indicators.append(indicator);
    });
    carousel.append(indicators);

    const advanceSlide = () => showSlide((activeIndex + 1) % imageSources.length);

    const startCarousel = () => {
        if (!timer) {
            timer = window.setInterval(advanceSlide, 3000);
        }
    };

    const stopCarousel = () => {
        window.clearInterval(timer);
        timer = null;
    };

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopCarousel();
        } else {
            startCarousel();
        }
    });

    startCarousel();
}

function initializePublicNavigation() {
    const header = document.querySelector('.public-site-header');
    if (!header) {
        return;
    }

    const updateScrollState = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 12);
    };

    window.addEventListener('scroll', updateScrollState, { passive: true });
    updateScrollState();
}

document.addEventListener('DOMContentLoaded', () => {
    initializeThemePreference();
    initializePublicAnimations();
    initializePublicHeroCarousel();
    initializePublicNavigation();

    if (!guardDashboardRole()) {
        return;
    }

    const mobileToggle = document.querySelector('[data-mobile-nav-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    const mobileClose = document.querySelector('[data-mobile-close]');
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');
    const sidebarDrawer = document.querySelector('[data-sidebar-drawer]');
    const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
    const cashierSidebarToggles = document.querySelectorAll('[data-cashier-sidebar-toggle]');
    const isCashierDashboard = document.body.dataset.roleDashboard === 'cashier';

    if (cashierSidebarToggles.length > 0 && isCashierDashboard) {
        let sidebarClosed = false;
        try {
            sidebarClosed = window.localStorage.getItem('nivora.cashier-sidebar-closed') === 'true';
        } catch {
            sidebarClosed = false;
        }

        const applyCashierSidebarState = (closed, persist = true) => {
            document.documentElement.classList.toggle('cashier-sidebar-closed', closed);
            cashierSidebarToggles.forEach((toggle) => {
                toggle.setAttribute('aria-expanded', String(!closed));
                toggle.setAttribute('aria-label', closed ? 'Buka sidebar kasir' : 'Tutup sidebar kasir');
                toggle.title = closed ? 'Buka sidebar' : 'Tutup sidebar';
            });

            if (persist) {
                try {
                    window.localStorage.setItem('nivora.cashier-sidebar-closed', String(closed));
                } catch {
                    // The layout still updates for the current page when storage is unavailable.
                }
            }
        };

        applyCashierSidebarState(sidebarClosed, false);
        cashierSidebarToggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
                applyCashierSidebarState(!document.documentElement.classList.contains('cashier-sidebar-closed'));
            });
        });
    }

    const closeMobileMenu = () => {
        if (mobileMenu) mobileMenu.classList.add('hidden');
        mobileToggle?.setAttribute('aria-expanded', 'false');
    };

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', () => {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden', isOpen);
            mobileToggle.setAttribute('aria-expanded', String(!isOpen));
        });

        mobileMenu.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMobileMenu));
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.public-site-header')) {
                closeMobileMenu();
                document.querySelectorAll('.public-theme-menu[open]').forEach((menu) => menu.removeAttribute('open'));
            }
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

    document.addEventListener('click', (event) => {
        const backLink = event.target.closest('[data-go-back]');
        if (!backLink || window.history.length < 2 || !document.referrer) {
            return;
        }

        try {
            if (new URL(document.referrer).origin === window.location.origin) {
                event.preventDefault();
                window.history.back();
            }
        } catch {
            return;
        }
    });

    bootAuthForms();
    bootLivePages();
    bootProducts();
    bootPromotions();
    bootCashierPromos();
    bootPos();
    bootMemberPages();
});
