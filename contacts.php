<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

checkLogin();

$message = '';
$contacts = getAllContacts();
$customers = getAllCustomers();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerId = $_POST['customer_id'];
    $contactType = $_POST['contact_type'];
    $subject = $_POST['subject'];
    $contactMessage = $_POST['message'];
    
    try {
        addContact($customerId, $contactType, $subject, $contactMessage);
        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                      تماس با موفقیت ثبت شد.
                    </div>';
        
        // بارگذاری مجدد لیست تماس‌ها
        $contacts = getAllContacts();
        
        // پاک کردن فرم
        $_POST = array();
        
    } catch (Exception $e) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                      خطا در ثبت تماس: ' . $e->getMessage() . '
                    </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت تماس‌ها - مدیریت درخواست پاسخگو رایانه</title>
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
        <!-- فرم ثبت تماس جدید -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">ثبت تماس جدید</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">فرم ثبت تماس با مشتری</p>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <?php echo $message; ?>
                
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">مشتری *</label>
                            <select name="customer_id" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">انتخاب مشتری</option>
                                <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>" <?php echo ($_POST['customer_id'] ?? '') == $customer['id'] ? 'selected' : ''; ?>>
                                    <?php echo $customer['name']; ?> - <?php echo $customer['phone']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع تماس *</label>
                            <select name="contact_type" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">انتخاب نوع تماس</option>
                                <option value="تماس" <?php echo ($_POST['contact_type'] ?? '') == 'تماس' ? 'selected' : ''; ?>>تماس</option>
                                <option value="ایمیل" <?php echo ($_POST['contact_type'] ?? '') == 'ایمیل' ? 'selected' : ''; ?>>ایمیل</option>
                                <option value="پیامک" <?php echo ($_POST['contact_type'] ?? '') == 'پیامک' ? 'selected' : ''; ?>>پیامک</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">موضوع</label>
                            <input type="text" name="subject" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2"
                                   value="<?php echo $_POST['subject'] ?? ''; ?>"
                                   placeholder="موضوع تماس">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">پیام *</label>
                            <textarea name="message" rows="4" required 
                                      class="w-full border border-gray-300 rounded-md px-3 py-2"
                                      placeholder="متن پیام یا توضیحات تماس"><?php echo $_POST['message'] ?? ''; ?></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">ثبت تماس</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- لیست تماس‌ها -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">مدیریت تماس‌ها</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">لیست تمام تماس‌های ثبت شده</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مشتری</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع تماس</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">موضوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">پیام</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo en2fa(jalali_date('Y/m/d H:i', strtotime($contact['contact_date']))); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $contact['customer_name']; ?><br>
                                <span class="text-gray-500"><?php echo $contact['customer_phone']; ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php 
                                    switch($contact['contact_type']) {
                                        case 'تماس': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'ایمیل': echo 'bg-green-100 text-green-800'; break;
                                        case 'پیامک': echo 'bg-purple-100 text-purple-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo $contact['contact_type']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $contact['subject'] ?: '-'; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php echo nl2br(substr($contact['message'], 0, 100)); ?>
                                <?php if (strlen($contact['message']) > 100): ?>
                                    <span class="text-gray-500">...</span>
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