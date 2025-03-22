<?= $this->extend('layout/default') ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Edit Class
        </h2>
        <a href="<?= site_url('class/details/' . $class['id']) ?>" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Back to Class
        </a>
    </div>

    <?php if (session()->has('error')): ?>
        <div x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 px-4 py-2 bg-red-100 text-red-700 rounded-lg">
            <?= session()->get('error') ?>
        </div>
    <?php endif; ?>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <form action="<?= site_url('class/update/' . $class['id']) ?>" method="post">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Class Name
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= esc($class['name']) ?>"
                    required
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    placeholder="Enter class name" />
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Description
                </label>
                <textarea
                    id="description"
                    name="description"
                    required
                    rows="4"
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    placeholder="Enter class description"><?= esc($class['description']) ?></textarea>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Update Class
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>