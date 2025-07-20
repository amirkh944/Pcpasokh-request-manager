# تنظیمات جدید ساختار پروژه

## ساختار جدید دایرکتوری‌ها

```
/
├── index.php                 # فایل اصلی مسیریابی
├── includes/                 # فایل‌های کمکی و تنظیمات
│   ├── config.php           # تنظیمات پایگاه داده
│   ├── functions.php        # توابع اصلی سیستم
│   ├── router.php           # توابع مسیریابی
│   └── sms_config.php       # تنظیمات پیامک
├── templates/               # قالب‌های مشترک
│   ├── header.php          # هدر و منو
│   └── footer.php          # فوتر
└── views/                   # صفحات نمایش
    ├── dashboard.php        # داشبورد
    ├── customers.php        # مشتریان
    ├── new_request.php      # درخواست جدید
    ├── requests.php         # لیست درخواست‌ها
    ├── payments.php         # مدیریت مالی
    ├── users.php           # مدیریت کاربران
    └── ...                 # سایر صفحات
```

## تغییرات عمده

### 1. مسیریابی جدید
- همه صفحات از طریق `index.php` قابل دسترسی هستند
- URL جدید: `index.php?page=dashboard` به جای `dashboard.php`
- امکان استفاده از نام‌های صفحات بدون پسوند

### 2. سیستم قالب
- هدر و منو در `templates/header.php`
- فوتر در `templates/footer.php`
- پشتیبانی از تم تیره/روشن
- منو داینامیک با هایلایت صفحه فعال

### 3. توابع جدید مسیریابی

#### `getPageUrl($page)`
تولید URL برای صفحات:
```php
echo getPageUrl('dashboard'); // خروجی: index.php?page=dashboard
```

#### `redirect($page, $params = [])`
ریدایرکت به صفحه:
```php
redirect('dashboard');
redirect('view_request', ['id' => 123]);
```

#### `setPageTitle($title)`
تنظیم عنوان صفحه:
```php
setPageTitle('داشبورد');
```

#### `enableCharts()`
فعال‌سازی Chart.js:
```php
enableCharts();
```

#### `addCustomStyles($styles)` و `addCustomScripts($scripts)`
اضافه کردن CSS و JavaScript سفارشی:
```php
addCustomStyles('.my-class { color: red; }');
addCustomScripts('console.log("Hello");');
```

### 4. استفاده در صفحات

نمونه ساختار صفحه جدید:
```php
<?php
// تنظیمات صفحه
setPageTitle('عنوان صفحه');
enableCharts(); // در صورت نیاز

// منطق صفحه
$data = getData();

// شامل کردن هدر
include __DIR__ . '/../templates/header.php';
?>

<div class="content">
    <!-- محتوای صفحه -->
</div>

<?php
include __DIR__ . '/../templates/footer.php';
?>
```

## مزایای ساختار جدید

1. **سازماندهی بهتر**: فایل‌ها در دسته‌بندی منطقی قرار گرفته‌اند
2. **قابلیت نگهداری**: تغییرات هدر/منو در یک فایل اعمال می‌شود
3. **امنیت بیشتر**: مسیریابی متمرکز برای کنترل دسترسی
4. **انعطاف‌پذیری**: قابلیت افزودن ویژگی‌های جدید به قالب‌ها
5. **URL تمیز**: امکان ایجاد URL های دوستدار موتور جستجو

## نحوه استفاده

برای دسترسی به صفحات از طریق URL:
- `index.php?page=dashboard` - داشبورد
- `index.php?page=customers` - مشتریان  
- `index.php?page=new_request` - درخواست جدید
- `index.php?page=requests` - لیست درخواست‌ها

همچنین می‌توان پارامترهای اضافی نیز ارسال کرد:
- `index.php?page=view_request&id=123&theme=dark`