<?php
checkLogin();

// کنترل تم
$theme = $_GET['theme'] ?? 'light';
$isDark = $theme === 'dark';

$requests = getAllRequests();
$pageTitle = 'مدیریت درخواست‌ها - پاسخگو رایانه';
include '../templates/header.php';
?>

        
        <!-- عنوان و اقدامات سریع -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 animate-slide-in">
            <div>
                <h1 class="text-3xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                    <i class="fas fa-list ml-3 text-purple-500"></i>
                    مدیریت درخواست‌ها
                </h1>
                <p class="mt-2 <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                    مشاهده و مدیریت تمام درخواست‌های ثبت شده
                </p>
            </div>
            
            <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
                <a href="search-requests?theme=<?php echo $theme; ?>" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                    <i class="fas fa-search ml-2"></i>
                    جستجوی پیشرفته
                </a>
                <a href="new-request?theme=<?php echo $theme; ?>" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                    <i class="fas fa-plus-circle ml-2"></i>
                    درخواست جدید
                </a>
            </div>
        </div>

        <!-- آمار سریع -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 animate-slide-in">
            <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clipboard-list text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">کل درخواست‌ها</p>
                        <p class="text-2xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>"><?php echo en2fa(count($requests)); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">در انتظار</p>
                        <p class="text-2xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                            <?php echo en2fa(count(array_filter($requests, function($r) { return $r['status'] == 'در حال بررسی'; }))); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">تکمیل شده</p>
                        <p class="text-2xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                            <?php echo en2fa(count(array_filter($requests, function($r) { return $r['status'] == 'تکمیل شده'; }))); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-times-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">لغو شده</p>
                        <p class="text-2xl font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                            <?php echo en2fa(count(array_filter($requests, function($r) { return $r['status'] == 'لغو شده'; }))); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول درخواست‌ها -->
        <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl overflow-hidden animate-slide-in">
            <div class="px-6 py-5 border-b <?php echo $isDark ? 'border-gray-600' : 'border-gray-200'; ?>">
                <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                    <i class="fas fa-table ml-2 text-blue-500"></i>
                    لیست درخواست‌ها
                </h3>
                <p class="mt-1 <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?> text-sm">
                    نمایش تمام درخواست‌های ثبت شده به همراه وضعیت و جزئیات
                </p>
            </div>
            
            <div class="overflow-x-auto">
                <?php if (empty($requests)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-6xl <?php echo $isDark ? 'text-gray-400' : 'text-gray-300'; ?> mb-4"></i>
                    <h3 class="text-lg font-medium <?php echo $isDark ? 'dark-text' : 'light-text'; ?> mb-2">هیچ درخواستی یافت نشد</h3>
                    <p class="<?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?> mb-6">هنوز درخواستی ثبت نشده است</p>
                    <a href="new-request?theme=<?php echo $theme; ?>" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus-circle ml-2"></i>
                        اولین درخواست را ثبت کنید
                    </a>
                </div>
                <?php else: ?>
                <table class="min-w-full">
                    <thead class="<?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?>">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">کد رهگیری</th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">عنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">مشتری</th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">دستگاه</th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">وضعیت</th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">تاریخ ایجاد</th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y <?php echo $isDark ? 'divide-gray-600' : 'divide-gray-200'; ?>">
                        <?php foreach ($requests as $request): ?>
                        <tr class="table-row hover:<?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?> transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-bold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                                    <?php echo en2fa($request['tracking_code']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                                    <?php echo htmlspecialchars($request['title']); ?>
                                </div>
                                <div class="text-xs <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                                    <?php echo htmlspecialchars(substr($request['problem_description'], 0, 50)); ?><?php echo strlen($request['problem_description']) > 50 ? '...' : ''; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                                    <?php echo htmlspecialchars($request['customer_name']); ?>
                                </div>
                                <div class="text-xs <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?> font-mono">
                                    <?php echo $request['customer_phone']; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                                    <?php echo htmlspecialchars($request['device_model']); ?>
                                </div>
                                <?php if (!empty($request['imei1'])): ?>
                                <div class="text-xs <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?> font-mono">
                                    IMEI: <?php echo en2fa($request['imei1']); ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php 
                                    switch($request['status']) {
                                        case 'تکمیل شده':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'لغو شده':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        default:
                                            echo 'bg-yellow-100 text-yellow-800';
                                    }
                                    ?>">
                                    <?php echo $request['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                                <?php echo en2fa(jalali_date('Y/m/d', strtotime($request['created_at']))); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="view-request?id=<?php echo $request['id']; ?>&theme=<?php echo $theme; ?>" 
                                       class="text-blue-600 hover:text-blue-900 text-sm" title="مشاهده جزئیات">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit-request?id=<?php echo $request['id']; ?>&theme=<?php echo $theme; ?>" 
                                       class="text-green-600 hover:text-green-900 text-sm" title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="print-receipt?id=<?php echo $request['id']; ?>" 
                                       class="text-purple-600 hover:text-purple-900 text-sm" title="چاپ رسید">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($requests)): ?>
            <!-- پاورقی جدول -->
            <div class="<?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?> px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                        نمایش <?php echo en2fa(count($requests)); ?> درخواست
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="print_all_requests.php" class="text-sm text-blue-600 hover:text-blue-800">
                            <i class="fas fa-print ml-1"></i>
                            چاپ همه
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="export_requests.php" class="text-sm text-green-600 hover:text-green-800">
                            <i class="fas fa-download ml-1"></i>
                            دانلود Excel
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
    </main>

    <!-- جاوا اسکریپت -->
    <script>
        // منوی موبایل
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
        
        // بستن منوی موبایل در صفحات بزرگ
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                document.getElementById('mobile-menu').classList.add('hidden');
            }
        });
    </script>
<?php include '../templates/footer.php'; ?>