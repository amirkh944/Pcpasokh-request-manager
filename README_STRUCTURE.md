# ساختار جدید پروژه سیستم مدیریت درخواست پاسخگو رایانه

## تغییرات اعمال شده

### 1. ساختار پوشه‌ها
```
/
├── index.php              # فایل اصلی مسیریابی
├── .htaccess             # تنظیمات URL rewriting
├── includes/             # فایل‌های شامل و توابع
│   ├── config.php        # تنظیمات پایگاه داده
│   ├── functions.php     # توابع اصلی
│   └── sms_config.php    # تنظیمات ارسال پیامک
├── templates/            # قالب‌های HTML
│   ├── header.php        # هدر و منوی ناوبری
│   └── footer.php        # فوتر
└── views/               # صفحات اصلی
    ├── dashboard.php     # داشبورد
    ├── login.php         # صفحه ورود
    ├── customers.php     # مدیریت مشتریان
    ├── requests.php      # مدیریت درخواست‌ها
    ├── track_request.php # پیگیری درخواست
    └── ...              # سایر صفحات
```

### 2. ویژگی‌های جدید

#### مسیریابی تمیز (Clean URLs)
- لینک‌ها بدون پسوند `.php` هستند
- مثال: `customers` به جای `customers.php`
- پشتیبانی از پارامترهای URL

#### قالب‌بندی مجزا
- هدر و منو در فایل `templates/header.php`
- فوتر در فایل `templates/footer.php`
- استفاده مجدد از کد در تمام صفحات

#### امنیت
- محافظت از پوشه‌های `includes/`، `templates/` و `views/`
- دسترسی مستقیم به این پوشه‌ها مسدود شده است

### 3. نحوه استفاده

#### دسترسی به صفحات
```
http://your-domain/dashboard
http://your-domain/customers
http://your-domain/requests
http://your-domain/new-request
http://your-domain/track-request
```

#### پارامترهای URL
```
http://your-domain/dashboard?theme=dark
http://your-domain/customers?theme=light
```

### 4. مزایای ساختار جدید

1. **سازماندهی بهتر**: فایل‌ها در پوشه‌های منطقی قرار گرفته‌اند
2. **کد تمیزتر**: عدم تکرار کد در فایل‌های مختلف
3. **نگهداری آسان‌تر**: تغییرات در هدر و منو فقط در یک فایل
4. **امنیت بیشتر**: محافظت از فایل‌های حساس
5. **URL های زیبا**: لینک‌های بدون پسوند فایل

### 5. فایل‌های کلیدی

#### `index.php`
- مسیریابی اصلی
- تعریف مسیرها و عنوان‌ها
- مدیریت لاگین

#### `templates/header.php`
- هدر HTML
- منوی ناوبری
- کنترل تم (روشن/تیره)
- استایل‌های CSS

#### `includes/config.php`
- تنظیمات پایگاه داده
- ایجاد جداول
- توابع تاریخ شمسی

#### `includes/functions.php`
- توابع اصلی سیستم
- مدیریت کاربران، مشتریان، درخواست‌ها
- توابع کمکی

### 6. نحوه افزودن صفحه جدید

1. فایل جدید را در پوشه `views/` ایجاد کنید
2. مسیر جدید را در `index.php` تعریف کنید
3. از قالب‌های `header.php` و `footer.php` استفاده کنید
4. لینک‌ها را بدون پسوند `.php` بنویسید

### 7. نکات مهم

- تمام فایل‌های view باید از `checkLogin()` استفاده کنند
- عنوان صفحه را در متغیر `$pageTitle` تنظیم کنید
- از `include '../templates/header.php'` و `include '../templates/footer.php'` استفاده کنید
- لینک‌ها را بدون پسوند `.php` بنویسید