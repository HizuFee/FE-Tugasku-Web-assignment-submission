<?php if (session()->has('success') || session()->has('error')): ?>
    <div x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 4000)"
        class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg <?= session()->has('success') ? 'bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-100' : 'bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-100' ?> transition-all duration-300">
        <div class="flex items-center">
            <?php if (session()->has('success')): ?>
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <?= session()->get('success') ?>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <?= session()->get('error') ?>
            <?php endif; ?>

            <button @click="show = false" class="ml-4 text-sm font-medium">
                ✕
            </button>
        </div>
    </div>
<?php endif; ?>