<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard portal peminjam CashFlow. Ajukan pinjaman mikro, pantau status persetujuan, dan lakukan pembayaran cicilan secara terenkripsi 256-bit SSL.">
    <meta name="keywords" content="portal peminjam, dashboard cashflow, bayar cicilan, ajukan pinjaman online">
    <title>Dashboard CashFlow - Pinjaman Online Terpercaya</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Extra inline dashboard page specific styles */
        .auth-card {
            max-width: 450px;
            margin: 5rem auto;
        }
        .auth-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 2rem;
            padding-bottom: 0.5rem;
        }
        .auth-tab {
            text-align: center;
            padding: 0.75rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-muted);
            transition: var(--transition);
        }
        .auth-tab.active {
            color: var(--text-white);
            border-bottom: 2px solid var(--primary);
        }
        .auth-form {
            display: none;
        }
        .auth-form.active {
            display: block;
        }
    </style>
</head>
<body>

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Header Navigation (Minimal for Dashboard) -->
    <header>
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-wallet logo-icon"></i> CashFlow
            </a>
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Portal Pengguna Aman</span>
        </div>
    </header>

    <main class="container">
        
        <!-- ================= AUTHENTICATION CONTAINER (LOGIN/REGISTER) ================= -->
        <div id="auth-section" class="auth-card glass-card" style="display: none;">
            <div class="auth-tabs">
                <div class="auth-tab active" onclick="switchAuthTab('login')">Masuk</div>
                <div class="auth-tab" onclick="switchAuthTab('register')">Daftar Akun</div>
            </div>

            <!-- Login Form -->
            <form id="login-form" class="auth-form active" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label for="login-username">Username</label>
                    <input type="text" id="login-username" class="form-control" placeholder="Masukkan username Anda" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" class="form-control" placeholder="Masukkan password Anda" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Masuk Ke Dashboard <i class="fa-solid fa-right-to-bracket"></i>
                </button>
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="admin_login.php" style="font-size: 0.85rem; color: var(--text-muted);">
                        <i class="fa-solid fa-user-shield"></i> Masuk Sebagai Administrator
                    </a>
                </div>
            </form>

            <!-- Register Form -->
            <form id="register-form" class="auth-form" onsubmit="handleRegister(event)">
                <div class="form-group">
                    <label for="reg-name">Nama Lengkap (Sesuai KTP)</label>
                    <input type="text" id="reg-name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="form-group">
                    <label for="reg-username">Username</label>
                    <input type="text" id="reg-username" class="form-control" placeholder="Min. 3 karakter" required>
                </div>
                <div class="form-group">
                    <label for="reg-password">Password</label>
                    <input type="password" id="reg-password" class="form-control" placeholder="Min. 6 karakter" required>
                </div>
                <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 1rem;">
                    Registrasi Akun <i class="fa-solid fa-user-plus"></i>
                </button>
            </form>
        </div>


        <!-- ================= MAIN PORTAL CONTAINER ================= -->
        <div id="portal-section" class="dashboard-grid" style="display: none;">
            
            <!-- Sidebar -->
            <aside class="sidebar glass-card" style="padding: 1.5rem;">
                <div class="user-profile-widget">
                    <div class="user-avatar" id="user-avatar-initials">U</div>
                    <div>
                        <h4 id="user-display-name">Loading...</h4>
                        <span style="font-size: 0.75rem; color: var(--accent-teal); font-weight: 600;" id="user-display-role">Peminjam</span>
                    </div>
                </div>
                <ul class="sidebar-menu">
                    <li class="sidebar-link active" onclick="switchPortalTab('summary', this)">
                        <i class="fa-solid fa-chart-pie"></i> Ringkasan
                    </li>
                    <li class="sidebar-link" id="nav-apply" onclick="switchPortalTab('apply', this)">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Ajukan Pinjaman
                    </li>
                    <li class="sidebar-link" id="nav-payment" onclick="switchPortalTab('payment', this)">
                        <i class="fa-solid fa-receipt"></i> Bayar Cicilan
                    </li>
                    <li class="sidebar-link" onclick="handleLogout()" style="color: var(--danger); margin-top: 2rem;">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </li>
                </ul>
            </aside>

            <!-- Dashboard Contents -->
            <div class="main-content">
                
                <!-- View 1: Summary Panel -->
                <div id="view-summary" class="glass-card" style="display: none;">
                    <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem;" id="welcome-message">Halo, Peminjam</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 2rem;">Selamat datang kembali di portal CashFlow. Di bawah ini adalah status keuangan aktif Anda.</p>
                    
                    <div id="loan-status-area">
                        <!-- Content will be injected dynamically based on loan state -->
                        <div style="text-align: center; padding: 3rem 0;">
                            <i class="fa-solid fa-spinner fa-spin" style="font-size: 2.5rem; color: var(--accent-teal);"></i>
                            <p style="margin-top: 1rem; color: var(--text-muted);">Memuat informasi akun Anda...</p>
                        </div>
                    </div>
                </div>

                <!-- View 2: Multi-Step Application Wizard Form -->
                <div id="view-apply" class="glass-card" style="display: none;">
                    <!-- Stepper Progress Bar -->
                    <div class="stepper-container">
                        <div class="stepper-progress-line" id="wizard-progress"></div>
                        <div class="step-node active" id="step-node-1">
                            <div class="step-circle">1</div>
                            <span class="step-label">Data Diri</span>
                        </div>
                        <div class="step-node" id="step-node-2">
                            <div class="step-circle">2</div>
                            <span class="step-label">Pekerjaan</span>
                        </div>
                        <div class="step-node" id="step-node-3">
                            <div class="step-circle">3</div>
                            <span class="step-label">Rekening Bank</span>
                        </div>
                        <div class="step-node" id="step-node-4">
                            <div class="step-circle">4</div>
                            <span class="step-label">KTP & Review</span>
                        </div>
                    </div>

                    <form id="loan-wizard-form" onsubmit="handleWizardSubmit(event)">
                        
                        <!-- Step 1: Personal Data -->
                        <div class="form-section active" id="wizard-step-1">
                            <h3 style="margin-bottom: 1.5rem;"><i class="fa-solid fa-id-card"></i> Informasi Data Diri</h3>
                            <div class="form-group">
                                <label for="form-nik">Nomor Induk Kependudukan (NIK)</label>
                                <input type="text" id="form-nik" class="form-control" placeholder="16 digit NIK sesuai KTP" maxlength="16" pattern="\d{16}" required>
                            </div>
                            <div class="form-group">
                                <label for="form-phone">Nomor Handphone Aktif (WhatsApp)</label>
                                <input type="tel" id="form-phone" class="form-control" placeholder="Contoh: 081234567890" required>
                            </div>
                            <div class="form-group">
                                <label for="form-address">Alamat Tempat Tinggal (Sesuai KTP)</label>
                                <textarea id="form-address" class="form-control" placeholder="Masukkan alamat lengkap Anda..." rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Step 2: Work Details -->
                        <div class="form-section" id="wizard-step-2">
                            <h3 style="margin-bottom: 1.5rem;"><i class="fa-solid fa-briefcase"></i> Informasi Pekerjaan & Penghasilan</h3>
                            <div class="form-group">
                                <label for="form-job">Profesi / Bidang Pekerjaan</label>
                                <input type="text" id="form-job" class="form-control" placeholder="Contoh: Karyawan Swasta, PNS, Pengusaha" required>
                            </div>
                            <div class="form-group">
                                <label for="form-salary">Penghasilan Bersih Bulanan (Rupiah)</label>
                                <input type="number" id="form-salary" class="form-control" placeholder="Contoh: 5000000" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="form-company">Nama Perusahaan / Tempat Usaha</label>
                                <input type="text" id="form-company" class="form-control" placeholder="Masukkan nama tempat Anda bekerja" required>
                            </div>
                        </div>

                        <!-- Step 3: Bank Account Details -->
                        <div class="form-section" id="wizard-step-3">
                            <h3 style="margin-bottom: 1.5rem;"><i class="fa-solid fa-building-columns"></i> Rekening Bank Penerima Dana</h3>
                            <div class="form-group">
                                <label for="form-bank-name">Nama Bank</label>
                                <select id="form-bank-name" class="form-control" required style="background-color: #0f1224;">
                                    <option value="" disabled selected>Pilih Bank Anda</option>
                                    <option value="BCA">Bank Central Asia (BCA)</option>
                                    <option value="Mandiri">Bank Mandiri</option>
                                    <option value="BRI">Bank Rakyat Indonesia (BRI)</option>
                                    <option value="BNI">Bank Negara Indonesia (BNI)</option>
                                    <option value="CIMB">CIMB Niaga</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="form-bank-acc">Nomor Rekening</label>
                                <input type="text" id="form-bank-acc" class="form-control" placeholder="Masukkan nomor rekening tanpa spasi" required>
                            </div>
                            <div class="form-group">
                                <label for="form-bank-holder">Nama Pemilik Rekening (Harus Sesuai KTP)</label>
                                <input type="text" id="form-bank-holder" class="form-control" placeholder="Contoh: BUDI SANTOSO" required>
                            </div>
                        </div>

                        <!-- Step 4: KTP Capture & Loan Amount Summary -->
                        <div class="form-section" id="wizard-step-4">
                            <h3 style="margin-bottom: 1.5rem;"><i class="fa-solid fa-camera"></i> Scan KTP Jaminan & Review</h3>
                            <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">Lakukan pemindaian KTP asli Anda sebagai dokumen jaminan pinjaman utama.</p>
                            
                            <div class="ktp-scanner-wrapper">
                                <div class="scan-line" id="scan-laser-line"></div>
                                <div class="scan-overlay" id="scan-laser-overlay">
                                    <i class="fa-solid fa-spinner fa-spin" style="font-size: 2.5rem; color: var(--accent-teal);"></i>
                                    <span id="scan-status-text">Memindai KTP...</span>
                                </div>
                                <div class="ktp-upload-box" id="ktp-box-container" onclick="simulateKtpCamera()" style="position: relative;">
                                    <div id="ktp-upload-prompt">
                                        <i class="fa-solid fa-expand ktp-icon" style="color: var(--accent-teal); font-size: 3.5rem;"></i>
                                        <h4>Simulasikan Scan Laser KTP</h4>
                                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Klik di sini untuk memindai KTP dan mengekstrak data OCR otomatis sebagai jaminan.</p>
                                    </div>
                                    <img id="ktp-preview-img" class="ktp-preview-img" alt="KTP Preview">
                                </div>
                            </div>
                            <div id="ocr-success-indicator" style="display: none; text-align: center; margin-top: 1rem;">
                                <div class="ocr-success-badge">
                                    <i class="fa-solid fa-shield-halved"></i> Jaminan KTP Terverifikasi (Match 100% OCR)
                                </div>
                            </div>
                            <input type="hidden" id="form-ktp-base64" required>

                            <!-- Mini Summary -->
                            <div class="calc-results" style="margin-top: 2rem;">
                                <div class="result-item">
                                    <span class="result-label">Jumlah Pinjaman Dipilih</span>
                                    <span class="result-value" id="summary-amount">Rp 0</span>
                                </div>
                                <div class="result-item">
                                    <span class="result-label">Tenor Terpilih</span>
                                    <span class="result-value" id="summary-tenure">0 Bulan</span>
                                </div>
                                <div class="result-item" style="grid-column: span 2; border-top: 1px dashed var(--glass-border); padding-top: 0.5rem; margin-top: 0.5rem;">
                                    <span class="result-label">Cicilan Bulanan</span>
                                    <span class="result-value highlight" id="summary-monthly">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Wizard Footer Actions -->
                        <div class="wizard-buttons">
                            <button type="button" class="btn btn-secondary" id="wizard-prev-btn" onclick="wizardPrev()" disabled>
                                <i class="fa-solid fa-chevron-left"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-primary" id="wizard-next-btn" onclick="wizardNext()">
                                Lanjut <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>

                    </form>
                </div>

                <!-- View 3: Payment Panel -->
                <div id="view-payment" class="glass-card" style="display: none;">
                    <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-receipt"></i> Pembayaran Cicilan Pinjaman</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 2rem;">Silakan lakukan pembayaran angsuran aktif Anda menggunakan nomor rekening Virtual Account di bawah ini.</p>

                    <!-- Payment summary -->
                    <div class="payment-detail-card">
                        <div>
                            <h4>Sisa Tagihan (Ditambah Bunga)</h4>
                            <p id="pay-total-remaining" style="color: var(--danger);">Rp 0</p>
                        </div>
                        <div>
                            <h4>Cicilan Per Bulan</h4>
                            <p id="pay-monthly-amount" style="color: var(--text-white);">Rp 0</p>
                        </div>
                    </div>

                    <!-- Virtual Account Number -->
                    <div class="virtual-account-box">
                        <span class="va-label">BANK MANDIRI VIRTUAL ACCOUNT</span>
                        <div class="va-number" id="pay-va-number">88000 + NIK</div>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Nama Rekening VA: <span id="pay-va-name">CASHFLOW - NAMA</span></p>
                    </div>

                    <!-- Form Payment Action -->
                    <div class="glass-card" style="background: rgba(255, 255, 255, 0.01);">
                        <h4 style="margin-bottom: 1rem;">Simulasi Transaksi Pembayaran</h4>
                        <div class="form-group">
                            <label for="pay-input-amount">Jumlah Pembayaran (Rp)</label>
                            <input type="number" id="pay-input-amount" class="form-control" placeholder="Masukkan nominal pembayaran">
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Anda bisa membayar sebagian cicilan atau melunasi seluruh sisa tagihan.</p>
                        </div>
                        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="fillInstallmentPayment()">Bayar Sesuai Cicilan Bulanan</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="fillFullPayment()">Lunasi Semua Sisa Tagihan</button>
                        </div>
                        <button type="button" class="btn btn-success" style="width: 100%; margin-top: 1.5rem;" onclick="processPaymentSubmit()">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Pembayaran (Simulasi Instan)
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer style="margin-top: 8rem;">
        <div class="container footer-bottom">
            <p>&copy; 2026 CashFlow Indonesia. Portal Pengguna Terenkripsi 256-bit SSL.</p>
        </div>
    </footer>

    <!-- JavaScript client-side operations -->
    <script>
        // System variables
        let activeTab = 'summary';
        let currentWizardStep = 1;
        
        // Account details cache
        let userData = null;
        let loanData = null;
        
        // Loan configurations chosen from simulator page (if any)
        let chosenAmount = 2000000;
        let chosenTenure = 3;

        // Parse query parameters
        function parseQueryParams() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('amount')) {
                chosenAmount = parseFloat(params.get('amount'));
            }
            if (params.has('tenure')) {
                chosenTenure = parseInt(params.get('tenure'));
            }
        }

        // Initialize script
        window.addEventListener('DOMContentLoaded', () => {
            parseQueryParams();
            checkSession();
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

        // Formatter for Currency
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        // Switch Auth Tab: login / register
        function switchAuthTab(tabName) {
            document.querySelectorAll('.auth-tab').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.auth-form').forEach(el => el.classList.remove('active'));
            
            if (tabName === 'login') {
                document.querySelectorAll('.auth-tab')[0].classList.add('active');
                document.getElementById('login-form').classList.add('active');
            } else {
                document.querySelectorAll('.auth-tab')[1].classList.add('active');
                document.getElementById('register-form').classList.add('active');
            }
        }

        // Check Login Session
        function checkSession() {
            fetch('api.php?action=get_status')
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        userData = res.data.user;
                        loanData = res.data.loan;
                        
                        // Handle Admin redirect
                        if (userData.role === 'admin') {
                            window.location.href = 'admin.php';
                            return;
                        }

                        renderPortal();
                    } else {
                        // User not logged in, show auth screens
                        document.getElementById('auth-section').style.display = 'block';
                        document.getElementById('portal-section').style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Koneksi ke server terputus.', 'error');
                });
        }

        // Render dashboard portal view after verification
        function renderPortal() {
            document.getElementById('auth-section').style.display = 'none';
            document.getElementById('portal-section').style.display = 'grid';
            
            // Set profile data
            document.getElementById('user-display-name').textContent = userData.name;
            document.getElementById('user-avatar-initials').textContent = userData.name.substring(0, 1).toUpperCase();
            document.getElementById('welcome-message').textContent = `Halo, ${userData.name}`;
            
            // Setup application defaults
            document.getElementById('summary-amount').textContent = formatRupiah(chosenAmount);
            document.getElementById('summary-tenure').textContent = chosenTenure + ' Bulan';
            
            const interest = chosenAmount * 0.10;
            const monthlyPayment = Math.ceil((chosenAmount + interest) / chosenTenure);
            document.getElementById('summary-monthly').textContent = formatRupiah(monthlyPayment);

            // Render loan summary status
            renderLoanSummary();
            switchPortalTab(activeTab, document.querySelector('.sidebar-link.active'));
        }

        // Render Summary Screen details based on loan conditions
        function renderLoanSummary() {
            const container = document.getElementById('loan-status-area');
            
            if (!loanData) {
                // Case: No Loan Applied yet
                container.innerHTML = `
                    <div class="glass-card" style="background: rgba(255,255,255,0.01); border-color: rgba(255,255,255,0.04); text-align: center; padding: 3rem 1.5rem;">
                        <div class="feature-icon-wrapper" style="margin: 0 auto 1.5rem auto; width: 70px; height: 70px;">
                            <i class="fa-solid fa-piggy-bank" style="font-size: 2rem;"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Belum Ada Pinjaman Aktif</h3>
                        <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 500px; margin: 0 auto 2rem auto;">
                            Anda tidak memiliki pengajuan atau tagihan pinjaman yang aktif. Tekan tombol di bawah untuk mengajukan pinjaman pertama Anda dengan pencairan instant.
                        </p>
                        <button class="btn btn-primary" onclick="goToApplyTab()">
                            <i class="fa-solid fa-circle-plus"></i> Ajukan Sekarang
                        </button>
                    </div>
                `;
                document.getElementById('nav-apply').style.display = 'flex';
                document.getElementById('nav-payment').style.display = 'none';
            } else if (loanData.status === 'pending') {
                // Case: Loan application is pending review
                container.innerHTML = `
                    <div class="status-banner pending" style="margin-bottom: 2rem;">
                        <span><i class="fa-solid fa-clock"></i> Pengajuan Sedang Ditinjau</span>
                        <span class="badge badge-pending">Pending</span>
                    </div>
                    <div class="glass-card" style="background: rgba(255,255,255,0.01); border-color: rgba(255,255,255,0.04);">
                        <h3 style="margin-bottom: 1rem;">Detail Pengajuan Anda</h3>
                        <div class="admin-detail-grid" style="margin-bottom: 1.5rem;">
                            <div class="detail-block">
                                <h5>Jumlah Pengajuan</h5>
                                <p>${formatRupiah(loanData.amount)}</p>
                            </div>
                            <div class="detail-block">
                                <h5>Jangka Waktu (Tenor)</h5>
                                <p>${loanData.tenure} Bulan</p>
                            </div>
                            <div class="detail-block">
                                <h5>Cicilan Bulanan</h5>
                                <p>${formatRupiah(loanData.monthly_payment)}</p>
                            </div>
                            <div class="detail-block">
                                <h5>Tujuan Pencairan Bank</h5>
                                <p>${loanData.bank_name} - ${loanData.bank_account}</p>
                            </div>
                        </div>
                        <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; border-top: 1px solid var(--glass-border); padding-top: 1rem; margin-top: 1rem;">
                            <i class="fa-solid fa-circle-info" style="color: var(--warning);"></i> 
                            Pengajuan pinjaman Anda sedang diproses oleh sistem kredit analisis kami. Kami akan memeriksa validitas data dan foto KTP Anda. Proses ini umumnya memakan waktu kurang dari 10 menit.
                        </p>
                    </div>
                `;
                document.getElementById('nav-apply').style.display = 'none';
                document.getElementById('nav-payment').style.display = 'none';
            } else if (loanData.status === 'approved') {
                // Case: Loan approved, active loan balance to pay
                container.innerHTML = `
                    <div class="status-banner approved" style="margin-bottom: 2rem;">
                        <span><i class="fa-solid fa-circle-check"></i> Pinjaman Aktif</span>
                        <span class="badge badge-approved">Aktif</span>
                    </div>
                    <div class="stats-grid" style="margin-bottom: 2rem;">
                        <div class="stat-card">
                            <div class="stat-header">
                                <span>Sisa Tagihan</span>
                                <i class="fa-solid fa-wallet stat-icon" style="color: var(--danger);"></i>
                            </div>
                            <span class="stat-value" style="color: var(--danger);">${formatRupiah(loanData.remaining_balance)}</span>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <span>Cicilan Bulanan</span>
                                <i class="fa-solid fa-calendar-days stat-icon"></i>
                            </div>
                            <span class="stat-value">${formatRupiah(loanData.monthly_payment)}</span>
                        </div>
                        <div class="stat-card">
                            <div class="stat-header">
                                <span>Tenor Tersisa</span>
                                <i class="fa-solid fa-hourglass-half stat-icon"></i>
                            </div>
                            <span class="stat-value">${loanData.tenure} Bulan</span>
                        </div>
                    </div>
                    <div class="glass-card" style="background: rgba(255,255,255,0.01); border-color: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
                        <div>
                            <h4>Ingin membayar cicilan pinjaman?</h4>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Gunakan Virtual Account Mandiri untuk pembayaran aman secara otomatis.</p>
                        </div>
                        <button class="btn btn-success" onclick="goToPaymentTab()">
                            <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                        </button>
                    </div>

                    <!-- KTP collateral/guarantee section -->
                    <div class="glass-card" style="background: rgba(255,255,255,0.01); border-color: rgba(255,255,255,0.04);">
                        <h4 style="margin-bottom: 1rem;"><i class="fa-solid fa-lock" style="color: var(--warning);"></i> Jaminan Pinjaman Aktif (KTP Terkunci)</h4>
                        <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                            <div style="background: rgba(0,0,0,0.2); border-radius: 8px; padding: 0.5rem; text-align: center; max-width: 180px;">
                                <img src="${loanData.ktp_photo}" style="max-width: 100%; border-radius: 6px; cursor: pointer; border: 1px solid var(--glass-border);" alt="Agunan KTP" onclick="showAgunanModal()">
                            </div>
                            <div style="flex: 1; min-width: 250px;">
                                <p style="font-size: 0.9rem; line-height: 1.5; color: var(--text-muted); margin-bottom: 0.5rem;">
                                    <span class="badge" style="color: var(--success); background: var(--success-bg); border-color: rgba(16, 185, 129, 0.3); padding: 0.25rem 0.5rem;"><i class="fa-solid fa-circle-check"></i> OCR Validated</span>
                                </p>
                                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.4;">
                                    KTP Anda saat ini terdaftar dan terkunci sebagai <strong>jaminan utama</strong> yang sah. Jaminan agunan akan dilepas secara otomatis dari database sistem setelah Anda melunasi sisa tagihan.
                                </p>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('nav-apply').style.display = 'none';
                document.getElementById('nav-payment').style.display = 'flex';
                
                // Set Payment Details Screen fields
                document.getElementById('pay-total-remaining').textContent = formatRupiah(loanData.remaining_balance);
                document.getElementById('pay-monthly-amount').textContent = formatRupiah(loanData.monthly_payment);
                document.getElementById('pay-va-number').textContent = "8890" + loanData.nik.substring(10);
                document.getElementById('pay-va-name').textContent = "CASHFLOW - " + userData.name.toUpperCase();
                document.getElementById('pay-input-amount').value = loanData.monthly_payment;
            } else if (loanData.status === 'rejected') {
                // Case: Loan rejected
                container.innerHTML = `
                    <div class="status-banner rejected" style="margin-bottom: 2rem;">
                        <span><i class="fa-solid fa-circle-xmark"></i> Pengajuan Ditolak</span>
                        <span class="badge badge-rejected">Ditolak</span>
                    </div>
                    <div class="glass-card" style="background: rgba(255,255,255,0.01); border-color: rgba(255,255,255,0.04); text-align: center; padding: 2.5rem 1.5rem;">
                        <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: var(--danger); margin-bottom: 1rem;"></i>
                        <h3 style="margin-bottom: 0.5rem;">Kelayakan Kredit Belum Terpenuhi</h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 550px; margin: 0 auto 2rem auto;">
                            Pengajuan pinjaman Anda ditolak oleh tim analis karena evaluasi scoring data Anda belum memenuhi batas minimal kelayakan kredit platform kami.
                        </p>
                        <button class="btn btn-primary" onclick="resetAndReapply()">
                            <i class="fa-solid fa-rotate-right"></i> Perbarui Data & Ajukan Ulang
                        </button>
                    </div>
                `;
                document.getElementById('nav-apply').style.display = 'none';
                document.getElementById('nav-payment').style.display = 'none';
            } else if (loanData.status === 'paid') {
                // Case: Loan completed and fully paid
                container.innerHTML = `
                    <div class="status-banner approved" style="margin-bottom: 2rem; background: var(--accent-teal-glow); border-color: rgba(20, 184, 166, 0.3); color: var(--accent-teal);">
                        <span><i class="fa-solid fa-trophy"></i> Pinjaman Telah Lunas</span>
                        <span class="badge badge-paid">Lunas</span>
                    </div>
                    <div class="glass-card" style="background: rgba(255,255,255,0.01); border-color: rgba(255,255,255,0.04); text-align: center; padding: 3rem 1.5rem;">
                        <i class="fa-solid fa-circle-check" style="font-size: 3.5rem; color: var(--accent-teal); margin-bottom: 1rem;"></i>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Selamat! Kewajiban Anda Selesai</h3>
                        <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 500px; margin: 0 auto 2rem auto;">
                            Seluruh tagihan pinjaman Anda telah berhasil dilunasi sepenuhnya dengan riwayat kredit yang sangat baik. Anda sekarang dapat melakukan pengajuan pinjaman baru.
                        </p>
                        <button class="btn btn-primary" onclick="resetAndReapply()">
                            <i class="fa-solid fa-money-bill-wave"></i> Ajukan Pinjaman Baru
                        </button>
                    </div>
                `;
                document.getElementById('nav-apply').style.display = 'flex';
                document.getElementById('nav-payment').style.display = 'none';
            }
        }

        // Switch Sidebar Menu Active State
        function switchPortalTab(tabName, element) {
            activeTab = tabName;
            
            // Toggle active sidebar highlight
            document.querySelectorAll('.sidebar-link').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');

            // Hide all views
            document.getElementById('view-summary').style.display = 'none';
            document.getElementById('view-apply').style.display = 'none';
            document.getElementById('view-payment').style.display = 'none';

            // Show current view
            if (tabName === 'summary') {
                document.getElementById('view-summary').style.display = 'block';
            } else if (tabName === 'apply') {
                document.getElementById('view-apply').style.display = 'block';
                initWizardProgress();
            } else if (tabName === 'payment') {
                document.getElementById('view-payment').style.display = 'block';
            }
        }

        // Easy shortcut navigators
        function goToApplyTab() {
            switchPortalTab('apply', document.getElementById('nav-apply'));
        }
        function goToPaymentTab() {
            switchPortalTab('payment', document.getElementById('nav-payment'));
        }

        // Reset and reapply after rejection or full payment
        function resetAndReapply() {
            // Locally wipe loanData cache so apply wizard is accessible
            loanData = null;
            renderLoanSummary();
            goToApplyTab();
        }

        // Login Handler
        function handleLogin(e) {
            e.preventDefault();
            const username = document.getElementById('login-username').value;
            const password = document.getElementById('login-password').value;

            fetch('api.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type: application/json' },
                body: JSON.stringify({ username, password })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast('Login berhasil! Mengalihkan...');
                    userData = res.data;
                    checkSession();
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Kesalahan jaringan saat login.', 'error'));
        }

        // Register Handler
        function handleRegister(e) {
            e.preventDefault();
            const name = document.getElementById('reg-name').value;
            const username = document.getElementById('reg-username').value;
            const password = document.getElementById('reg-password').value;

            fetch('api.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type: application/json' },
                body: JSON.stringify({ name, username, password })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast('Registrasi berhasil! Membuka Dashboard...');
                    userData = res.data;
                    checkSession();
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Gagal mendaftarkan akun.', 'error'));
        }

        // Logout Handler
        function handleLogout() {
            fetch('api.php?action=logout')
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showToast('Berhasil keluar dari sesi.');
                        userData = null;
                        loanData = null;
                        document.getElementById('auth-section').style.display = 'block';
                        document.getElementById('portal-section').style.display = 'none';
                        // Clean input fields
                        document.getElementById('login-form').reset();
                    }
                });
        }

        // ================= WIZARD CONTROLS =================
        function initWizardProgress() {
            const line = document.getElementById('wizard-progress');
            const pct = ((currentWizardStep - 1) / 3) * 100;
            line.style.width = pct + '%';
        }

        function wizardNext() {
            if (currentWizardStep === 1) {
                const nik = document.getElementById('form-nik');
                const phone = document.getElementById('form-phone');
                const address = document.getElementById('form-address');
                if (!nik.checkValidity() || !phone.checkValidity() || !address.checkValidity()) {
                    showToast('Harap isi Data Diri dengan benar. NIK harus 16 digit.', 'warning');
                    return;
                }
            } else if (currentWizardStep === 2) {
                const job = document.getElementById('form-job');
                const salary = document.getElementById('form-salary');
                const company = document.getElementById('form-company');
                if (!job.checkValidity() || !salary.checkValidity() || !company.checkValidity()) {
                    showToast('Harap lengkapi Data Pekerjaan Anda.', 'warning');
                    return;
                }
            } else if (currentWizardStep === 3) {
                const bank = document.getElementById('form-bank-name');
                const acc = document.getElementById('form-bank-acc');
                const holder = document.getElementById('form-bank-holder');
                if (!bank.checkValidity() || !acc.checkValidity() || !holder.checkValidity()) {
                    showToast('Harap masukkan Data Rekening Bank Anda.', 'warning');
                    return;
                }
            }

            if (currentWizardStep < 4) {
                // Remove active from current step
                document.getElementById(`wizard-step-${currentWizardStep}`).classList.remove('active');
                document.getElementById(`step-node-${currentWizardStep}`).classList.add('completed');
                
                currentWizardStep++;
                
                // Show next step
                document.getElementById(`wizard-step-${currentWizardStep}`).classList.add('active');
                document.getElementById(`step-node-${currentWizardStep}`).classList.add('active');
                
                // Disable/Enable buttons
                document.getElementById('wizard-prev-btn').disabled = false;
                if (currentWizardStep === 4) {
                    document.getElementById('wizard-next-btn').innerHTML = 'Kirim Pengajuan <i class="fa-solid fa-paper-plane"></i>';
                    document.getElementById('wizard-next-btn').className = "btn btn-success";
                    document.getElementById('wizard-next-btn').type = "submit";
                }
                
                initWizardProgress();
            }
        }

        function wizardPrev() {
            if (currentWizardStep > 1) {
                document.getElementById(`wizard-step-${currentWizardStep}`).classList.remove('active');
                document.getElementById(`step-node-${currentWizardStep}`).classList.remove('active');
                
                currentWizardStep--;
                
                document.getElementById(`wizard-step-${currentWizardStep}`).classList.add('active');
                document.getElementById(`step-node-${currentWizardStep}`).classList.remove('completed');
                
                document.getElementById('wizard-prev-btn').disabled = (currentWizardStep === 1);
                document.getElementById('wizard-next-btn').innerHTML = 'Lanjut <i class="fa-solid fa-chevron-right"></i>';
                document.getElementById('wizard-next-btn').className = "btn btn-primary";
                document.getElementById('wizard-next-btn').type = "button";
                
                initWizardProgress();
            }
        }

        // Simulate Capture Photo KTP via mock camera drawing with scanning laser effect
        function simulateKtpCamera() {
            // If already scanned, confirm reload
            const inputHidden = document.getElementById('form-ktp-base64');
            if (inputHidden.value) {
                if (!confirm('Apakah Anda ingin memindai ulang KTP Anda?')) return;
            }

            const name = document.getElementById('reg-name').value || (userData ? userData.name : 'BUDI SANTOSO');
            const nik = document.getElementById('form-nik').value || '1234567890123456';
            const address = document.getElementById('form-address').value || 'Jl. Jenderal Sudirman No. 12';
            
            // Show scanning states
            const scanLaser = document.getElementById('scan-laser-line');
            const scanOverlay = document.getElementById('scan-laser-overlay');
            const statusText = document.getElementById('scan-status-text');
            const successIndicator = document.getElementById('ocr-success-indicator');
            const prompt = document.getElementById('ktp-upload-prompt');
            const preview = document.getElementById('ktp-preview-img');

            // Activate scanning visuals
            scanLaser.classList.add('scanning');
            scanOverlay.classList.add('scanning');
            successIndicator.style.display = 'none';
            statusText.textContent = 'Menghubungkan Kamera Laser...';

            setTimeout(() => {
                statusText.innerHTML = '<i class="fa-solid fa-expand fa-spin"></i> Menyelaraskan Bingkai Dokumen KTP...';
                
                setTimeout(() => {
                    statusText.innerHTML = '<i class="fa-solid fa-qrcode fa-fade"></i> Membaca OCR Data Identitas...';
                    
                    setTimeout(() => {
                        // Create KTP card on canvas
                        const canvas = document.createElement('canvas');
                        canvas.width = 400;
                        canvas.height = 250;
                        const ctx = canvas.getContext('2d');
                        
                        const grad = ctx.createLinearGradient(0, 0, 400, 250);
                        grad.addColorStop(0, '#111827');
                        grad.addColorStop(1, '#1f2937');
                        ctx.fillStyle = grad;
                        ctx.fillRect(0, 0, 400, 250);
                        
                        ctx.fillStyle = '#ffffff';
                        ctx.font = 'bold 11px Arial';
                        ctx.fillText('PROVINSI DKI JAKARTA', 140, 25);
                        ctx.fillText('KARTU TANDA PENDUDUK', 135, 40);
                        
                        ctx.fillStyle = '#ffffff';
                        ctx.font = 'bold 13px Arial';
                        ctx.fillText('NIK : ' + nik, 30, 70);
                        
                        ctx.font = '9px Arial';
                        ctx.fillText('Nama', 30, 95);
                        ctx.fillText(': ' + name.toUpperCase(), 120, 95);
                        
                        ctx.fillText('Tempat/Tgl Lahir', 30, 110);
                        ctx.fillText(': JAKARTA, 17-08-1995', 120, 110);
                        
                        ctx.fillText('Jenis Kelamin', 30, 125);
                        ctx.fillText(': LAKI-LAKI', 120, 125);
                        
                        ctx.fillText('Alamat', 30, 140);
                        ctx.fillText(': ' + address.toUpperCase(), 120, 140);
                        
                        ctx.fillText('Agama', 30, 155);
                        ctx.fillText(': ISLAM', 120, 155);
                        
                        ctx.fillText('Status Perkawinan', 30, 170);
                        ctx.fillText(': BELUM KAWIN', 120, 170);
                        
                        ctx.fillText('Pekerjaan', 30, 185);
                        ctx.fillText(': KARYAWAN', 120, 185);
                        
                        ctx.fillText('Kewarganegaraan', 30, 200);
                        ctx.fillText(': WNI', 120, 200);

                        ctx.fillStyle = 'rgba(255,255,255,0.08)';
                        ctx.fillRect(285, 70, 90, 110);
                        ctx.strokeStyle = 'rgba(255,255,255,0.2)';
                        ctx.lineWidth = 1;
                        ctx.strokeRect(285, 70, 90, 110);
                        
                        ctx.fillStyle = '#10b981';
                        ctx.font = 'bold 8px Arial';
                        ctx.fillText('OCR JAMINAN', 295, 125);

                        const base64 = canvas.toDataURL('image/jpeg');
                        
                        // Disable scanning overlays
                        scanLaser.classList.remove('scanning');
                        scanOverlay.classList.remove('scanning');
                        
                        prompt.style.display = 'none';
                        preview.src = base64;
                        preview.style.display = 'block';
                        inputHidden.value = base64;
                        successIndicator.style.display = 'block';
                        
                        showToast('Pindai KTP Berhasil! Data jaminan tervalidasi via OCR.');
                    }, 1200);
                }, 1000);
            }, 800);
        }

        // Wizard Final Submission
        function handleWizardSubmit(e) {
            e.preventDefault();
            
            const nik = document.getElementById('form-nik').value;
            const phone = document.getElementById('form-phone').value;
            const address = document.getElementById('form-address').value;
            const job = document.getElementById('form-job').value;
            const salary = document.getElementById('form-salary').value;
            const bank_name = document.getElementById('form-bank-name').value;
            const bank_account = document.getElementById('form-bank-acc').value;
            const bank_holder = document.getElementById('form-bank-holder').value;
            const ktp_photo = document.getElementById('form-ktp-base64').value;

            if (!ktp_photo) {
                showToast('Harap lakukan simulasi foto KTP terlebih dahulu.', 'warning');
                return;
            }

            const payload = {
                nik, phone, address, job, salary, bank_name, bank_account, bank_holder, ktp_photo,
                amount: chosenAmount,
                tenure: chosenTenure
            };

            fetch('api.php?action=apply', {
                method: 'POST',
                headers: { 'Content-Type: application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast('Pengajuan Anda sukses terkirim!');
                    loanData = res.data;
                    
                    // Reset wizard steps
                    document.getElementById('loan-wizard-form').reset();
                    document.getElementById('ktp-preview-img').style.display = 'none';
                    document.getElementById('ktp-upload-prompt').style.display = 'block';
                    document.getElementById('form-ktp-base64').value = '';
                    
                    // Reset wizard index nodes
                    document.getElementById(`wizard-step-${currentWizardStep}`).classList.remove('active');
                    document.getElementById(`step-node-${currentWizardStep}`).classList.remove('active');
                    currentWizardStep = 1;
                    document.getElementById(`wizard-step-1`).classList.add('active');
                    document.getElementById(`step-node-1`).classList.add('active');
                    document.querySelectorAll('.step-node').forEach(el => el.classList.remove('completed'));
                    
                    // Switch back to summary dashboard showing pending
                    checkSession();
                    switchPortalTab('summary', document.querySelector('.sidebar-link'));
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Kesalahan koneksi saat mengirim pengajuan.', 'error'));
        }

        // ================= PAYMENT ACTIONS =================
        function fillInstallmentPayment() {
            if (loanData) {
                document.getElementById('pay-input-amount').value = Math.min(loanData.monthly_payment, loanData.remaining_balance);
            }
        }

        function fillFullPayment() {
            if (loanData) {
                document.getElementById('pay-input-amount').value = loanData.remaining_balance;
            }
        }

        function processPaymentSubmit() {
            const amount = parseFloat(document.getElementById('pay-input-amount').value);
            if (isNaN(amount) || amount <= 0) {
                showToast('Nominal pembayaran harus diisi dan valid.', 'warning');
                return;
            }

            fetch('api.php?action=pay', {
                method: 'POST',
                headers: { 'Content-Type: application/json' },
                body: JSON.stringify({ amount })
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast(`Pembayaran senilai ${formatRupiah(res.data.paid_amount)} berhasil diproses!`);
                    
                    // Update state and refresh UI
                    checkSession();
                } else {
                    showToast(res.message, 'error');
                }
            })
            .catch(() => showToast('Kesalahan koneksi saat melakukan pembayaran.', 'error'));
        }

        // Show Agunan Modal
        function showAgunanModal() {
            if (!loanData || !loanData.ktp_photo) return;
            
            // Create dynamic modal overlay
            const modal = document.createElement('div');
            modal.id = 'agunan-modal-overlay';
            modal.className = 'modal-overlay active';
            modal.style.zIndex = '3000';
            modal.onclick = () => modal.remove();
            
            modal.innerHTML = `
                <div class="modal-container glass-card" style="max-width: 500px; text-align: center; position: relative;" onclick="event.stopPropagation()">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                        <h3>Dokumen Jaminan KTP</h3>
                        <button onclick="document.getElementById('agunan-modal-overlay').remove()" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.25rem; cursor: pointer;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.3); padding: 0.5rem;">
                        <!-- Watermark Overlay -->
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 1.8rem; font-weight: bold; color: rgba(239, 68, 68, 0.4); text-transform: uppercase; border: 4px dashed rgba(239, 68, 68, 0.4); padding: 0.5rem 1rem; pointer-events: none; z-index: 10; white-space: nowrap;">
                            Jaminan Aktif
                        </div>
                        <img src="${loanData.ktp_photo}" style="max-width: 100%; border-radius: 8px;" alt="Agunan KTP">
                    </div>
                    <p style="margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted);">
                        Sertifikat jaminan enkripsi CashFlow Indonesia. Dilindungi oleh hash OCR.
                    </p>
                </div>
            `;
            document.body.appendChild(modal);
        }
    </script>
</body>
</html>
