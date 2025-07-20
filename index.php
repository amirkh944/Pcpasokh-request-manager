<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// مسیریابی
$request = $_GET['page'] ?? 'dashboard';

// بررسی لاگین برای صفحات غیر از لاگین
if ($request !== 'login' && !isset($_SESSION['user_id'])) {
    $request = 'login';
}

// نگاشت مسیرها به فایل‌های view
$routes = [
    'dashboard' => 'views/dashboard.php',
    'login' => 'views/login.php',
    'new-request' => 'views/new_request.php',
    'search-requests' => 'views/search_requests.php',
    'requests' => 'views/requests.php',
    'customers' => 'views/customers.php',
    'payments' => 'views/payments.php',
    'communications' => 'views/communications.php',
    'users' => 'views/users.php',
    'add-payment' => 'views/add_payment.php',
    'contacts' => 'views/contacts.php',
    'edit-request' => 'views/edit_request.php',
    'view-request' => 'views/view_request.php',
    'view-customer' => 'views/view_customer.php',
    'print-customer' => 'views/print_customer.php',
    'print-receipt' => 'views/print_receipt.php',
    'send-sms' => 'views/send_sms.php',
    'track-request' => 'views/track_request.php',
    'logout' => 'views/logout.php'
];

// بررسی وجود مسیر
if (!isset($routes[$request])) {
    $request = 'dashboard';
}

// تنظیم عنوان صفحه
$pageTitles = [
    'dashboard' => 'داشبورد - سیستم مدیریت درخواست پاسخگو رایانه',
    'login' => 'ورود به سیستم - پاسخگو رایانه',
    'new-request' => 'درخواست جدید - پاسخگو رایانه',
    'search-requests' => 'جستجوی درخواست‌ها - پاسخگو رایانه',
    'requests' => 'لیست درخواست‌ها - پاسخگو رایانه',
    'customers' => 'مدیریت مشتریان - پاسخگو رایانه',
    'payments' => 'مدیریت مالی - پاسخگو رایانه',
    'communications' => 'ارتباطات - پاسخگو رایانه',
    'users' => 'مدیریت کاربران - پاسخگو رایانه',
    'add-payment' => 'افزودن پرداخت - پاسخگو رایانه',
    'contacts' => 'تماس‌ها - پاسخگو رایانه',
    'edit-request' => 'ویرایش درخواست - پاسخگو رایانه',
    'view-request' => 'مشاهده درخواست - پاسخگو رایانه',
    'view-customer' => 'مشاهده مشتری - پاسخگو رایانه',
    'print-customer' => 'چاپ اطلاعات مشتری - پاسخگو رایانه',
    'print-receipt' => 'چاپ رسید - پاسخگو رایانه',
    'send-sms' => 'ارسال پیامک - پاسخگو رایانه',
    'track-request' => 'پیگیری درخواست - پاسخگو رایانه'
];

$pageTitle = $pageTitles[$request] ?? 'سیستم مدیریت درخواست پاسخگو رایانه';

// بارگذاری فایل view
$viewFile = $routes[$request];
if (file_exists($viewFile)) {
    include $viewFile;
} else {
    // صفحه 404
    $pageTitle = 'صفحه یافت نشد - پاسخگو رایانه';
    include 'templates/header.php';
    ?>
    <div class="text-center py-12">
        <h1 class="text-4xl font-bold text-red-500 mb-4">404</h1>
        <p class="text-xl text-gray-600 mb-8">صفحه مورد نظر یافت نشد</p>
        <a href="dashboard" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg">
            بازگشت به داشبورد
        </a>
    </div>
    <?php
    include 'templates/footer.php';
}
?>