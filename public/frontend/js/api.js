async function apiCall(endpoint, method = 'GET', body = null, token = null) {
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const options = {
        method,
        headers,
    };

    if (body) {
        options.body = JSON.stringify(body);
    }

    try {
        const response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, options);
        const data = await response.json();

        if (!response.ok) {
            // Jika endpoint adalah login/register dan 401, itu berarti kredensial salah
            if (response.status === 401) {
                // Untuk endpoint login, jangan redirect, tapi lempar error dengan pesan dari server
                if (endpoint.includes('/login/') || endpoint === '/register') {
                    throw new Error(data.message || 'Kredensial yang diberikan salah');
                } else {
                    // Untuk endpoint terproteksi, redirect ke login
                    localStorage.clear();
                    window.location.href = '/frontend/login.html';
                    throw new Error('Sesi habis, silakan login ulang');
                }
            }
            throw new Error(data.message || `Error ${response.status}`);
        }

        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}
