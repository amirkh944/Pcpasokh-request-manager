-- ایجاد پایگاه داده
CREATE DATABASE IF NOT EXISTS `pasokh_rayane` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci;
USE `pasokh_rayane`;

-- جدول کاربران
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL COMMENT 'نام کاربری',
    `password` VARCHAR(255) NOT NULL COMMENT 'رمز عبور (هش شده)',
    `email` VARCHAR(100) DEFAULT NULL COMMENT 'آدرس ایمیل',
    `phone` VARCHAR(20) DEFAULT NULL COMMENT 'تلفن همراه',
    `user_id_code` VARCHAR(3) UNIQUE NOT NULL COMMENT 'شناسه سه رقمی کاربر',
    `is_admin` BOOLEAN DEFAULT FALSE COMMENT 'آیا کاربر مدیر است',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ ایجاد'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci COMMENT='جدول کاربران سیستم';

-- جدول مشتریان
CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COMMENT 'نام و نام خانوادگی',
    `phone` VARCHAR(20) NOT NULL COMMENT 'تلفن همراه',
    `email` VARCHAR(100) DEFAULT NULL COMMENT 'آدرس ایمیل',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ ثبت'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci COMMENT='جدول مشتریان';

-- جدول درخواست‌ها
CREATE TABLE IF NOT EXISTS `requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL COMMENT 'شناسه مشتری',
    `title` VARCHAR(200) NOT NULL COMMENT 'عنوان درخواست',
    `device_model` VARCHAR(100) DEFAULT NULL COMMENT 'مدل دستگاه',
    `imei1` VARCHAR(20) DEFAULT NULL COMMENT 'شناسه IMEI اول',
    `imei2` VARCHAR(20) DEFAULT NULL COMMENT 'شناسه IMEI دوم',
    `problem_description` TEXT DEFAULT NULL COMMENT 'شرح مشکل',
    `registration_date` VARCHAR(20) DEFAULT NULL COMMENT 'تاریخ ثبت شمسی',
    `estimated_duration` VARCHAR(50) DEFAULT NULL COMMENT 'مدت زمان احتمالی انجام',
    `actions_required` TEXT DEFAULT NULL COMMENT 'اقدامات قابل انجام',
    `cost` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'هزینه درخواست',
    `tracking_code` VARCHAR(7) UNIQUE NOT NULL COMMENT 'کد رهگیری 7 رقمی',
    `status` ENUM('در حال پردازش', 'تکمیل شده', 'لغو شده') DEFAULT 'در حال پردازش' COMMENT 'وضعیت درخواست',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ ایجاد',
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci COMMENT='جدول درخواست‌ها';

-- جدول پرداخت‌ها
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL COMMENT 'شناسه مشتری',
    `request_id` INT DEFAULT NULL COMMENT 'شناسه درخواست مربوطه',
    `amount` DECIMAL(10,2) NOT NULL COMMENT 'مبلغ',
    `payment_type` ENUM('واریز', 'بدهکاری') NOT NULL COMMENT 'نوع پرداخت',
    `description` TEXT DEFAULT NULL COMMENT 'توضیحات',
    `receipt_image` VARCHAR(255) DEFAULT NULL COMMENT 'تصویر رسید',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ ثبت',
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`request_id`) REFERENCES `requests`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci COMMENT='جدول پرداخت‌ها و بدهکاری‌ها';

-- جدول تماس‌ها
CREATE TABLE IF NOT EXISTS `contacts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL COMMENT 'شناسه مشتری',
    `contact_type` ENUM('تماس', 'ایمیل', 'پیامک') NOT NULL COMMENT 'نوع تماس',
    `subject` VARCHAR(200) DEFAULT NULL COMMENT 'موضوع',
    `message` TEXT DEFAULT NULL COMMENT 'متن پیام',
    `contact_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ تماس',
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci COMMENT='جدول تماس‌ها';

-- ایجاد ایندکس‌ها برای بهبود عملکرد
CREATE INDEX `idx_customers_phone` ON `customers`(`phone`);
CREATE INDEX `idx_requests_customer_id` ON `requests`(`customer_id`);
CREATE INDEX `idx_requests_tracking_code` ON `requests`(`tracking_code`);
CREATE INDEX `idx_requests_status` ON `requests`(`status`);
CREATE INDEX `idx_requests_created_at` ON `requests`(`created_at`);
CREATE INDEX `idx_payments_customer_id` ON `payments`(`customer_id`);
CREATE INDEX `idx_payments_request_id` ON `payments`(`request_id`);
CREATE INDEX `idx_payments_type` ON `payments`(`payment_type`);
CREATE INDEX `idx_payments_created_at` ON `payments`(`created_at`);
CREATE INDEX `idx_contacts_customer_id` ON `contacts`(`customer_id`);
CREATE INDEX `idx_contacts_type` ON `contacts`(`contact_type`);
CREATE INDEX `idx_contacts_date` ON `contacts`(`contact_date`);

-- درج کاربر مدیر پیش‌فرض
INSERT INTO `users` (`username`, `password`, `email`, `phone`, `user_id_code`, `is_admin`) 
VALUES ('amirkh94', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@pasokh.com', '09123456789', '001', TRUE)
ON DUPLICATE KEY UPDATE `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- نوت: رمز عبور هش شده برای 'Amir137530' است

-- درج داده‌های نمونه برای تست (اختیاری)
INSERT INTO `customers` (`name`, `phone`, `email`) VALUES
('علی احمدی', '09121234567', 'ali@example.com'),
('فاطمه رضایی', '09359876543', 'fateme@example.com'),
('محمد حسینی', '09177654321', NULL);

-- درج درخواست‌های نمونه
INSERT INTO `requests` (`customer_id`, `title`, `device_model`, `imei1`, `problem_description`, `registration_date`, `estimated_duration`, `actions_required`, `cost`, `tracking_code`, `status`) VALUES
(1, 'تعمیر صفحه نمایش', 'Samsung Galaxy A52', '123456789012345', 'صفحه نمایش شکسته و لمس کار نمی‌کند', '1403/01/15', '2-3 روز کاری', 'تعویض صفحه نمایش و تست عملکرد', 1500000.00, '1234567', 'در حال پردازش'),
(2, 'تعویض باتری', 'iPhone 12', '987654321098765', 'باتری خیلی سریع تمام می‌شود', '1403/01/16', '1 روز کاری', 'تعویض باتری اصلی', 800000.00, '2345678', 'تکمیل شده'),
(3, 'بازیابی اطلاعات', 'Xiaomi Redmi Note 10', '456789123456789', 'گوشی روشن نمی‌شود و اطلاعات مهم دارد', '1403/01/17', '3-5 روز کاری', 'تعمیر برد و بازیابی اطلاعات', 2000000.00, '3456789', 'در حال پردازش');

-- درج پرداخت‌های نمونه
INSERT INTO `payments` (`customer_id`, `request_id`, `amount`, `payment_type`, `description`) VALUES
(1, 1, 1500000.00, 'بدهکاری', 'بدهکاری درخواست: تعمیر صفحه نمایش'),
(2, 2, 800000.00, 'بدهکاری', 'بدهکاری درخواست: تعویض باتری'),
(2, 2, 800000.00, 'واریز', 'پرداخت کامل هزینه تعویض باتری'),
(3, 3, 2000000.00, 'بدهکاری', 'بدهکاری درخواست: بازیابی اطلاعات'),
(3, 3, 1000000.00, 'واریز', 'پرداخت بخشی از هزینه');

-- درج تماس‌های نمونه
INSERT INTO `contacts` (`customer_id`, `contact_type`, `subject`, `message`) VALUES
(1, 'تماس', 'پیگیری وضعیت درخواست', 'تماس برای پیگیری وضعیت تعمیر صفحه نمایش'),
(2, 'پیامک', 'اطلاع رسانی تکمیل کار', 'پیامک اطلاع رسانی تکمیل تعویض باتری'),
(3, 'ایمیل', 'درخواست آپدیت', 'ایمیل درخواست آپدیت در مورد وضعیت بازیابی اطلاعات');

-- تنظیمات نهایی
SET FOREIGN_KEY_CHECKS = 1;

-- نمایش اطلاعات جداول ایجاد شده
SELECT 'جداول با موفقیت ایجاد شدند!' as message;
SELECT 'تعداد کاربران:' as info, COUNT(*) as count FROM users;
SELECT 'تعداد مشتریان:' as info, COUNT(*) as count FROM customers;
SELECT 'تعداد درخواست‌ها:' as info, COUNT(*) as count FROM requests;
SELECT 'تعداد پرداخت‌ها:' as info, COUNT(*) as count FROM payments;
SELECT 'تعداد تماس‌ها:' as info, COUNT(*) as count FROM contacts;