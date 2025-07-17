<?php
require_once 'config.php';

// تابع لاگین
function login($username, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        return true;
    }
    return false;
}

// تابع لاگ اوت
function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

// تابع بررسی لاگین
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

// تابع بررسی دسترسی مدیر
function checkAdmin() {
    checkLogin();
    if (!$_SESSION['is_admin']) {
        header('Location: dashboard.php');
        exit;
    }
}

// تابع تولید کد رهگیری
function generateTrackingCode() {
    return str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
}

// تابع تولید شناسه کاربر
function generateUserIdCode() {
    global $pdo;
    
    do {
        $code = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id_code = ?");
        $stmt->execute([$code]);
        $exists = $stmt->fetchColumn();
    } while ($exists);
    
    return $code;
}

// تابع ایجاد کاربر جدید
function createUser($username, $password, $email, $phone) {
    global $pdo;
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $userIdCode = generateUserIdCode();
    
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, phone, user_id_code) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$username, $hashedPassword, $email, $phone, $userIdCode]);
}

// تابع دریافت اطلاعات کاربر
function getUser($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// تابع دریافت تمام کاربران
function getAllUsers() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// تابع ایجاد مشتری جدید
function createCustomer($name, $phone, $email = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO customers (name, phone, email) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone, $email]);
    return $pdo->lastInsertId();
}

// تابع دریافت مشتری بر اساس شماره تلفن
function getCustomerByPhone($phone) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE phone = ?");
    $stmt->execute([$phone]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// تابع دریافت تمام مشتریان
function getAllCustomers() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// تابع دریافت مشتری
function getCustomer($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// تابع ایجاد درخواست جدید
function createRequest($customerId, $title, $deviceModel, $imei1, $imei2, $problemDescription, $estimatedDuration, $actionsRequired, $cost) {
    global $pdo;
    
    $trackingCode = generateTrackingCode();
    $registrationDate = jalali_date('Y/m/d');
    
    $stmt = $pdo->prepare("INSERT INTO requests (customer_id, title, device_model, imei1, imei2, problem_description, registration_date, estimated_duration, actions_required, cost, tracking_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$customerId, $title, $deviceModel, $imei1, $imei2, $problemDescription, $registrationDate, $estimatedDuration, $actionsRequired, $cost, $trackingCode]);
    
    $requestId = $pdo->lastInsertId();
    
    // ثبت بدهکاری
    if ($cost > 0) {
        $stmt = $pdo->prepare("INSERT INTO payments (customer_id, request_id, amount, payment_type, description) VALUES (?, ?, ?, 'بدهکاری', ?)");
        $stmt->execute([$customerId, $requestId, $cost, "بدهکاری درخواست: $title"]);
    }
    
    return $requestId;
}

// تابع دریافت تمام درخواست‌ها
function getAllRequests() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT r.*, c.name as customer_name, c.phone as customer_phone 
                        FROM requests r 
                        JOIN customers c ON r.customer_id = c.id 
                        ORDER BY r.created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// تابع دریافت درخواست
function getRequest($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT r.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email 
                          FROM requests r 
                          JOIN customers c ON r.customer_id = c.id 
                          WHERE r.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// تابع به‌روزرسانی درخواست
function updateRequest($id, $title, $deviceModel, $imei1, $imei2, $problemDescription, $estimatedDuration, $actionsRequired, $cost, $status) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE requests SET title = ?, device_model = ?, imei1 = ?, imei2 = ?, problem_description = ?, estimated_duration = ?, actions_required = ?, cost = ?, status = ? WHERE id = ?");
    return $stmt->execute([$title, $deviceModel, $imei1, $imei2, $problemDescription, $estimatedDuration, $actionsRequired, $cost, $status, $id]);
}

// تابع ثبت پرداخت
function addPayment($customerId, $requestId, $amount, $description, $receiptImage = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO payments (customer_id, request_id, amount, payment_type, description, receipt_image) VALUES (?, ?, ?, 'واریز', ?, ?)");
    return $stmt->execute([$customerId, $requestId, $amount, $description, $receiptImage]);
}

// تابع دریافت پرداخت‌های مشتری
function getCustomerPayments($customerId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT p.*, r.title as request_title, r.tracking_code 
                          FROM payments p 
                          LEFT JOIN requests r ON p.request_id = r.id 
                          WHERE p.customer_id = ? 
                          ORDER BY p.created_at DESC");
    $stmt->execute([$customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// تابع محاسبه موجودی مشتری
function getCustomerBalance($customerId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT 
                            SUM(CASE WHEN payment_type = 'واریز' THEN amount ELSE 0 END) as total_paid,
                            SUM(CASE WHEN payment_type = 'بدهکاری' THEN amount ELSE 0 END) as total_debt
                          FROM payments 
                          WHERE customer_id = ?");
    $stmt->execute([$customerId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return ($result['total_paid'] ?? 0) - ($result['total_debt'] ?? 0);
}

// تابع دریافت درخواست‌های مشتری
function getCustomerRequests($customerId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM requests WHERE customer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$customerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// تابع ثبت تماس
function addContact($customerId, $contactType, $subject, $message) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO contacts (customer_id, contact_type, subject, message) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$customerId, $contactType, $subject, $message]);
}

// تابع دریافت تماس‌ها
function getAllContacts() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT co.*, c.name as customer_name, c.phone as customer_phone 
                        FROM contacts co 
                        JOIN customers c ON co.customer_id = c.id 
                        ORDER BY co.contact_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// تابع دریافت آمار
function getStats() {
    global $pdo;
    
    $stats = [];
    
    // تعداد کل درخواست‌ها
    $stmt = $pdo->query("SELECT COUNT(*) FROM requests");
    $stats['total_requests'] = $stmt->fetchColumn();
    
    // تعداد درخواست‌های در حال پردازش
    $stmt = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'در حال پردازش'");
    $stats['pending_requests'] = $stmt->fetchColumn();
    
    // تعداد درخواست‌های تکمیل شده
    $stmt = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'تکمیل شده'");
    $stats['completed_requests'] = $stmt->fetchColumn();
    
    // تعداد کل مشتریان
    $stmt = $pdo->query("SELECT COUNT(*) FROM customers");
    $stats['total_customers'] = $stmt->fetchColumn();
    
    // کل درآمد
    $stmt = $pdo->query("SELECT SUM(amount) FROM payments WHERE payment_type = 'واریز'");
    $stats['total_income'] = $stmt->fetchColumn() ?? 0;
    
    // کل بدهکاری
    $stmt = $pdo->query("SELECT SUM(amount) FROM payments WHERE payment_type = 'بدهکاری'");
    $stats['total_debt'] = $stmt->fetchColumn() ?? 0;
    
    return $stats;
}

// تابع فرمت عدد به فارسی
function formatNumber($number) {
    return number_format($number, 0, '.', ',');
}

// تابع تبدیل اعداد انگلیسی به فارسی
function en2fa($str) {
    $en = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $fa = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    return str_replace($en, $fa, $str);
}

// تابع تبدیل اعداد فارسی به انگلیسی
function fa2en($str) {
    $fa = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $en = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    return str_replace($fa, $en, $str);
}
?>