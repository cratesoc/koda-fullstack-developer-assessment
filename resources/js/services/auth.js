const csrf = async () => {
    await fetch('/sanctum/csrf-cookie', {
        credentials: 'include',
        headers: {
            Accept: 'application/json',
        },
    });
};

export const login = async (email, password, remember = false) => {
    await csrf();

    const response = await fetch('/api/login', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({
            email,
            password,
            remember,
        }),
    });

    const data = await response.json();

    if (!response.ok) {
        throw data;
    }

    return data;
};

export const register = async (name, email, password, password_confirmation) => {
    await csrf();

    const response = await fetch('/api/register', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: JSON.stringify({
            name,
            email,
            password,
            password_confirmation,
        }),
    });

    const data = await response.json();

    if (!response.ok) {
        throw data;
    }

    return data;
};

export const getUser = async () => {
    const response = await fetch('/api/user', {
        credentials: 'include',
        headers: {
            Accept: 'application/json',
        },
    });

    if (!response.ok) {
        return null;
    }

    const data = await response.json();

    return data.user;
};

export const logout = async () => {
    const response = await fetch('/api/logout', {
        method: 'POST',
        credentials: 'include',
        headers: {
            Accept: 'application/json',
        },
    });

    if (!response.ok) {
        throw new Error('Unable to logout');
    }
};