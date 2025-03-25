<!-- app/Views/assignment/edit.php -->
<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>Edit Assignment<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Edit Assignment
        </h2>
        <a href="<?= base_url("class/{$classId}/assignments/{$assignment['id']}") ?>"
            class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <?= $this->include('partials/alerts') ?>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <form action="<?= base_url("class/{$classId}/assignments/{$assignment['id']}/update") ?>" method="post">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="title">
                    Judul Assignment
                </label>
                <input type="text"
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    id="title"
                    name="title"
                    value="<?= old('title', $assignment['title'] ?? '') ?>"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="description">
                    Deskripsi
                </label>
                <textarea class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    id="description"
                    name="description"
                    rows="5"
                    required><?= old('description', $assignment['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="deadline">
                    Deadline
                </label>
                <input type="datetime-local"
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    id="deadline"
                    name="deadline"
                    value="<?= old('deadline', date('Y-m-d\TH:i', strtotime($assignment['deadline'] ?? 'now'))) ?>"
                    required>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Perbarui Assignment
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>