// Cek status login dan redirect jika perlu
function checkAuth(requiredRole = null) {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    if (!token) {
        window.location.href = '/frontend/login.html';
        return null;
    }

    if (requiredRole && user.role !== requiredRole) {
        // Redirect ke dashboard sesuai role
        if (user.role === 'taruna') window.location.href = '/frontend/dashboard/taruna.html';
        else if (user.role === 'orang_tua') window.location.href = '/frontend/dashboard/orangtua.html';
        else if (user.role === 'admin') window.location.href = '/frontend/dashboard/admin.html';
        else window.location.href = '/frontend/login.html';
        return null;
    }

    return { token, user };
}

// Logout
async function logout() {
    const token = localStorage.getItem('token');
    if (token) {
        try {
            await apiCall('/logout', 'POST', null, token);
        } catch (error) {
            console.error('Logout error:', error);
        }
    }
    localStorage.clear();
    window.location.href = '/frontend/login.html';
}

// Format tanggal
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
