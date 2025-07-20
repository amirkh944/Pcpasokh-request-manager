<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/router.php';
require_once __DIR__ . '/../includes/router.php';

checkLogin();

// تنظیم صفحه
setPageTitle('مدیریت مشتریان');

$customers = getAllCustomers();

// شامل کردن هدر
include __DIR__ . '/../templates/header.php';
?>

        <div class="<?php echo $isDark ? 'dark-card' : 'light-card'; ?> rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b <?php echo $isDark ? 'border-gray-600' : 'border-gray-200'; ?>">
                <h3 class="text-lg font-semibold <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                    <i class="fas fa-users ml-2 text-blue-500"></i>
                    مدیریت مشتریان
                </h3>
                <p class="mt-1 text-sm <?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                    لیست تمام مشتریان سیستم
                </p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y <?php echo $isDark ? 'divide-gray-600' : 'divide-gray-200'; ?>">
                    <thead class="<?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?>">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                شناسه
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                نام
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                تلفن
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                ایمیل
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                تاریخ ثبت
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?> uppercase tracking-wider">
                                عملیات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="<?php echo $isDark ? 'bg-gray-800' : 'bg-white'; ?> divide-y <?php echo $isDark ? 'divide-gray-600' : 'divide-gray-200'; ?>">
                        <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="text-center">
                                    <i class="fas fa-users text-4xl <?php echo $isDark ? 'text-gray-400' : 'text-gray-300'; ?> mb-4"></i>
                                    <p class="<?php echo $isDark ? 'dark-text-secondary' : 'light-text-secondary'; ?>">
                                        هنوز مشتری‌ای ثبت نشده است
                                    </p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                            <tr class="<?php echo $isDark ? 'hover:bg-gray-700' : 'hover:bg-gray-50'; ?> transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?>">
                                    <?php echo en2fa($customer['id']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium <?php echo $isDark ? 'dark-text' : 'light-text'; ?>">
                                        <?php echo htmlspecialchars($customer['name']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?>">
                                        <?php echo htmlspecialchars($customer['phone']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?>">
                                        <?php echo htmlspecialchars($customer['email'] ?? '-'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">
                                    <?php echo jalali_date('Y/m/d', strtotime($customer['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo getPageUrl('view_customer'); ?>?id=<?php echo $customer['id']; ?>&theme=<?php echo $theme; ?>" 
                                       class="text-blue-500 hover:text-blue-700 ml-3">
                                        <i class="fas fa-eye"></i> مشاهده
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($customers)): ?>
            <div class="px-6 py-4 <?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?> border-t <?php echo $isDark ? 'border-gray-600' : 'border-gray-200'; ?>">
                <div class="flex items-center justify-between">
                    <div class="text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-700'; ?>">
                        نمایش <?php echo en2fa(count($customers)); ?> مشتری
                    </div>
                    <div class="flex space-x-2 space-x-reverse">
                        <a href="<?php echo getPageUrl('new_request'); ?>?theme=<?php echo $theme; ?>" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-plus ml-1"></i>درخواست جدید
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

<?php
// شامل کردن فوتر
include __DIR__ . '/../templates/footer.php';
?>