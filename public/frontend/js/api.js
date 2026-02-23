async function apiCall(endpoint, method = 'GET', body = null, token = null, isFormData = false) {
    let headers = {};

    // Jika bukan FormData, set Content-Type JSON
    if (!isFormData) {
        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';
    } else {
        headers['Accept'] = 'application/json';
        // Jangan set Content-Type, biarkan browser set sendiri dengan boundary
    }

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const options = {
        method,
        headers,
    };

    if (body) {
        if (isFormData) {
            options.body = body; // body sudah FormData
        } else {
            options.body = JSON.stringify(body);
        }
    }

    try {
        const response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, options);
        const data = await response.json();

        if (!response.ok) {
            // Jika endpoint login/register dan 401, itu berarti kredensial salah
            if (response.status === 401) {
                if (endpoint.includes('/login/') || endpoint === '/register') {
                    throw new Error(data.message || 'Kredensial yang diberikan salah');
                } else {
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
