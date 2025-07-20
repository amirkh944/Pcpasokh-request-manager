<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
require_once 'sms_config.php';

checkLogin();

$message = '';
$customers = getAllCustomers();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customerName = $_POST['customer_name'];
    $customerPhone = $_POST['customer_phone'];
    $customerEmail = $_POST['customer_email'] ?? '';
    $existingCustomerId = $_POST['existing_customer'] ?? '';
    
    $title = $_POST['title'];
    $deviceModel = $_POST['device_model'];
    $imei1 = $_POST['imei1'];
    $imei2 = $_POST['imei2'];
    $problemDescription = $_POST['problem_description'];
    $estimatedDuration = $_POST['estimated_duration'];
    $actionsRequired = $_POST['actions_required'];
    $cost = floatval($_POST['cost']);
    
    try {
        // تعیین مشتری
        if ($existingCustomerId) {
            $customerId = $existingCustomerId;
        } else {
            // بررسی وجود مشتری با همین شماره تلفن
            $existingCustomer = getCustomerByPhone($customerPhone);
            if ($existingCustomer) {
                $customerId = $existingCustomer['id'];
            } else {
                $customerId = createCustomer($customerName, $customerPhone, $customerEmail);
            }
        }
        
        // ایجاد درخواست
        $requestId = createRequest($customerId, $title, $deviceModel, $imei1, $imei2, $problemDescription, $estimatedDuration, $actionsRequired, $cost);
        
        // دریافت کد رهگیری
        $request = getRequest($requestId);
        $trackingCode = $request['tracking_code'];
        
        // ارسال پیامک اطلاع‌رسانی
        $smsResult = sendNewRequestSMS($customerPhone, $trackingCode, $title);
        
        $smsStatus = $smsResult['success'] ? 
            '<div class="bg-blue-100 border border-blue-400 text-blue-700 px-3 py-2 rounded text-sm mt-2">
                پیامک اطلاع‌رسانی ارسال شد
            </div>' : 
            '<div class="bg-orange-100 border border-orange-400 text-orange-700 px-3 py-2 rounded text-sm mt-2">
                خطا در ارسال پیامک: ' . $smsResult['message'] . '
            </div>';
        
        $message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                      درخواست با موفقیت ثبت شد. کد رهگیری: <strong>' . en2fa($trackingCode) . '</strong>
                      <br><a href="print_receipt.php?id=' . $requestId . '" class="underline">چاپ رسید</a>
                      ' . $smsStatus . '
                    </div>';
        
        // پاک کردن فرم
        $_POST = array();
        
    } catch (Exception $e) {
        $message = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                      خطا در ثبت درخواست: ' . $e->getMessage() . '
                    </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>درخواست جدید - مدیریت درخواست پاسخگو رایانه</title>
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

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">ثبت درخواست جدید</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">لطفاً اطلاعات درخواست را کامل وارد کنید</p>
            </div>
            
            <div class="px-4 py-5 sm:p-6">
                <?php echo $message; ?>
                
                <form method="POST" class="space-y-6">
                    <!-- اطلاعات مشتری -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-medium text-gray-900 mb-4">اطلاعات مشتری</h4>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">انتخاب از مشتریان قبلی</label>
                            <select name="existing_customer" id="existing_customer" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">مشتری جدید</option>
                                <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>" 
                                        data-name="<?php echo $customer['name']; ?>"
                                        data-phone="<?php echo $customer['phone']; ?>"
                                        data-email="<?php echo $customer['email']; ?>">
                                    <?php echo $customer['name']; ?> - <?php echo $customer['phone']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">نام و نام خانوادگی *</label>
                                <input type="text" name="customer_name" id="customer_name" required 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['customer_name'] ?? ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">شماره تماس *</label>
                                <input type="text" name="customer_phone" id="customer_phone" required 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['customer_phone'] ?? ''; ?>">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">آدرس ایمیل</label>
                                <input type="email" name="customer_email" id="customer_email" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['customer_email'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- اطلاعات درخواست -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-medium text-gray-900 mb-4">اطلاعات درخواست</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان درخواست *</label>
                                <input type="text" name="title" required 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['title'] ?? ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">مدل دستگاه</label>
                                <input type="text" name="device_model" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['device_model'] ?? ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">IMEI اول</label>
                                <input type="text" name="imei1" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['imei1'] ?? ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">IMEI دوم</label>
                                <input type="text" name="imei2" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['imei2'] ?? ''; ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">مدت زمان احتمالی</label>
                                <input type="text" name="estimated_duration" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['estimated_duration'] ?? ''; ?>">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">شرح مشکل</label>
                                <textarea name="problem_description" rows="3" 
                                          class="w-full border border-gray-300 rounded-md px-3 py-2"><?php echo $_POST['problem_description'] ?? ''; ?></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">اقدامات قابل انجام</label>
                                <textarea name="actions_required" rows="3" 
                                          class="w-full border border-gray-300 rounded-md px-3 py-2"><?php echo $_POST['actions_required'] ?? ''; ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">هزینه درخواست (تومان)</label>
                                <input type="number" name="cost" step="0.01" 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2"
                                       value="<?php echo $_POST['cost'] ?? '0'; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 space-x-reverse">
                        <a href="dashboard.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">انصراف</a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">ثبت درخواست</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // پر کردن خودکار فیلدها هنگام انتخاب مشتری قبلی
        document.getElementById('existing_customer').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                document.getElementById('customer_name').value = selectedOption.dataset.name;
                document.getElementById('customer_phone').value = selectedOption.dataset.phone;
                document.getElementById('customer_email').value = selectedOption.dataset.email;
                
                // غیرفعال کردن فیلدها
                document.getElementById('customer_name').readOnly = true;
                document.getElementById('customer_phone').readOnly = true;
                document.getElementById('customer_email').readOnly = true;
            } else {
                // پاک کردن فیلدها
                document.getElementById('customer_name').value = '';
                document.getElementById('customer_phone').value = '';
                document.getElementById('customer_email').value = '';
                
                // فعال کردن فیلدها
                document.getElementById('customer_name').readOnly = false;
                document.getElementById('customer_phone').readOnly = false;
                document.getElementById('customer_email').readOnly = false;
            }
        });
    </script>
</body>
</html>