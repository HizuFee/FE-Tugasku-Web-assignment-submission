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
            Create New Class
        </h2>
        <a href="<?= site_url('dashboard') ?>" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            Back to Dashboard
        </a>
    </div>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <form action="<?= site_url('class/create') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Class Name
                </label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    placeholder="Enter class name"
                    value="<?= old('name') ?>"
                    required
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" />
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Description
                </label>
                <textarea
                    id="description"
                    name="description"
                    placeholder="Enter class description"
                    required
                    class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    rows="4"><?= old('description') ?></textarea>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    You will be the owner of this class. You can invite other teachers to be contributors.
                </p>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                    Create Class
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>