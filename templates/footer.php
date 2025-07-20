    </main>

    <!-- فوتر -->
    <footer class="<?php echo $isDark ? 'bg-gray-800 text-gray-300' : 'bg-white text-gray-600'; ?> mt-auto py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> پاسخگو رایانه. تمامی حقوق محفوظ است.</p>
            </div>
        </div>
    </footer>

    <!-- اسکریپت‌های جاوا اسکریپت -->
    <script>
        // منوی موبایل
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // اسکریپت‌های اضافی صفحه
        <?php if (isset($customScripts)) echo $customScripts; ?>
    </script>

</body>
</html>