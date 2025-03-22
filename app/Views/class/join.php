<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
    <?php if (session()->has('error')): ?>
        <div x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3000)"
            class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded-lg">
            <?= session()->get('error') ?>
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-center">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            <?= $role === 'teacher' ? 'Contribute to Class' : 'Join Class' ?>
        </h2>
        <a href="<?= site_url('dashboard') ?>" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Back to Dashboard
        </a>
    </div>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <form action="<?= site_url('class/join') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Class Code
                </label>
                <input
                    id="code"
                    name="code"
                    type="text"
                    placeholder="Enter the 6-character class code"
                    value="<?= old('code') ?>"
                    required
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    minlength="6"
                    maxlength="6" />
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <?php if ($role === 'teacher'): ?>
                        You will be added as a contributor to this class and will be able to help manage it.
                    <?php else: ?>
                        You will be enrolled in this class as a student.
                    <?php endif; ?>
                </p>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-blue">
                    <?= $role === 'teacher' ? 'Contribute Now' : 'Join Class' ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>