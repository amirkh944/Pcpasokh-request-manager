<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

checkLogin();

// دریافت تمام پرداخت‌ها
$stmt = $pdo->query("SELECT p.*, c.name as customer_name, c.phone as customer_phone, r.title as request_title, r.tracking_code 
                     FROM payments p 
                     JOIN customers c ON p.customer_id = c.id 
                     LEFT JOIN requests r ON p.request_id = r.id 
                     ORDER BY p.created_at DESC");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// آمار مالی
$stats = getStats();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت مالی - مدیریت درخواست پاسخگو رایانه</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Vazir', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="dashboard.php" class="text-xl font-bold text-gray-800">مدیریت درخواست پاسخگو رایانه</a>
                </div>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <a href="dashboard.php" class="text-gray-700 hover:text-gray-900">داشبورد</a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">خروج</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- آمار مالی -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-up text-2xl text-green-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">کل درآمد</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo en2fa(formatNumber($stats['total_income'])); ?> تومان</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-down text-2xl text-red-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">کل بدهکاری</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo en2fa(formatNumber($stats['total_debt'])); ?> تومان</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-balance-scale text-2xl text-blue-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">موجودی کل</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo en2fa(formatNumber($stats['total_income'] - $stats['total_debt'])); ?> تومان</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- لیست پرداخت‌ها -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">مدیریت مالی</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">لیست تمام پرداخت‌ها و بدهکاری‌ها</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مشتری</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">درخواست</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">توضیحات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رسید</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo en2fa(jalali_date('Y/m/d', strtotime($payment['created_at']))); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $payment['customer_name']; ?><br>
                                <span class="text-gray-500"><?php echo $payment['customer_phone']; ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php if ($payment['request_title']): ?>
                                    <?php echo $payment['request_title']; ?><br>
                                    <span class="text-gray-500"><?php echo en2fa($payment['tracking_code']); ?></span>
                                <?php else: ?>
                                    <span class="text-gray-500">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php echo $payment['payment_type'] == 'واریز' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $payment['payment_type']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span class="<?php echo $payment['payment_type'] == 'واریز' ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo en2fa(formatNumber($payment['amount'])); ?> تومان
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo $payment['description']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($payment['receipt_image']): ?>
                                    <a href="uploads/receipts/<?php echo $payment['receipt_image']; ?>" target="_blank" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-image"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">ندارد</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>