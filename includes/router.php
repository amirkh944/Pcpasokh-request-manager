<?php
// توابع مسیریابی

/**
 * تولید URL برای صفحات
 */
function getPageUrl($page) {
    return "index.php?page=" . urlencode($page);
}

/**
 * بارگذاری صفحه مورد نظر
 */
function loadPage($page) {
    $allowedPages = [
        'dashboard',
        'new_request', 
        'search_requests',
        'requests',
        'customers',
        'payments',
        'users',
        'view_request',
        'view_customer',
        'edit_request',
        'add_payment',
        'print_receipt',
        'print_customer',
        'communications',
        'send_sms',
        'contacts',
        'logout'
    ];
    
    if (in_array($page, $allowedPages)) {
        $filePath = "views/{$page}.php";
        if (file_exists($filePath)) {
            include $filePath;
            return true;
        }
    }
    
    return false;
}

/**
 * ریدایرکت به صفحه
 */
function redirect($page, $params = []) {
    $url = getPageUrl($page);
    if (!empty($params)) {
        $url .= '&' . http_build_query($params);
    }
    header("Location: " . $url);
    exit;
}

/**
 * تنظیم عنوان صفحه
 */
function setPageTitle($title) {
    global $pageTitle;
    $pageTitle = $title . ' - سیستم مدیریت درخواست پاسخگو رایانه';
}

/**
 * فعال کردن Chart.js
 */
function enableCharts() {
    global $includeChart;
    $includeChart = true;
}

/**
 * اضافه کردن استایل سفارشی
 */
function addCustomStyles($styles) {
    global $customStyles;
    $customStyles = isset($customStyles) ? $customStyles . $styles : $styles;
}

/**
 * اضافه کردن اسکریپت سفارشی
 */
function addCustomScripts($scripts) {
    global $customScripts;
    $customScripts = isset($customScripts) ? $customScripts . $scripts : $scripts;
}
?>