<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
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

    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        My Dashboard
    </h2>

    <div class="mb-6 flex gap-4">
        <?php if (isset($role) && $role === 'teacher'): ?>
            <a href="<?= site_url('class/create') ?>"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                Create New Class
            </a>
            <a href="<?= site_url('class/join') ?>"
                class="px-4 py-2 bg-green-700 text-white rounded ml-4"
                style="background-color: rgb(21 118 61); transition: background-color 0.2s;">
                Contribute to Class
            </a>
        <?php elseif (isset($role) && $role === 'student'): ?>
            <a href="<?= site_url('class/join') ?>"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                Join Class
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($role) && $role === 'teacher'): ?>
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">
            <?php if (isset($classes) && count($classes) > 0): ?>
                <?php foreach ($classes as $class): ?>
                    <div class="flex flex-col p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                        <div class="w-full">
                            <div class="flex justify-between items-center">
                                <h4 class="text-xl font-semibold text-gray-700 dark:text-gray-200">
                                    <?= esc($class['name']) ?>
                                </h4>
                                <?php if (isset($class['role']) && $class['role'] === 'owner'): ?>
                                    <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">
                                        Owner
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                        Contributor
                                    </span>
                                <?php endif; ?>
                            </div>

                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                <?= esc($class['description']) ?>
                            </p>

                            <?php if (isset($class['role']) && $class['role'] === 'owner' && isset($class['code'])): ?>
                                <div class="mt-4 text-sm text-gray-500">
                                    Class Code: <span class="font-bold"><?= esc($class['code']) ?></span>
                                </div>
                            <?php endif; ?>

                            <a href="<?= site_url('class/details/' . $class['id']) ?>"
                                class="mt-4 inline-block px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        You haven't created or joined any classes yet. Click on "Create New Class" to get started.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Students View -->
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">
            <?php if (isset($classes) && count($classes) > 0): ?>
                <?php foreach ($classes as $class): ?>
                    <div class="flex flex-col p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                        <div class="w-full">
                            <h4 class="text-xl font-semibold text-gray-700 dark:text-gray-200">
                                <?= esc($class['name']) ?>
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                <?= esc($class['description']) ?>
                            </p>

                            <a href="<?= site_url('class/details/' . $class['id']) ?>"
                                class="mt-4 inline-block px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        You haven't joined any classes yet. Click on "Join Class" to enter a class code.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>