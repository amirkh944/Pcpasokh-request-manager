<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
require_once 'sms_config.php';

checkLogin();

$requestId = $_GET['request_id'] ?? 0;
$message = '';

// دریافت اطلاعات درخواست
if ($requestId) {
    $request = getRequest($requestId);
    if (!$request) {
        header('Location: dashboard.php');
        exit;
    }
    $customer = getCustomer($request['customer_id']);
} else {
    header('Location: dashboard.php');
    exit;
}

// پردازش ارسال پیامک
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $smsMessage = $_POST['sms_message'] ?? '';
    $usePattern = $_POST['use_pattern'] ?? 'no';
    
    if (!empty($smsMessage)) {
        try {
            if ($usePattern === 'yes' && SMS_PATTERN_NEW_REQUEST) {
                // ارسال با الگو
                $smsResult = sendNewRequestSMS($customer['phone'], $request['tracking_code'], $request['title']);
            } else {
                // ارسال متن دلخواه
                $smsResult = sendSMSUpdated($customer['phone'], $smsMessage);
            }
            
            if ($smsResult['success']) {
                $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                              <i class="fas fa-check-circle ml-2"></i>
                              پیامک با موفقیت ارسال شد به شماره ' . $customer['phone'] . '
                            </div>';
            } else {
                $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                              <i class="fas fa-exclamation-circle ml-2"></i>
                              خطا در ارسال پیامک: ' . $smsResult['message'] . '
                            </div>';
            }
        } catch (Exception $e) {
            $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                          <i class="fas fa-exclamation-triangle ml-2"></i>
                          خطای سیستمی: ' . $e->getMessage() . '
                        </div>';
        }
    } else {
        $message = '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                      <i class="fas fa-exclamation-triangle ml-2"></i>
                      لطفاً متن پیامک را وارد کنید
                    </div>';
    }
}

// کنترل تم (قالب)
$theme = $_GET['theme'] ?? 'light';
$isDark = $theme === 'dark';

// متن‌های پیش‌فرض
$defaultMessages = [
    'new_request' => "درخواست شما با کد رهگیری {$request['tracking_code']} ثبت شد. عنوان: {$request['title']} - پاسخگو رایانه",
    'status_update' => "وضعیت درخواست {$request['tracking_code']} به {$request['status']} تغییر یافت. پاسخگو رایانه",
    'reminder' => "یادآوری: درخواست شما با کد {$request['tracking_code']} در دست بررسی است. پاسخگو رایانه",
    'completion' => "درخواست شما با کد {$request['tracking_code']} تکمیل شد. لطفاً جهت تحویل مراجعه فرمایید. پاسخگو رایانه"
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ارسال پیامک - مدیریت درخواست پاسخگو رایانه</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .dark-input {
            background: #374151;
            border: 1px solid #4b5563;
            color: #e5e7eb;
        }
        .dark-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        <?php endif; ?>
        .message-template {
            cursor: pointer;
            transition: all 0.2s;
        }
        .message-template:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="<?php echo $isDark ? 'dark-theme min-h-screen' : 'bg-gray-100'; ?>">
    <!-- Navigation -->
    <nav class="<?php echo $isDark ? 'bg-gray-800 shadow-lg' : 'bg-white shadow-lg'; ?>">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="dashboard.php?theme=<?php echo $theme; ?>" class="text-xl font-bold <?php echo $isDark ? 'text-white' : 'text-gray-800'; ?>">
                        مدیریت درخواست پاسخگو رایانه
                    </a>
                </div>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <!-- Navigation Menu -->
                    <div class="hidden md:flex space-x-2 space-x-reverse">
                        <a href="dashboard.php?theme=<?php echo $theme; ?>" class="px-3 py-2 rounded <?php echo $isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <i class="fas fa-home ml-1"></i>داشبورد
                        </a>
                        <a href="requests.php?theme=<?php echo $theme; ?>" class="px-3 py-2 rounded <?php echo $isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <i class="fas fa-list ml-1"></i>درخواست‌ها
                        </a>
                        <a href="view_request.php?id=<?php echo $requestId; ?>&theme=<?php echo $theme; ?>" class="px-3 py-2 rounded <?php echo $isDark ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <i class="fas fa-eye ml-1"></i>مشاهده درخواست
                        </a>
                    </div>
                    
                    <!-- Theme Toggle -->
                    <div class="flex space-x-2">
                        <a href="?request_id=<?php echo $requestId; ?>&theme=light" class="px-3 py-1 rounded <?php echo !$isDark ? 'bg-blue-500 text-white' : 'bg-gray-600 text-gray-300'; ?>" title="حالت روشن">
                            <i class="fas fa-sun"></i>
                        </a>
                        <a href="?request_id=<?php echo $requestId; ?>&theme=dark" class="px-3 py-1 rounded <?php echo $isDark ? 'bg-blue-500 text-white' : 'bg-gray-600 text-gray-300'; ?>" title="حالت تیره">
                            <i class="fas fa-moon"></i>
                        </a>
                    </div>
                    
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">خروج</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Request Info -->
        <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>">
                    <i class="fas fa-sms ml-2 text-green-500"></i>
                    ارسال پیامک
                </h3>
                <p class="mt-1 max-w-2xl text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">
                    ارسال پیامک اطلاع‌رسانی برای درخواست
                </p>
            </div>
            
            <div class="border-t <?php echo $isDark ? 'border-gray-600' : 'border-gray-200'; ?>">
                <dl>
                    <div class="<?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?> px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium <?php echo $isDark ? 'text-gray-200' : 'text-gray-500'; ?>">کد رهگیری</dt>
                        <dd class="mt-1 text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?> sm:mt-0 sm:col-span-2 font-mono">
                            <?php echo en2fa($request['tracking_code']); ?>
                        </dd>
                    </div>
                    <div class="<?php echo $isDark ? 'bg-gray-800' : 'bg-white'; ?> px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium <?php echo $isDark ? 'text-gray-200' : 'text-gray-500'; ?>">عنوان درخواست</dt>
                        <dd class="mt-1 text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?> sm:mt-0 sm:col-span-2">
                            <?php echo htmlspecialchars($request['title']); ?>
                        </dd>
                    </div>
                    <div class="<?php echo $isDark ? 'bg-gray-700' : 'bg-gray-50'; ?> px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium <?php echo $isDark ? 'text-gray-200' : 'text-gray-500'; ?>">مشتری</dt>
                        <dd class="mt-1 text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?> sm:mt-0 sm:col-span-2">
                            <div class="font-medium"><?php echo htmlspecialchars($customer['name']); ?></div>
                            <div class="text-gray-500 font-mono"><?php echo $customer['phone']; ?></div>
                        </dd>
                    </div>
                    <div class="<?php echo $isDark ? 'bg-gray-800' : 'bg-white'; ?> px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium <?php echo $isDark ? 'text-gray-200' : 'text-gray-500'; ?>">وضعیت</dt>
                        <dd class="mt-1 text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-900'; ?> sm:mt-0 sm:col-span-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?php 
                                switch($request['status']) {
                                    case 'تکمیل شده': echo 'bg-green-100 text-green-800'; break;
                                    case 'لغو شده': echo 'bg-red-100 text-red-800'; break;
                                    default: echo 'bg-yellow-100 text-yellow-800';
                                }
                                ?>">
                                <?php echo $request['status']; ?>
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <?php echo $message; ?>

        <!-- SMS Service Status -->
        <?php if (!isSMSEnabled()): ?>
        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-orange-400"></i>
                </div>
                <div class="mr-3">
                    <p class="text-sm">
                        <strong>توجه:</strong> سرویس پیامک غیرفعال است یا تنظیمات ناقص است.
                        <br>برای فعال‌سازی، فایل <code>sms_config.php</code> را بررسی کنید.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Message Templates -->
        <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>">
                    <i class="fas fa-template ml-2 text-blue-500"></i>
                    قالب‌های آماده
                </h3>
                <p class="mt-1 max-w-2xl text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">
                    روی هر قالب کلیک کنید تا متن آن در فرم پایین قرار گیرد
                </p>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($defaultMessages as $key => $msg): ?>
                    <div class="message-template <?php echo $isDark ? 'bg-gray-700 border-gray-600' : 'bg-gray-50 border-gray-200'; ?> border rounded-lg p-4" 
                         onclick="setMessage('<?php echo addslashes($msg); ?>')">
                        <div class="flex items-center justify-between">
                            <h4 class="font-medium <?php echo $isDark ? 'text-gray-200' : 'text-gray-700'; ?>">
                                <?php 
                                $titles = [
                                    'new_request' => 'اطلاع ثبت درخواست',
                                    'status_update' => 'به‌روزرسانی وضعیت',
                                    'reminder' => 'یادآوری',
                                    'completion' => 'اطلاع تکمیل'
                                ];
                                echo $titles[$key];
                                ?>
                            </h4>
                            <i class="fas fa-hand-pointer text-blue-500"></i>
                        </div>
                        <p class="mt-2 text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-600'; ?> leading-relaxed">
                            <?php echo htmlspecialchars(substr($msg, 0, 80)) . (strlen($msg) > 80 ? '...' : ''); ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- SMS Form -->
        <div class="<?php echo $isDark ? 'dark-card' : 'bg-white'; ?> shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium <?php echo $isDark ? 'text-white' : 'text-gray-900'; ?>">
                    <i class="fas fa-edit ml-2 text-purple-500"></i>
                    فرم ارسال پیامک
                </h3>
                <p class="mt-1 max-w-2xl text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-500'; ?>">
                    متن پیامک خود را تایپ کنید یا از قالب‌های بالا استفاده کنید
                </p>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="sms_message" class="block text-sm font-medium <?php echo $isDark ? 'text-gray-200' : 'text-gray-700'; ?> mb-2">
                            متن پیامک *
                        </label>
                        <textarea name="sms_message" id="sms_message" rows="4" required
                                  class="w-full <?php echo $isDark ? 'dark-input' : 'border border-gray-300'; ?> rounded-md px-3 py-2"
                                  placeholder="متن پیامک خود را اینجا بنویسید..."
                                  maxlength="160"><?php echo $_POST['sms_message'] ?? $defaultMessages['new_request']; ?></textarea>
                        <div class="mt-1 flex justify-between">
                            <span class="text-xs <?php echo $isDark ? 'text-gray-400' : 'text-gray-500'; ?>">
                                حداکثر ۱۶۰ کاراکتر
                            </span>
                            <span id="charCount" class="text-xs <?php echo $isDark ? 'text-gray-400' : 'text-gray-500'; ?>">
                                0/160
                            </span>
                        </div>
                    </div>

                    <?php if (SMS_PATTERN_NEW_REQUEST): ?>
                    <div class="flex items-center">
                        <input type="checkbox" name="use_pattern" value="yes" id="use_pattern"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="use_pattern" class="mr-2 block text-sm <?php echo $isDark ? 'text-gray-200' : 'text-gray-700'; ?>">
                            استفاده از الگوی از پیش تعریف شده (اگر متن بالا را تغییر ندهید)
                        </label>
                    </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-center">
                        <div class="text-sm <?php echo $isDark ? 'text-gray-300' : 'text-gray-600'; ?>">
                            <i class="fas fa-mobile-alt ml-1"></i>
                            ارسال به: <strong><?php echo $customer['phone']; ?></strong>
                        </div>
                        <div class="flex gap-3">
                            <a href="view_request.php?id=<?php echo $requestId; ?>&theme=<?php echo $theme; ?>" 
                               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition duration-200">
                                <i class="fas fa-arrow-right ml-2"></i>
                                بازگشت
                            </a>
                            <button type="submit" 
                                    class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition duration-200"
                                    <?php echo !isSMSEnabled() ? 'disabled title="سرویس پیامک غیرفعال است"' : ''; ?>>
                                <i class="fas fa-paper-plane ml-2"></i>
                                ارسال پیامک
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Character counter
        const messageTextarea = document.getElementById('sms_message');
        const charCount = document.getElementById('charCount');
        
        function updateCharCount() {
            const length = messageTextarea.value.length;
            charCount.textContent = length + '/160';
            
            if (length > 160) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('<?php echo $isDark ? "text-gray-400" : "text-gray-500"; ?>');
            } else {
                charCount.classList.remove('text-red-500');
                charCount.classList.add('<?php echo $isDark ? "text-gray-400" : "text-gray-500"; ?>');
            }
        }
        
        messageTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count
        
        // Set message from template
        function setMessage(message) {
            messageTextarea.value = message;
            updateCharCount();
            messageTextarea.focus();
        }
    </script>
</body>
</html>