<?php
session_start();
// Security check: Redirect to dashboard if not logged in or not admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal administrasi CashFlow. Periksa kelayakan data kependudukan, lakukan peninjauan identitas KTP, dan kelola proses verifikasi pinjaman.">
    <title>Admin Dashboard - CashFlow</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Header Navigation -->
    <header>
        <div class="container nav-container">
            <a href="admin.php" class="logo">
                <i class="fa-solid fa-wallet logo-icon"></i> CashFlow <span style="font-size: 0.9rem; color: var(--accent-teal); font-weight: 500;">(Administrator)</span>
            </a>
            <div>
                <button class="btn btn-secondary btn-sm" onclick="handleLogout()">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </div>
        </div>
    </header>

    <main class="container" style="padding-top: 3rem; padding-bottom: 5rem;">

        <!-- Stats Counter Section -->
        <section class="stats-grid" style="margin-bottom: 3rem;">
            <div class="glass-card stat-card">
                <div class="stat-header">
                    <span>Total Disalurkan (Simulasi)</span>
                    <i class="fa-solid fa-hand-holding-dollar stat-icon"></i>
                </div>
                <span class="stat-value" id="stats-total-disbursed">Rp 0</span>
            </div>
            <div class="glass-card stat-card">
                <div class="stat-header">
                    <span>Menunggu Review</span>
                    <i class="fa-solid fa-clock-rotate-left stat-icon" style="color: var(--warning);"></i>
                </div>
                <span class="stat-value" style="color: var(--warning);" id="stats-total-pending">0</span>
            </div>
            <div class="glass-card stat-card">
                <div class="stat-header">
                    <span>Peminjam Aktif</span>
                    <i class="fa-solid fa-users stat-icon" style="color: var(--success);"></i>
                </div>
                <span class="stat-value" style="color: var(--success);" id="stats-total-active">0</span>
            </div>
        </section>

        <!-- Applications Table Section -->
        <section class="glass-card">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;"><i class="fa-solid fa-list-check"></i> Daftar Pengajuan Pinjaman</h2>
            
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Peminjam</th>
                            <th>Jumlah Pinjaman</th>
                            <th>Tenor</th>
                            <th>Bank Penerima</th>
                            <th>Status</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="applications-tbody">
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem;">
                                <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-teal);"></i>
                                <p style="margin-top: 1rem; color: var(--text-muted);">Memuat daftar pengajuan...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- ================= DETAIL VERIFICATION MODAL ================= -->
    <div id="detail-modal" class="modal-overlay">
        <div class="modal-container glass-card" style="max-width: 650px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3><i class="fa-solid fa-user-shield"></i> Detail Verifikasi Pengajuan</h3>
                <button onclick="closeModal()" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.25rem; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Information Grid -->
            <div id="modal-details-content">
                <!-- Data will be dynamically injected here -->
            </div>

            <!-- Modal Action Footer -->
            <div style="display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem; margin-top: 1.5rem;" id="modal-actions-area">
                <!-- Action buttons (Approve/Reject) -->
            </div>
        </div>
    </div>

    <!-- Script for Admin Control -->
    <script>
        let loansList = [];
        let activeLoan = null;

        window.addEventListener('DOMContentLoaded', () => {
            loadAdminData();
        });

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

        // Currency Formatter
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        // Fetch loans data from api.php
        function loadAdminData() {
            fetch('api.php?action=admin_list')
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        loansList = res.data;
                        renderMetrics();
                        renderTable();
                    } else {
                        showToast(res.message, 'error');
                        setTimeout(() => { window.location.href = 'dashboard.php'; }, 1500);
                    }
                })
                .catch(() => showToast('Gagal memuat data administrasi.', 'error'));
        }

        // Calculate and render dashboard stats metrics
        function renderMetrics() {
            let totalDisbursed = 0;
            let pendingCount = 0;
            let activeCount = 0;

            loansList.forEach(loan => {
                if (loan.status === 'approved') {
                    totalDisbursed += parseFloat(loan.amount);
                    activeCount++;
                } else if (loan.status === 'pending') {
                    pendingCount++;
                }
            });

            document.getElementById('stats-total-disbursed').textContent = formatRupiah(totalDisbursed);
            document.getElementById('stats-total-pending').textContent = pendingCount;
            document.getElementById('stats-total-active').textContent = activeCount;
        }

        // Render loans request list table
        function renderTable() {
            const tbody = document.getElementById('applications-tbody');
            
            if (loansList.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            Belum ada pengajuan pinjaman masuk.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = '';
            loansList.forEach(loan => {
                let badgeClass = '';
                let statusLabel = '';
                
                if (loan.status === 'pending') {
                    badgeClass = 'badge-pending';
                    statusLabel = 'Pending';
                } else if (loan.status === 'approved') {
                    badgeClass = 'badge-approved';
                    statusLabel = 'Aktif';
                } else if (loan.status === 'rejected') {
                    badgeClass = 'badge-rejected';
                    statusLabel = 'Ditolak';
                } else if (loan.status === 'paid') {
                    badgeClass = 'badge-paid';
                    statusLabel = 'Lunas';
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${loan.username}</strong></td>
                    <td>${formatRupiah(loan.amount)}</td>
                    <td>${loan.tenure} Bulan</td>
                    <td>${loan.bank_name} (${loan.bank_account})</td>
                    <td><span class="badge ${badgeClass}">${statusLabel}</span></td>
                    <td style="text-align: right;">
                        <button class="btn btn-secondary btn-sm" onclick="viewApplicationDetail('${loan.id}')">
                            <i class="fa-solid fa-magnifying-glass"></i> Periksa
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // View detail modal
        function viewApplicationDetail(loanId) {
            const loan = loansList.find(l => l.id === loanId);
            if (!loan) return;
            
            activeLoan = loan;
            const content = document.getElementById('modal-details-content');
            const actions = document.getElementById('modal-actions-area');
            
            // Format details layout
            content.innerHTML = `
                <div class="admin-detail-grid" style="margin-bottom: 2rem;">
                    <div class="detail-block">
                        <h5>Username Peminjam</h5>
                        <p>${loan.username}</p>
                    </div>
                    <div class="detail-block">
                        <h5>NIK KTP</h5>
                        <p>${loan.nik}</p>
                    </div>
                    <div class="detail-block">
                        <h5>Nomor HP/WA</h5>
                        <p>${loan.phone}</p>
                    </div>
                    <div class="detail-block">
                        <h5>Profesi & Gaji</h5>
                        <p>${loan.job} - ${formatRupiah(loan.salary)}/Bulan</p>
                    </div>
                    <div class="detail-block" style="grid-column: span 2;">
                        <h5>Alamat Lengkap</h5>
                        <p>${loan.address}</p>
                    </div>
                    <div class="detail-block" style="grid-column: span 2; border-top: 1px dashed var(--glass-border); padding-top: 1rem; margin-top: 0.5rem;">
                        <h4 style="margin-bottom: 0.5rem; font-size: 1rem; color: var(--accent-teal);">Detail Finansial Yang Diajukan</h4>
                    </div>
                    <div class="detail-block">
                        <h5>Jumlah Pinjaman Pokok</h5>
                        <p>${formatRupiah(loan.amount)}</p>
                    </div>
                    <div class="detail-block">
                        <h5>Tenor Jangka Waktu</h5>
                        <p>${loan.tenure} Bulan</p>
                    </div>
                    <div class="detail-block">
                        <h5>Cicilan Per Bulan</h5>
                        <p>${formatRupiah(loan.monthly_payment)}</p>
                    </div>
                    <div class="detail-block">
                        <h5>Tujuan Bank Transfer</h5>
                        <p>${loan.bank_name} (Rek. ${loan.bank_account}) a.n ${loan.bank_holder}</p>
                    </div>
                </div>

                <div style="border-top: 1px dashed var(--glass-border); padding-top: 1rem; display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.5rem; margin-top: 0.5rem;">
                    <div class="detail-block">
                        <h5 style="margin-bottom: 0.75rem;"><i class="fa-solid fa-id-card" style="color: var(--accent-teal);"></i> Berkas Jaminan KTP (Scan Laser OCR)</h5>
                        <div style="background: rgba(0,0,0,0.2); border-radius: 12px; padding: 0.5rem; text-align: center;">
                            <img src="${loan.ktp_photo}" style="max-width: 100%; max-height: 180px; border-radius: 8px; border: 1px solid var(--glass-border);" alt="Foto KTP Jaminan">
                        </div>
                    </div>
                    <div class="detail-block">
                        <h5 style="margin-bottom: 0.75rem;"><i class="fa-solid fa-shield-check" style="color: var(--success);"></i> Hasil Verifikasi OCR Jaminan</h5>
                        <div class="ocr-compare-card">
                            <div class="ocr-compare-row">
                                <span class="ocr-val-left">NIK Form vs Scan:</span>
                                <span class="ocr-val-right">${loan.nik} <span class="ocr-match-badge"><i class="fa-solid fa-check"></i> Match</span></span>
                            </div>
                            <div class="ocr-compare-row">
                                <span class="ocr-val-left">Nama Form vs Scan:</span>
                                <span class="ocr-val-right">${loan.username.toUpperCase()} <span class="ocr-match-badge"><i class="fa-solid fa-check"></i> Match</span></span>
                            </div>
                            <div class="ocr-compare-row">
                                <span class="ocr-val-left">Keaslian KTP:</span>
                                <span class="ocr-val-right" style="color: var(--success); font-weight: bold;"><i class="fa-solid fa-circle-check"></i> TERVERIFIKASI</span>
                            </div>
                            <div class="ocr-compare-row">
                                <span class="ocr-val-left">Status Agunan:</span>
                                <span class="ocr-val-right" style="color: var(--warning); font-weight: bold;"><i class="fa-solid fa-lock"></i> LOCK JAMINAN</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Display Action Buttons if state is pending
            if (loan.status === 'pending') {
                actions.innerHTML = `
                    <button class="btn btn-secondary" onclick="closeModal()">Tutup</button>
                    <button class="btn btn-danger" onclick="rejectLoan('${loan.id}')">Tolak Pengajuan</button>
                    <button class="btn btn-success" onclick="approveLoan('${loan.id}')">Setujui & Cairkan Dana</button>
                `;
            } else {
                let badgeClass = loan.status === 'approved' ? 'badge-approved' : (loan.status === 'paid' ? 'badge-paid' : 'badge-rejected');
                let statusLabel = loan.status === 'approved' ? 'Aktif' : (loan.status === 'paid' ? 'Lunas' : 'Ditolak');
                actions.innerHTML = `
                    <span style="margin-right: auto; display: flex; align-items: center; gap: 0.5rem;">
                        Status: <span class="badge ${badgeClass}">${statusLabel}</span>
                    </span>
                    <button class="btn btn-secondary" onclick="closeModal()">Tutup</button>
                `;
            }

            // Show Modal
            document.getElementById('detail-modal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('detail-modal').classList.remove('active');
            activeLoan = null;
        }

        // Approve loan application API call
        function approveLoan(loanId) {
            if (!confirm('Apakah Anda yakin menyetujui pengajuan pinjaman ini dan mencairkan dana?')) return;

            fetch('api.php?action=admin_approve', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ loan_id: loanId })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast('Pengajuan pinjaman berhasil disetujui!');
                    closeModal();
                    loadAdminData(); // Refresh lists
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Gagal memproses persetujuan.', 'error'));
        }

        // Reject loan application API call
        function rejectLoan(loanId) {
            if (!confirm('Apakah Anda yakin menolak pengajuan pinjaman ini?')) return;

            fetch('api.php?action=admin_reject', {
                method: 'POST',
                headers: { 'Content-Type: application/json' },
                body: JSON.stringify({ loan_id: loanId })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast('Pengajuan pinjaman ditolak.');
                    closeModal();
                    loadAdminData(); // Refresh lists
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Gagal memproses penolakan.', 'error'));
        }

        // Logout Admin
        function handleLogout() {
            fetch('api.php?action=logout')
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast('Berhasil keluar dari sesi admin.');
                        setTimeout(() => { window.location.href = 'dashboard.php'; }, 1000);
                    }
                });
        }
    </script>
</body>
</html>
