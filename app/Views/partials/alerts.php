<?php if (session()->has('success')): ?>
    <div x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="mt-4 px-4 py-2 bg-green-100 text-green-700 rounded-lg">
        <?= session()->get('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded-lg">
        <?= session()->get('error') ?>
    </div>
<?php endif; ?>