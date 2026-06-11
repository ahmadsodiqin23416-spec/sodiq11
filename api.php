<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

// Helper to return JSON responses
function respond($status, $message, $data = null) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Decode JSON input body if content-type is application/json
$input = [];
$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
if (strpos($content_type, 'application/json') !== false) {
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true) ?: [];
} else {
    $input = $_POST;
}

// Determine routing action
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'register':
        $username = isset($input['username']) ? trim($input['username']) : '';
        $password = isset($input['password']) ? $input['password'] : '';
        $name = isset($input['name']) ? trim($input['name']) : '';

        if (empty($username) || empty($password) || empty($name)) {
            respond('error', 'Semua data registrasi harus diisi.');
        }

        if (strlen($username) < 3) {
            respond('error', 'Username minimal 3 karakter.');
        }

        if (strlen($password) < 6) {
            respond('error', 'Password minimal 6 karakter.');
        }

        if (create_user($username, $password, $name)) {
            // Auto login after register
            $_SESSION['username'] = $username;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = 'user';
            respond('success', 'Registrasi berhasil.', ['username' => $username, 'name' => $name, 'role' => 'user']);
        } else {
            respond('error', 'Username sudah terdaftar.');
        }
        break;

    case 'login':
        $username = isset($input['username']) ? trim($input['username']) : '';
        $password = isset($input['password']) ? $input['password'] : '';

        if (empty($username) || empty($password)) {
            respond('error', 'Username dan password harus diisi.');
        }

        $user = get_user($username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            respond('success', 'Login berhasil.', [
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role']
            ]);
        } else {
            respond('error', 'Username atau password salah.');
        }
        break;

    case 'logout':
        session_destroy();
        respond('success', 'Logout berhasil.');
        break;

    case 'get_status':
        if (!isset($_SESSION['username'])) {
            respond('unauthorized', 'Sesi login tidak valid.');
        }
        $username = $_SESSION['username'];
        $user = get_user($username);
        $loan = get_user_loan($username);
        
        respond('success', 'Status data berhasil dimuat.', [
            'user' => [
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role']
            ],
            'loan' => $loan
        ]);
        break;

    case 'apply':
        if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
            respond('unauthorized', 'Silakan login terlebih dahulu.');
        }

        $username = $_SESSION['username'];
        
        // Ensure no active loan application already exists that is pending or active
        $existing = get_user_loan($username);
        if ($existing && ($existing['status'] === 'pending' || $existing['status'] === 'approved')) {
            respond('error', 'Anda sudah memiliki pinjaman aktif atau pengajuan yang sedang diproses.');
        }

        // Parse fields
        $nik = isset($input['nik']) ? trim($input['nik']) : '';
        $phone = isset($input['phone']) ? trim($input['phone']) : '';
        $job = isset($input['job']) ? trim($input['job']) : '';
        $salary = isset($input['salary']) ? floatval($input['salary']) : 0;
        $bank_name = isset($input['bank_name']) ? trim($input['bank_name']) : '';
        $bank_account = isset($input['bank_account']) ? trim($input['bank_account']) : '';
        $bank_holder = isset($input['bank_holder']) ? trim($input['bank_holder']) : '';
        $ktp_photo = isset($input['ktp_photo']) ? $input['ktp_photo'] : ''; // base64
        $amount = isset($input['amount']) ? floatval($input['amount']) : 0;
        $tenure = isset($input['tenure']) ? intval($input['tenure']) : 0; // in months

        if (empty($nik) || empty($phone) || empty($job) || $salary <= 0 || empty($bank_name) || empty($bank_account) || empty($bank_holder) || $amount <= 0 || $tenure <= 0) {
            respond('error', 'Semua data pengajuan harus diisi dengan benar.');
        }

        // Simple calculations
        $interest_rate = 0.10; // 10% flat
        $total_interest = $amount * $interest_rate;
        $total_to_pay = $amount + $total_interest;
        $monthly_payment = ceil($total_to_pay / $tenure);

        $app_data = [
            'username' => $username,
            'nik' => $nik,
            'phone' => $phone,
            'job' => $job,
            'salary' => $salary,
            'bank_name' => $bank_name,
            'bank_account' => $bank_account,
            'bank_holder' => $bank_holder,
            'ktp_photo' => $ktp_photo,
            'amount' => $amount,
            'tenure' => $tenure,
            'monthly_payment' => $monthly_payment,
            'status' => 'pending',
            'remaining_balance' => 0, // set upon approval
            'total_to_pay' => $total_to_pay
        ];

        if (save_loan_application($app_data)) {
            respond('success', 'Pengajuan pinjaman berhasil dikirim.', $app_data);
        } else {
            respond('error', 'Gagal menyimpan data pengajuan.');
        }
        break;

    case 'pay':
        if (!isset($_SESSION['username'])) {
            respond('unauthorized', 'Sesi login tidak valid.');
        }

        $username = $_SESSION['username'];
        $loan = get_user_loan($username);
        
        if (!$loan || $loan['status'] !== 'approved') {
            respond('error', 'Tidak ada pinjaman aktif untuk dibayar.');
        }

        $amount_to_pay = isset($input['amount']) ? floatval($input['amount']) : 0;
        if ($amount_to_pay <= 0) {
            respond('error', 'Jumlah pembayaran tidak valid.');
        }

        if ($amount_to_pay > $loan['remaining_balance']) {
            $amount_to_pay = $loan['remaining_balance']; // Only pay up to remaining balance
        }

        if (pay_installment($loan['id'], $amount_to_pay)) {
            respond('success', 'Pembayaran cicilan berhasil diproses.', [
                'paid_amount' => $amount_to_pay,
                'new_balance' => max(0, $loan['remaining_balance'] - $amount_to_pay)
            ]);
        } else {
            respond('error', 'Gagal memproses pembayaran.');
        }
        break;

    // Admin-only actions
    case 'admin_list':
        if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
            respond('unauthorized', 'Akses ditolak. Hanya untuk Admin.');
        }
        respond('success', 'Data pengajuan berhasil dimuat.', get_all_loans());
        break;

    case 'admin_approve':
        if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
            respond('unauthorized', 'Akses ditolak.');
        }
        $loan_id = isset($input['loan_id']) ? $input['loan_id'] : '';
        if (update_loan_status($loan_id, 'approved')) {
            respond('success', 'Pengajuan pinjaman disetujui.');
        } else {
            respond('error', 'Gagal menyetujui pengajuan pinjaman.');
        }
        break;

    case 'admin_reject':
        if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
            respond('unauthorized', 'Akses ditolak.');
        }
        $loan_id = isset($input['loan_id']) ? $input['loan_id'] : '';
        if (update_loan_status($loan_id, 'rejected')) {
            respond('success', 'Pengajuan pinjaman ditolak.');
        } else {
            respond('error', 'Gagal menolak pengajuan pinjaman.');
        }
        break;

    default:
        respond('error', 'Aksi tidak dikenal.');
        break;
}
?>
