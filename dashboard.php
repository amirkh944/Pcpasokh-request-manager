<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

checkLogin();

$stats = getStats();
$recentRequests = array_slice(getAllRequests(), 0, 5);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد - مدیریت درخواست پاسخگو رایانه</title>
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
                    <h1 class="text-xl font-bold text-gray-800">مدیریت درخواست پاسخگو رایانه</h1>
                </div>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <span class="text-gray-700">خوش آمدید، <?php echo $_SESSION['username']; ?></span>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">خروج</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Menu -->
        <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <a href="new_request.php" class="bg-blue-500 text-white p-4 rounded-lg text-center hover:bg-blue-600 transition">
                        <i class="fas fa-plus-circle text-2xl mb-2"></i>
                        <div>درخواست جدید</div>
                    </a>
                    <a href="requests.php" class="bg-green-500 text-white p-4 rounded-lg text-center hover:bg-green-600 transition">
                        <i class="fas fa-list text-2xl mb-2"></i>
                        <div>مدیریت درخواست‌ها</div>
                    </a>
                    <a href="customers.php" class="bg-purple-500 text-white p-4 rounded-lg text-center hover:bg-purple-600 transition">
                        <i class="fas fa-users text-2xl mb-2"></i>
                        <div>مدیریت مشتریان</div>
                    </a>
                    <a href="payments.php" class="bg-yellow-500 text-white p-4 rounded-lg text-center hover:bg-yellow-600 transition">
                        <i class="fas fa-credit-card text-2xl mb-2"></i>
                        <div>مدیریت مالی</div>
                    </a>
                    <a href="contacts.php" class="bg-indigo-500 text-white p-4 rounded-lg text-center hover:bg-indigo-600 transition">
                        <i class="fas fa-phone text-2xl mb-2"></i>
                        <div>مدیریت تماس‌ها</div>
                    </a>
                    <?php if ($_SESSION['is_admin']): ?>
                    <a href="users.php" class="bg-red-500 text-white p-4 rounded-lg text-center hover:bg-red-600 transition">
                        <i class="fas fa-user-cog text-2xl mb-2"></i>
                        <div>مدیریت کاربران</div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-list text-2xl text-blue-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">کل درخواست‌ها</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo en2fa($stats['total_requests']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-2xl text-yellow-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">در حال پردازش</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo en2fa($stats['pending_requests']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-2xl text-green-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">کل مشتریان</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo en2fa($stats['total_customers']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-money-bill-wave text-2xl text-purple-500"></i>
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
        </div>

        <!-- Recent Requests -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">آخرین درخواست‌ها</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">لیست آخرین درخواست‌های ثبت شده</p>
            </div>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($recentRequests as $request): ?>
                <li>
                    <div class="px-4 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-mobile-alt text-gray-600"></i>
                                </div>
                            </div>
                            <div class="mr-4">
                                <div class="text-sm font-medium text-gray-900"><?php echo $request['title']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $request['customer_name']; ?> - <?php echo $request['customer_phone']; ?></div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?php echo $request['status'] == 'تکمیل شده' ? 'bg-green-100 text-green-800' : 
                                    ($request['status'] == 'لغو شده' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?php echo $request['status']; ?>
                            </span>
                            <div class="mr-4 text-sm text-gray-500">
                                کد رهگیری: <?php echo en2fa($request['tracking_code']); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="bg-gray-50 px-4 py-3 text-center">
                <a href="requests.php" class="text-sm text-indigo-600 hover:text-indigo-500">
                    مشاهده همه درخواست‌ها →
                </a>
            </div>
        </div>
    </div>
</body>
</html>