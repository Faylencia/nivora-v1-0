import { apiRequest, clearSession, getSession, saveSession } from './nivora-api';

function destinationAfterLogin(user) {
    const requestedPath = new URLSearchParams(window.location.search).get('redirect');

    if (requestedPath) {
        const destination = new URL(requestedPath, window.location.origin);
        if (destination.origin === window.location.origin) {
            return `${destination.pathname}${destination.search}${destination.hash}`;
        }
    }

    const roles = user?.roles?.map((role) => role.name) || [];

    if (roles.includes('super_admin')) {
        return '/super-admin';
    }
    if (roles.includes('owner')) {
        return '/owner';
    }
    if (roles.includes('cashier')) {
        return '/cashier/pos';
    }
    if (roles.includes('member')) {
        return '/';
    }

    return '/member';
}

function showAuthMessage(form, message) {
    const container = form.querySelector('[data-auth-message]');
    container.textContent = message;
    container.classList.remove('hidden');
}

function dashboardPathForUser(user) {
    const roles = user?.roles?.map((role) => role.name) || [];

    if (roles.includes('super_admin')) {
        return '/super-admin';
    }
    if (roles.includes('owner')) {
        return '/owner';
    }
    if (roles.includes('cashier')) {
        return '/cashier/pos';
    }

    return '/member';
}

function bootAuthenticatedNavigation() {
    let session = getSession();
    if (session?.expiresAt && session.expiresAt <= Math.floor(Date.now() / 1000)) {
        clearSession();
        session = null;
    }

    document.querySelectorAll('[data-auth-guest]').forEach((element) => {
        element.classList.toggle('hidden', Boolean(session));
    });
    document.querySelectorAll('[data-authenticated-nav]').forEach((element) => {
        element.classList.toggle('hidden', !session);
    });

    if (!session) {
        return;
    }

    document.querySelectorAll('[data-session-user]').forEach((element) => {
        element.textContent = session.user?.name || 'Akun aktif';
    });
    document.querySelectorAll('[data-session-dashboard]').forEach((link) => {
        link.href = dashboardPathForUser(session.user);
        const isMember = session.user?.roles?.some((role) => role.name === 'member');
        link.textContent = isMember ? 'Dashboard Member' : 'Dashboard';
    });

    document.querySelector('[data-home-guest-callout]')?.classList.add('hidden');
    document.querySelector('[data-home-authenticated-callout]')?.classList.remove('hidden');

    const hasMemberRole = session.user?.roles?.some((role) => role.name === 'member');
    const sidebar = document.querySelector('[data-member-public-sidebar]');
    const content = document.querySelector('[data-public-content]');

    const isMemberPublicHome = Boolean(document.querySelector('[data-member-public-home]'));

    if (hasMemberRole && sidebar && content && !isMemberPublicHome) {
        sidebar.classList.remove('hidden');
        content.classList.add('lg:pl-72');
        document.querySelector('[data-member-sidebar-trigger]')?.classList.remove('hidden');
        document.querySelector('[data-mobile-nav-toggle]')?.classList.add('hidden');
    }
}

function initializeAuthForm(form) {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = form.querySelector('[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Memproses…';

        try {
            const body = Object.fromEntries(new FormData(form).entries());
            const endpoint = form.dataset.authForm === 'register' ? '/auth/register' : '/auth/login';
            const response = await apiRequest(endpoint, {
                method: 'POST',
                body: JSON.stringify(body),
                authenticated: false,
            });
            const session = saveSession(response.data);
            const destination = form.dataset.authForm === 'register' ? '/' : destinationAfterLogin(session.user);

            window.location.assign(destination);
        } catch (error) {
            showAuthMessage(form, error.message || 'Tidak dapat masuk. Periksa kembali data yang diisi.');
            submitButton.disabled = false;
            submitButton.textContent = form.dataset.authForm === 'register' ? 'Buat akun member' : 'Masuk';
        }
    });
}

export function bootAuthForms() {
    bootAuthenticatedNavigation();

    const session = getSession();
    if (session?.user?.name) {
        document.querySelectorAll('[data-session-user]').forEach((element) => {
            element.textContent = session.user.name;
        });
    }

    document.querySelectorAll('[data-auth-form]').forEach(initializeAuthForm);

    document.querySelectorAll('[data-auth-logout]').forEach((link) => {
        link.addEventListener('click', async (event) => {
            event.preventDefault();
            const session = getSession();

            if (session?.token) {
                try {
                    await apiRequest('/auth/logout', { method: 'POST' });
                } catch {
                    clearSession();
                }
            }

            clearSession();
            window.location.assign('/login');
        });
    });
}
