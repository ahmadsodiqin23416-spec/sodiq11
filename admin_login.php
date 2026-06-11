<?php
session_start();
// If already logged in as admin, redirect directly to admin page
if (isset($_SESSION['username']) && $_SESSION['role'] === 'admin') {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Halaman login administrator CashFlow. Masuk untuk meninjau pengajuan dan verifikasi kredit.">
    <title>Login Admin - CashFlow</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-card {
            max-width: 450px;
            margin: 6rem auto;
        }
        .auth-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-title i {
            font-size: 2.5rem;
            color: var(--accent-teal);
            margin-bottom: 0.75rem;
            display: block;
        }
    </style>
</head>
<body>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Header Navigation -->
    <header>
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-wallet logo-icon"></i> CashFlow
            </a>
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Portal Keamanan Admin</span>
        </div>
    </header>

    <main class="container">
        
        <div class="auth-card glass-card">
            <div class="auth-title">
                <i class="fa-solid fa-user-shield"></i>
                <h2>Login Administrator</h2>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">Gunakan kredensial admin untuk mengelola sistem.</p>
            </div>

            <form id="admin-login-form" onsubmit="handleAdminLogin(event)">
                <div class="form-group">
                    <label for="admin-username">Username Admin</label>
                    <input type="text" id="admin-username" class="form-control" placeholder="Masukkan username admin" required>
                </div>
                <div class="form-group">
                    <label for="admin-password">Password Security</label>
                    <input type="password" id="admin-password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">
                    Autentikasi & Masuk <i class="fa-solid fa-shield-halved"></i>
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="dashboard.php" style="font-size: 0.85rem; color: var(--text-muted);">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Portal Peminjam
                </a>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer style="position: fixed; bottom: 0; width: 100%; margin-top: 0; padding: 1.5rem 0;">
        <div class="container footer-bottom" style="margin-top: 0; padding-top: 0; border-top: none;">
            <p>&copy; 2026 CashFlow Indonesia. Hak Akses Dibatasi.</p>
        </div>
    </footer>

    <script>
        // Toast Alert Manager
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            let iconClass = 'fa-check-circle';
            if (type === 'error') iconClass = 'fa-exclamation-circle';
            if (type === 'warning') iconClass = 'fa-triangle-exclamation';

            toast.innerHTML = `
                <i class="fa-solid ${iconClass}"></i>
                <span>${message}</span>
            `;
            container.appendChild(toast);
            
            setTimeout(() => { toast.classList.add('show'); }, 50);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => { toast.remove(); }, 300);
            }, 3500);
        }

        // Login Admin
        function handleAdminLogin(e) {
            e.preventDefault();
            const username = document.getElementById('admin-username').value;
            const password = document.getElementById('admin-password').value;

            fetch('api.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type: application/json' },
                body: JSON.stringify({ username, password })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    if (res.data.role === 'admin') {
                        showToast('Autentikasi admin sukses! Membuka Portal...');
                        setTimeout(() => { window.location.href = 'admin.php'; }, 1000);
                    } else {
                        showToast('Akses ditolak. Akun Anda bukan Administrator.', 'error');
                        // Log out to clear wrong session role
                        fetch('api.php?action=logout');
                    }
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Kesalahan jaringan saat autentikasi.', 'error'));
        }
    </script>
</body>
</html>
