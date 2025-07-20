<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

checkLogin();

$stats = getStats();
$recentRequests = array_slice(getAllRequests(), 0, 5);

// کنترل تم (قالب)
$theme = $_GET['theme'] ?? 'light';
$isDark = $theme === 'dark';

// آمار برای چارت‌ها
$weeklyStats = getWeeklyStats();
$monthlyStats = getMonthlyStats();
$statusStats = getStatusStats();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Vazir', sans-serif; }
        <?php if ($isDark): ?>
        .dark-theme {
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            color: #e5e7eb;
        }
        .dark-card {
            background: rgba(31, 41, 55, 0.9);
            border: 1px solid #374151;
        }
        .dark-widget {
            background: rgba(31, 41, 55, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid #4b5563;
        }
        <?php endif; ?>
        .widget-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body class="<?php echo $isDark ? 'dark-theme min-h-screen' : 'bg-gray-100'; ?>">
    <!-- Navigation -->
    <nav class="<?php echo $isDark ? 'bg-gray-800 shadow-lg' : 'bg-white shadow-lg'; ?>">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold <?php echo $isDark ? 'text-white' : 'text-gray-800'; ?>">مدیریت درخواست پاسخگو رایانه</h1>
                </div>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <!-- Navigation Menu -->
                    <div class="hidden md:flex space-x-2 space-x-reverse">
                        <a href="new_request.php?theme=<?php echo $theme; ?>" class="px-3 py-2 rounded <?php echo $isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <i class="fas fa-plus-circle ml-1"></i>درخواست جدید
                        </a>
                        <a href="search_requests.php?theme=<?php echo $theme; ?>" class="px-3 py-2 rounded <?php echo $isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <i class="fas fa-search ml-1"></i>جستجو
                        </a>
                        <a href="requests.php?theme=<?php echo $theme; ?>" class="px-3 py-2 rounded <?php echo $isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <i class="fas fa-list ml-1"></i>درخواست‌ها
                        </a>
                        <a href="customers.php?theme=<?php echo $theme; ?>" class="px-3 py-2 rounded <?php echo $isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <i class="fas fa-users ml-1"></i>مشتریان
                        </a>
                        <a href="payments.php?theme=<?php echo $theme; ?>" class="px-3 py-2 rounded <?php echo $isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <i class="fas fa-credit-card ml-1"></i>مالی
                        </a>
                    </div>
                    
                    <span class="<?php echo $isDark ? 'text-gray-300' : 'text-gray-700'; ?>">خوش آمدید، <?php echo $_SESSION['username']; ?></span>
                    
                    <!-- Theme Toggle -->
                    <div class="flex space-x-2">
                        <a href="?theme=light" class="px-3 py-1 rounded <?php echo !$isDark ? 'bg-blue-500 text-white' : 'bg-gray-600 text-gray-300'; ?>" title="حالت روشن">
                            <i class="fas fa-sun"></i>
                        </a>
                        <a href="?theme=dark" class="px-3 py-1 rounded <?php echo $isDark ? 'bg-blue-500 text-white' : 'bg-gray-600 text-gray-300'; ?>" title="حالت تیره">
                            <i class="fas fa-moon"></i>
                        </a>
                    </div>
                    
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">خروج</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Quick Actions Menu -->
        <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> overflow-hidden shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?> mb-4">دسترسی سریع</h3>
                <div class="widget-grid">
                    <a href="new_request.php?theme=<?php echo $theme; ?>" class="<?php echo $isDark ? 'dark-widget' : 'bg-gradient-to-r from-blue-500 to-blue-600'; ?> text-white p-4 rounded-lg text-center hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-plus-circle text-2xl mb-2"></i>
                        <div class="font-medium">درخواست جدید</div>
                    </a>
                    <a href="search_requests.php?theme=<?php echo $theme; ?>" class="<?php echo $isDark ? 'dark-widget' : 'bg-gradient-to-r from-green-500 to-green-600'; ?> text-white p-4 rounded-lg text-center hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-search text-2xl mb-2"></i>
                        <div class="font-medium">جستجوی درخواست</div>
                    </a>
                    <a href="requests.php?theme=<?php echo $theme; ?>" class="<?php echo $isDark ? 'dark-widget' : 'bg-gradient-to-r from-purple-500 to-purple-600'; ?> text-white p-4 rounded-lg text-center hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-list text-2xl mb-2"></i>
                        <div class="font-medium">مدیریت درخواست‌ها</div>
                    </a>
                    <a href="customers.php?theme=<?php echo $theme; ?>" class="<?php echo $isDark ? 'dark-widget' : 'bg-gradient-to-r from-indigo-500 to-indigo-600'; ?> text-white p-4 rounded-lg text-center hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-users text-2xl mb-2"></i>
                        <div class="font-medium">مشاهده مشتریان</div>
                    </a>
                    <a href="contacts.php?theme=<?php echo $theme; ?>" class="<?php echo $isDark ? 'dark-widget' : 'bg-gradient-to-r from-orange-500 to-orange-600'; ?> text-white p-4 rounded-lg text-center hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-user-plus text-2xl mb-2"></i>
                        <div class="font-medium">ثبت مشتری جدید</div>
                    </a>
                    <a href="communications.php?theme=<?php echo $theme; ?>" class="<?php echo $isDark ? 'dark-widget' : 'bg-gradient-to-r from-teal-500 to-teal-600'; ?> text-white p-4 rounded-lg text-center hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-phone text-2xl mb-2"></i>
                        <div class="font-medium">مدیریت ارتباطات</div>
                    </a>
                    <a href="payments.php?theme=<?php echo $theme; ?>" class="<?php echo $isDark ? 'dark-widget' : 'bg-gradient-to-r from-yellow-500 to-yellow-600'; ?> text-white p-4 rounded-lg text-center hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-credit-card text-2xl mb-2"></i>
                        <div class="font-medium">مدیریت مالی</div>
                    </a>
                    <?php if ($_SESSION['is_admin']): ?>
                    <a href="users.php?theme=<?php echo $theme; ?>" class="<?php echo $isDark ? 'dark-widget' : 'bg-gradient-to-r from-red-500 to-red-600'; ?> text-white p-4 rounded-lg text-center hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-user-cog text-2xl mb-2"></i>
                        <div class="font-medium">مدیریت کاربران</div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-list text-2xl text-blue-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> truncate">کل درخواست‌ها</dt>
                                <dd class="text-lg font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>"><?php echo en2fa($stats['total_requests']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-2xl text-yellow-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> truncate">در حال پردازش</dt>
                                <dd class="text-lg font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>"><?php echo en2fa($stats['pending_requests']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-2xl text-green-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> truncate">کل مشتریان</dt>
                                <dd class="text-lg font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>"><?php echo en2fa($stats['total_customers']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-money-bill-wave text-2xl text-purple-500"></i>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> truncate">کل درآمد</dt>
                                <dd class="text-lg font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>"><?php echo en2fa(formatNumber($stats['total_income'])); ?> تومان</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Weekly Income Chart -->
            <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>">درآمد هفتگی</h3>
                    <p class="mt-1 text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">آمار درآمد ۷ روز گذشته</p>
                </div>
                <div class="px-4 pb-5">
                    <div class="chart-container">
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Distribution Chart -->
            <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>">توزیع وضعیت درخواست‌ها</h3>
                    <p class="mt-1 text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">نمودار دایره‌ای وضعیت‌ها</p>
                </div>
                <div class="px-4 pb-5">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>">آخرین درخواست‌ها</h3>
                <p class="mt-1 max-w-2xl text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">لیست آخرین درخواست‌های ثبت شده</p>
            </div>
            <ul class="divide-y <?php echo $isDark ? 'divide-gray-600' : 'divide-gray-200'; ?>">
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
                                <div class="text-sm font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>"><?php echo $request['title']; ?></div>
                                <div class="text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>"><?php echo $request['customer_name']; ?> - <?php echo $request['customer_phone']; ?></div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?php echo $request['status'] == 'تکمیل شده' ? 'bg-green-100 text-green-800' : 
                                    ($request['status'] == 'لغو شده' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?php echo $request['status']; ?>
                            </span>
                            <div class="mr-4 text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">
                                کد رهگیری: <?php echo en2fa($request['tracking_code']); ?>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        // Weekly Income Chart
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyChart = new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($weeklyStats, 'date')); ?>,
                datasets: [{
                    label: 'درآمد روزانه',
                    data: <?php echo json_encode(array_column($weeklyStats, 'income')); ?>,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '<?php echo $isDark ? "#e5e7eb" : "#374151"; ?>'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '<?php echo $isDark ? "#9ca3af" : "#6b7280"; ?>'
                        },
                        grid: {
                            color: '<?php echo $isDark ? "#374151" : "#f3f4f6"; ?>'
                        }
                    },
                    y: {
                        ticks: {
                            color: '<?php echo $isDark ? "#9ca3af" : "#6b7280"; ?>'
                        },
                        grid: {
                            color: '<?php echo $isDark ? "#374151" : "#f3f4f6"; ?>'
                        }
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($statusStats, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($statusStats, 'count')); ?>,
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '<?php echo $isDark ? "#e5e7eb" : "#374151"; ?>',
                            padding: 20
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>