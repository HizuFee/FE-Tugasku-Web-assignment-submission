<!-- app/Views/assignment/edit.php -->
<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>Edit Assignment - <?= $assignment['title'] ?? 'Assignment' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center mb-6">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Edit Tugas
        </h2>
        <a href="<?= base_url("class/{$classId}/assignments/{$assignment['id']}") ?>"
            class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Tugas
        </a>
    </div>

    <?= $this->include('partials/alerts') ?>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <form action="<?= base_url("class/{$classId}/assignments/{$assignment['id']}/update") ?>" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="title">
                    Judul Tugas
                </label>
                <input
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    type="text"
                    id="title"
                    name="title"
                    value="<?= old('title', $assignment['title'] ?? '') ?>"
                    required />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="description">
                    Deskripsi
                </label>
                <textarea
                    class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                    id="description"
                    name="description"
                    rows="5"
                    required><?= old('description', $assignment['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="deadline">
                    Tenggat Waktu
                </label>
                <input
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    type="datetime-local"
                    id="deadline"
                    name="deadline"
                    value="<?= old('deadline', date('Y-m-d\TH:i', strtotime($assignment['deadline']))) ?>"
                    required />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="file">
                    File Lampiran (Opsional)
                </label>
                <?php if (!empty($assignment['file_path'])): ?>
                    <div class="mb-2 flex items-center">
                        <span class="text-sm text-gray-700 dark:text-gray-400 mr-2">File saat ini:</span>
                        <a href="<?= base_url("class/{$classId}/assignments/{$assignment['id']}/download") ?>" class="text-sm text-blue-500 hover:underline">
                            <i class="fas fa-download mr-1"></i> Download File
                        </a>
                    </div>
                <?php endif; ?>
                <input
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    type="file"
                    id="file"
                    name="file" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Format yang didukung: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, JPG, PNG, ZIP
                </p>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>