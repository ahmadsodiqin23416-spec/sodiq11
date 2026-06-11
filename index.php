<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CashFlow - Platform pinjaman online cepat, aman, dan terpercaya tanpa jaminan. Proses pengajuan 100% online dengan verifikasi instan dalam 10 menit.">
    <meta name="keywords" content="pinjol, pinjaman online, dana cepat, kredit mikro, cashflow, pinjaman tanpa jaminan">
    <title>CashFlow - Pinjaman Online Cepat, Aman & Terpercaya</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header Navigation -->
    <header>
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-wallet logo-icon"></i> CashFlow
            </a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="#home" class="nav-link active">Home</a></li>
                    <li><a href="#simulator" class="nav-link">Simulator</a></li>
                    <li><a href="#features" class="nav-link">Keunggulan</a></li>
                    <li><a href="#faq" class="nav-link">FAQ</a></li>
                    <li>
                        <?php if(isset($_SESSION['username'])): ?>
                            <?php if($_SESSION['role'] === 'admin'): ?>
                                <a href="admin.php" class="btn btn-primary btn-sm">Dashboard Admin</a>
                            <?php else: ?>
                                <a href="dashboard.php" class="btn btn-primary btn-sm">Dashboard Peminjam</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="dashboard.php" class="btn btn-secondary btn-sm" style="margin-right: 0.5rem;">Login Peminjam</a>
                            <a href="admin_login.php" class="btn btn-primary btn-sm">Login Admin</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">

        <!-- Hero & Simulator Section -->
        <section id="home" class="hero-section">
            <div class="hero-content">
                <span class="section-tag">Solusi Keuangan Anda</span>
                <h1 class="hero-title">Pinjaman Cepat <span>Tanpa Jaminan</span> & Aman</h1>
                <p class="hero-desc">Dapatkan dana tunai hingga Rp 10.000.000 dengan proses pengajuan 100% online, verifikasi cepat dalam 10 menit, dan bunga yang sangat kompetitif.</p>
                <div style="display: flex; gap: 1rem;">
                    <a href="#simulator" class="btn btn-primary"><i class="fa-solid fa-calculator"></i> Cek Simulasi</a>
                    <a href="#features" class="btn btn-secondary">Pelajari Selengkapnya</a>
                </div>
            </div>

            <!-- Loan Simulator Card -->
            <div id="simulator" class="glass-card">
                <div class="calc-header">
                    <h3 class="calc-title">Simulasi Pinjaman</h3>
                    <p class="calc-subtitle">Sesuaikan jumlah pinjaman dan jangka waktu pembayaran Anda.</p>
                </div>

                <!-- Slider Amount -->
                <div class="slider-group">
                    <div class="slider-label-row">
                        <span>Jumlah Pinjaman</span>
                        <span class="slider-value" id="amount-val">Rp 2.000.000</span>
                    </div>
                    <input type="range" id="amount-slider" class="custom-slider" min="500000" max="10000000" step="500000" value="2000000">
                </div>

                <!-- Slider Tenure -->
                <div class="slider-group">
                    <div class="slider-label-row">
                        <span>Tenor (Jangka Waktu)</span>
                        <span class="slider-value" id="tenure-val">3 Bulan</span>
                    </div>
                    <input type="range" id="tenure-slider" class="custom-slider" min="3" max="12" step="1" value="3">
                </div>

                <!-- Result Calculations -->
                <div class="calc-results">
                    <div class="result-item">
                        <span class="result-label">Total Bunga (10% flat)</span>
                        <span class="result-value" id="interest-result">Rp 200.000</span>
                    </div>
                    <div class="result-item">
                        <span class="result-label">Total Pengembalian</span>
                        <span class="result-value" id="total-result">Rp 2.200.000</span>
                    </div>
                    <div class="result-item" style="grid-column: span 2; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--glass-border);">
                        <span class="result-label">Cicilan per Bulan</span>
                        <span class="result-value highlight" id="monthly-result">Rp 733.333</span>
                    </div>
                </div>

                <button class="btn btn-success" style="width: 100%;" id="apply-btn">
                    Ajukan Pinjaman Sekarang <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </section>

        <!-- Steps Section -->
        <section style="padding: 3rem 0; text-align: center;">
            <span class="section-tag">Alur Pengajuan</span>
            <h2 class="section-title">4 Langkah Mudah Pengajuan</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 2rem;">
                <div class="glass-card" style="padding: 2rem; text-align: center;">
                    <div class="feature-icon-wrapper" style="margin: 0 auto 1.5rem auto;">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <h3>1. Daftar Akun</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">Buat akun CashFlow menggunakan nomor HP aktif Anda dalam 1 menit.</p>
                </div>
                <div class="glass-card" style="padding: 2rem; text-align: center;">
                    <div class="feature-icon-wrapper" style="margin: 0 auto 1.5rem auto;">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <h3>2. Lengkapi Data</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">Isi formulir data diri, pekerjaan, rekening bank, dan upload foto KTP Anda.</p>
                </div>
                <div class="glass-card" style="padding: 2rem; text-align: center;">
                    <div class="feature-icon-wrapper" style="margin: 0 auto 1.5rem auto;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3>3. Analisis & Verifikasi</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">Sistem kami menganalisis kelayakan kredit Anda dalam hitungan menit.</p>
                </div>
                <div class="glass-card" style="padding: 2rem; text-align: center;">
                    <div class="feature-icon-wrapper" style="margin: 0 auto 1.5rem auto;">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>
                    <h3>4. Dana Cair</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">Dana langsung ditransfer ke rekening bank terdaftar setelah pengajuan disetujui.</p>
                </div>
            </div>
        </section>

        <!-- Features/Keunggulan Section -->
        <section id="features" class="features-section">
            <span class="section-tag">Keunggulan Kami</span>
            <h2 class="section-title">Mengapa Memilih CashFlow?</h2>
            <div class="features-grid">
                <div class="glass-card feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3>Pencairan Instant</h3>
                    <p>Setelah pengajuan Anda disetujui, dana akan segera masuk ke rekening bank Anda dalam waktu kurang dari 10 menit.</p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                    <h3>Bunga Bersahabat</h3>
                    <p>Suku bunga transparan tanpa biaya tersembunyi. Kami menawarkan bunga flat 10% yang sangat bersahabat.</p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h3>Keamanan Data Terjamin</h3>
                    <p>Semua informasi data pribadi Anda dienkripsi dengan teknologi standar perbankan demi menjaga privasi Anda.</p>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="faq-section">
            <div style="text-align: center;">
                <span class="section-tag">Tanya Jawab</span>
                <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
            </div>
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Apa saja syarat untuk mengajukan pinjaman di CashFlow?</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Persyaratannya sangat mudah! Anda hanya perlu menjadi Warga Negara Indonesia (WNI) berusia minimal 18 tahun, memiliki KTP aktif, memiliki pekerjaan/penghasilan tetap, serta memiliki rekening bank atas nama sendiri.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Berapa lama proses persetujuan pinjaman?</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Proses analisis pengajuan pinjaman memerlukan waktu kurang lebih 5-15 menit dari saat Anda mengirimkan pengajuan di dashboard. Setelah disetujui, dana akan segera ditransfer secara otomatis.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Bagaimana cara melakukan pembayaran cicilan?</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Pembayaran dapat dilakukan dengan mudah melalui transfer Virtual Account bank yang tertera pada halaman dashboard akun Anda. Cicilan dapat dibayar sebagian atau langsung dilunasi sekaligus.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Apakah data pribadi saya aman bersama CashFlow?</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        Tentu saja! Keamanan privasi Anda adalah prioritas utama kami. Seluruh transmisi data dienkripsi menggunakan sertifikasi keamanan tingkat tinggi dan kami tidak akan pernah menyebarluaskan data Anda kepada pihak ketiga tanpa persetujuan Anda.
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer>
        <div class="container footer-container">
            <div>
                <a href="index.php" class="logo">
                    <i class="fa-solid fa-wallet logo-icon"></i> CashFlow
                </a>
                <p class="footer-desc">Solusi platform finansial teknologi mikro-kredit tercepat dan terpercaya untuk memenuhi kebutuhan produktif dan darurat Anda secara instan.</p>
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; font-size: 1.25rem;">
                    <a href="#" style="color: var(--text-muted);"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" style="color: var(--text-muted);"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" style="color: var(--text-muted);"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
            <div>
                <h4 class="footer-heading">Menu Cepat</h4>
                <ul class="footer-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#simulator">Simulator</a></li>
                    <li><a href="#features">Keunggulan</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-heading">Layanan Pelanggan</h4>
                <ul class="footer-links">
                    <li><a href="#"><i class="fa-solid fa-envelope"></i> support@cashflow.id</a></li>
                    <li><a href="#"><i class="fa-solid fa-phone"></i> (021) 5000-8888</a></li>
                    <li><a href="#"><i class="fa-solid fa-location-dot"></i> Sudirman Central Business District, Jakarta</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom container">
            <p>&copy; 2026 CashFlow Indonesia. Hak Cipta Dilindungi Undang-Undang.</p>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; line-height: 1.4;">
                *Catatan Simulasi: CashFlow adalah platform demonstrasi pinjaman online untuk keperluan portofolio/uji coba. Seluruh transaksi, data, dan proses di dalamnya adalah simulasi belaka.
            </p>
        </div>
    </footer>

    <!-- JavaScript logic for index page -->
    <script>
        const amountSlider = document.getElementById('amount-slider');
        const tenureSlider = document.getElementById('tenure-slider');
        const amountVal = document.getElementById('amount-val');
        const tenureVal = document.getElementById('tenure-val');
        
        const interestResult = document.getElementById('interest-result');
        const totalResult = document.getElementById('total-result');
        const monthlyResult = document.getElementById('monthly-result');
        
        const applyBtn = document.getElementById('apply-btn');

        // Formatter for Rupiah Currency
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        // Live calculation logic
        function updateCalculator() {
            const amount = parseFloat(amountSlider.value);
            const tenure = parseInt(tenureSlider.value);

            amountVal.textContent = formatRupiah(amount);
            tenureVal.textContent = tenure + ' Bulan';

            // Calculations: 10% flat interest rate
            const interest = amount * 0.10;
            const total = amount + interest;
            const monthly = Math.ceil(total / tenure);

            interestResult.textContent = formatRupiah(interest);
            totalResult.textContent = formatRupiah(total);
            monthlyResult.textContent = formatRupiah(monthly);
        }

        amountSlider.addEventListener('input', updateCalculator);
        tenureSlider.addEventListener('input', updateCalculator);

        // Apply Button action (redirect to dashboard with params)
        applyBtn.addEventListener('click', () => {
            const amount = amountSlider.value;
            const tenure = tenureSlider.value;
            window.location.href = `dashboard.php?amount=${amount}&tenure=${tenure}`;
        });

        // FAQ accordion action
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(q => {
            q.addEventListener('click', () => {
                const parent = q.parentElement;
                const isActive = parent.classList.contains('active');
                
                // Close all FAQ items
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Open current item if it wasn't active
                if (!isActive) {
                    parent.classList.add('active');
                }
            });
        });

        // Initialize Calculator on load
        updateCalculator();
    </script>
</body>
</html>
