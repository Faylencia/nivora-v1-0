const sessionKey = 'nivora.session';

export function getSession() {
    try {
        const session = JSON.parse(window.localStorage.getItem(sessionKey) || 'null');
        return session?.token ? session : null;
    } catch {
        window.localStorage.removeItem(sessionKey);
        return null;
    }
}

export function saveSession(authData) {
    const user = authData.user;
    const assignment = user?.roles?.[0] ?? null;
    const business = authData.business ?? null;
    const session = {
        token: authData.token,
        expiresAt: Number(authData.expires_at),
        user,
        businessId: business?.id ?? assignment?.business_id ?? null,
        businessName: business?.name ?? null,
    };

    window.localStorage.setItem(sessionKey, JSON.stringify(session));

    return session;
}

export function clearSession() {
    window.localStorage.removeItem(sessionKey);
}

export function loginUrl() {
    const returnTo = `${window.location.pathname}${window.location.search}`;

    return `/login?redirect=${encodeURIComponent(returnTo)}`;
}

export function requireSession() {
    const session = getSession();
    const expired = session?.expiresAt && session.expiresAt <= Math.floor(Date.now() / 1000);

    if (!session || expired) {
        clearSession();
        window.location.assign(loginUrl());

        return null;
    }

    return session;
}

export function guardDashboardRole() {
    const dashboard = document.querySelector('[data-role-dashboard]');
    if (!dashboard) {
        return true;
    }

    const session = requireSession();
    if (!session) {
        return false;
    }

    const roles = session.user?.roles?.map((role) => role.name) || [];
    const expectedRole = dashboard.dataset.roleDashboard;
    if (roles.includes('super_admin') || roles.includes(expectedRole)) {
        return true;
    }

    const destination = roles.includes('owner') ? '/owner'
        : roles.includes('cashier') ? '/cashier/pos'
            : '/member';
    window.location.replace(destination);

    return false;
}

export async function apiRequest(path, options = {}) {
    const { authenticated = true, ...requestOptions } = options;
    const session = getSession();
    const headers = new Headers(requestOptions.headers || {});
    headers.set('Accept', 'application/json');

    if (requestOptions.body && !(requestOptions.body instanceof FormData)) {
        headers.set('Content-Type', 'application/json');
    }

    if (authenticated && session?.token) {
        headers.set('Authorization', `Bearer ${session.token}`);
        if (session.businessId) {
            headers.set('X-Business-ID', String(session.businessId));
        }
    }

    const response = await fetch(`/api/v1${path}`, {
        ...requestOptions,
        headers,
        credentials: 'same-origin',
    });
    const payload = await response.json().catch(() => null);

    if (response.status === 401 && authenticated && session?.token) {
        clearSession();
        window.location.assign(loginUrl());
    }

    if (!response.ok) {
        const validationMessage = Object.values(payload?.errors || {}).flat()[0];
        const error = new Error(validationMessage || payload?.message || 'Permintaan ke server gagal.');
        error.status = response.status;
        error.payload = payload;
        throw error;
    }

    return payload;
}

export function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value || 0));
}

export function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[character]);
}

export function pagedItems(payload) {
    return payload?.data?.items || [];
}

export function apiMessage(error) {
    if (error?.status === 401) {
        return 'Sesi login berakhir. Silakan masuk kembali.';
    }

    if (error?.status === 403) {
        return 'Akun ini tidak memiliki akses untuk tindakan tersebut.';
    }

    return error?.message || 'Terjadi kesalahan. Coba lagi.';
}
