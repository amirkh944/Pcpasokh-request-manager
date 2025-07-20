<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/router.php';
require_once __DIR__ . '/../includes/router.php';

checkLogin();

// کنترل تم
$theme = $_GET['theme'] ?? 'light';
$isDark = $theme === 'dark';

// تنظیم عنوان صفحه و ویژگی‌ها
setPageTitle('داشبورد');
enableCharts();

// دریافت آمار کلی
$stats = getStats();
$recentRequests = array_slice(getAllRequests(), 0, 5);

// آمار برای چارت‌ها
$weeklyStats = getWeeklyStats();
$monthlyStats = getMonthlyStats();
$statusStats = getStatusStats();

// اضافه کردن اسکریپت چارت‌ها
$chartScripts = "
        // نمودار آمار هفتگی
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: " . json_encode(array_column($weeklyStats, 'day')) . ",
                datasets: [{
                    label: 'درخواست‌های روزانه',
                    data: " . json_encode(array_column($weeklyStats, 'count')) . ",
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
                            color: '" . ($isDark ? "#e2e8f0" : "#1e293b") . "',
                            font: { family: 'Vazir' }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '" . ($isDark ? "#94a3b8" : "#64748b") . "',
                            font: { family: 'Vazir' }
                        },
                        grid: {
                            color: '" . ($isDark ? "#475569" : "#e2e8f0") . "'
                        }
                    },
                    x: {
                        ticks: {
                            color: '" . ($isDark ? "#94a3b8" : "#64748b") . "',
                            font: { family: 'Vazir' }
                        },
                        grid: {
                            color: '" . ($isDark ? "#475569" : "#e2e8f0") . "'
                        }
                    }
                }
            }
        });
        
        // نمودار آمار ماهانه
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: " . json_encode(array_column($monthlyStats, 'month')) . ",
                datasets: [{
                    label: 'درخواست‌های ماهانه',
                    data: " . json_encode(array_column($monthlyStats, 'count')) . ",
                    backgroundColor: '#10b981',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '" . ($isDark ? "#e2e8f0" : "#1e293b") . "',
                            font: { family: 'Vazir' }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '" . ($isDark ? "#94a3b8" : "#64748b") . "',
                            font: { family: 'Vazir' }
                        },
                        grid: {
                            color: '" . ($isDark ? "#475569" : "#e2e8f0") . "'
                        }
                    },
                    x: {
                        ticks: {
                            color: '" . ($isDark ? "#94a3b8" : "#64748b") . "',
                            font: { family: 'Vazir' }
                        },
                        grid: {
                            color: '" . ($isDark ? "#475569" : "#e2e8f0") . "'
                        }
                    }
                }
            }
        });
        
        // نمودار وضعیت درخواست‌ها
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: " . json_encode(array_column($statusStats, 'status')) . ",
                datasets: [{
                    data: " . json_encode(array_column($statusStats, 'count')) . ",
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
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
                            color: '" . ($isDark ? "#e2e8f0" : "#1e293b") . "',
                            font: { family: 'Vazir' },
                            padding: 20
                        }
                    }
                }
            }
        });
";

addCustomScripts($chartScripts);

// شامل کردن هدر
include __DIR__ . '/../templates/header.php';
?>

        <!-- کارت‌های آمار کلی -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- کل درخواست‌ها -->
            <div class="stat-card <?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                            کل درخواست‌ها
                        </p>
                        <p class="text-2xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                            <?php echo en2fa($stats['total_requests']); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- در حال پردازش -->
            <div class="stat-card <?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                            در حال پردازش
                        </p>
                        <p class="text-2xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                            <?php echo en2fa($stats['pending_requests']); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- تکمیل شده -->
            <div class="stat-card <?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                            تکمیل شده
                        </p>
                        <p class="text-2xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                            <?php echo en2fa($stats['completed_requests']); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- کل مشتریان -->
            <div class="stat-card <?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                            کل مشتریان
                        </p>
                        <p class="text-2xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                            <?php echo en2fa($stats['total_customers']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ویجت‌های دسترسی سریع -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- درخواست جدید -->
            <a href="<?php echo getPageUrl('new_request'); ?>?theme=<?php echo $theme; ?>" class="quick-access-item block">
                <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-plus text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?> mb-2">
                        درخواست جدید
                    </h3>
                    <p class="text-sm <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                        ثبت درخواست جدید مشتری
                    </p>
                </div>
            </a>
            
            <!-- جستجو -->
            <a href="<?php echo getPageUrl('search_requests'); ?>?theme=<?php echo $theme; ?>" class="quick-access-item block">
                <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?> mb-2">
                        جستجو
                    </h3>
                    <p class="text-sm <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                        جستجو در درخواست‌ها
                    </p>
                </div>
            </a>
            
            <!-- پرداخت‌ها -->
            <a href="<?php echo getPageUrl('payments'); ?>?theme=<?php echo $theme; ?>" class="quick-access-item block">
                <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-credit-card text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?> mb-2">
                        مالی
                    </h3>
                    <p class="text-sm <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                        مدیریت پرداخت‌ها
                    </p>
                </div>
            </a>
            
            <!-- مشتریان -->
            <a href="<?php echo getPageUrl('customers'); ?>?theme=<?php echo $theme; ?>" class="quick-access-item block">
                <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?> mb-2">
                        مشتریان
                    </h3>
                    <p class="text-sm <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                        مدیریت مشتریان
                    </p>
                </div>
            </a>
        </div>

        <!-- نمودارها و آمار تفصیلی -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- نمودار آمار هفتگی -->
            <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?> mb-6">
                    آمار هفت روز گذشته
                </h3>
                <div class="chart-container">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
            
            <!-- نمودار وضعیت درخواست‌ها -->
            <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?> mb-6">
                    وضعیت درخواست‌ها
                </h3>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- نمودار آمار ماهانه -->
        <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6 mb-8">
            <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?> mb-6">
                آمار شش ماه گذشته
            </h3>
            <div class="chart-container">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- جدیدترین درخواست‌ها -->
        <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b <?php echo $isDark ? 'border-gray-600' : 'border-gray-200'; ?>">
                <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                    جدیدترین درخواست‌ها
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y <?php echo $isDark ? 'divide-gray-600' : 'divide-gray-200'; ?>">
                    <thead class="<?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?>">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                کد رهگیری
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                مشتری
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                عنوان
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                وضعیت
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                تاریخ
                            </th>
                        </tr>
                    </thead>
                    <tbody class="<?php echo $isDark ? 'bg-gray-800' : 'bg-white'; ?> divide-y <?php echo $isDark ? 'divide-gray-600' : 'divide-gray-200'; ?>">
                        <?php foreach ($recentRequests as $request): ?>
                        <tr class="<?php echo $isDark ? 'hover:bg-gray-700' : 'hover:bg-gray-50'; ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?php echo getPageUrl('view_request'); ?>?id=<?php echo $request['id']; ?>&theme=<?php echo $theme; ?>" 
                                   class="text-blue-500 hover:text-blue-700 font-medium">
                                    <?php echo en2fa($request['tracking_code']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?>">
                                <?php echo htmlspecialchars($request['customer_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?>">
                                <?php echo htmlspecialchars($request['title']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $statusClass = '';
                                switch($request['status']) {
                                    case 'در حال پردازش':
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'تکمیل شده':
                                        $statusClass = 'bg-green-100 text-green-800';
                                        break;
                                    case 'لغو شده':
                                        $statusClass = 'bg-red-100 text-red-800';
                                        break;
                                }
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                                    <?php echo $request['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">
                                <?php echo jalali_date('Y/m/d', strtotime($request['created_at'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 <?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?>">
                <a href="<?php echo getPageUrl('requests'); ?>?theme=<?php echo $theme; ?>" 
                   class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                    مشاهده همه درخواست‌ها →
                </a>
            </div>
        </div>

<?php
// شامل کردن فوتر
include __DIR__ . '/../templates/footer.php';
?>