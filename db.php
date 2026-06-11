<?php
// Database Helper using local JSON file
define('DB_FILE', __DIR__ . '/database.json');

// Initialize database with default structure and admin user
function init_db() {
    $default_admin_user = 'sodik8';
    $default_admin_pass = 'sodikpassword';
    
    if (!file_exists(DB_FILE)) {
        $default_data = [
            'users' => [
                $default_admin_user => [
                    'username' => $default_admin_user,
                    'password' => password_hash($default_admin_pass, PASSWORD_DEFAULT),
                    'name' => 'Administrator',
                    'role' => 'admin',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ],
            'applications' => [],
            'payments' => []
        ];
        save_db($default_data);
    } else {
        // Migration: Ensure the new admin exists and the old default 'admin' is removed
        $json = file_get_contents(DB_FILE);
        $db = json_decode($json, true) ?: ['users' => [], 'applications' => [], 'payments' => []];
        
        if (!isset($db['users'][$default_admin_user])) {
            if (isset($db['users']['admin'])) {
                unset($db['users']['admin']);
            }
            $db['users'][$default_admin_user] = [
                'username' => $default_admin_user,
                'password' => password_hash($default_admin_pass, PASSWORD_DEFAULT),
                'name' => 'Administrator',
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s')
            ];
            save_db($db);
        }
    }
}

// Load database contents
function load_db() {
    init_db();
    $json = file_get_contents(DB_FILE);
    return json_decode($json, true) ?: ['users' => [], 'applications' => [], 'payments' => []];
}

// Save database contents
function save_db($data) {
    file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
}

// Helper: Get user by username
function get_user($username) {
    $db = load_db();
    return isset($db['users'][$username]) ? $db['users'][$username] : null;
}

// Helper: Create user
function create_user($username, $password, $name, $role = 'user') {
    $db = load_db();
    if (isset($db['users'][$username])) {
        return false;
    }
    $db['users'][$username] = [
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'name' => $name,
        'role' => $role,
        'created_at' => date('Y-m-d H:i:s')
    ];
    save_db($db);
    return true;
}

// Helper: Get user's active loan application
function get_user_loan($username) {
    $db = load_db();
    foreach ($db['applications'] as $app) {
        if ($app['username'] === $username) {
            return $app;
        }
    }
    return null;
}

// Helper: Save/Update loan application
function save_loan_application($app_data) {
    $db = load_db();
    
    // Check if user already has an application
    $found_index = -1;
    foreach ($db['applications'] as $index => $app) {
        if ($app['username'] === $app_data['username']) {
            $found_index = $index;
            break;
        }
    }

    if ($found_index !== -1) {
        // Update existing application
        $db['applications'][$found_index] = array_merge($db['applications'][$found_index], $app_data);
    } else {
        // Add new application
        $app_data['id'] = uniqid('loan_', true);
        $app_data['created_at'] = date('Y-m-d H:i:s');
        $db['applications'][] = $app_data;
    }
    
    save_db($db);
    return true;
}

// Helper: Get all applications
function get_all_loans() {
    $db = load_db();
    return $db['applications'];
}

// Helper: Update loan status
function update_loan_status($loan_id, $status) {
    $db = load_db();
    foreach ($db['applications'] as &$app) {
        if ($app['id'] === $loan_id) {
            $app['status'] = $status;
            if ($status === 'approved') {
                $app['remaining_balance'] = $app['amount'] + ($app['amount'] * 0.1); // Amount + 10% mock interest fee
            }
            save_db($db);
            return true;
        }
    }
    return false;
}

// Helper: Pay installment
function pay_installment($loan_id, $payment_amount) {
    $db = load_db();
    foreach ($db['applications'] as &$app) {
        if ($app['id'] === $loan_id && $app['status'] === 'approved') {
            $new_balance = max(0, $app['remaining_balance'] - $payment_amount);
            $app['remaining_balance'] = $new_balance;
            
            if ($new_balance <= 0) {
                $app['status'] = 'paid';
            }
            
            // Record payment log
            $payment_log = [
                'id' => uniqid('pay_', true),
                'loan_id' => $loan_id,
                'username' => $app['username'],
                'amount' => $payment_amount,
                'payment_date' => date('Y-m-d H:i:s')
            ];
            $db['payments'][] = $payment_log;
            
            save_db($db);
            return true;
        }
    }
    return false;
}
?>
