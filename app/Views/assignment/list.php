<!-- app/Views/assignment/list.php -->
<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>Assignments - <?= $class['name'] ?? 'Class' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center my-6">
        <div>
            <a href="<?= base_url("class/details/{$class['id']}") ?>" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-arrow-left"></i> Kembali ke Kelas
            </a>
            <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
                Tugas untuk <?= $class['name'] ?? 'Kelas' ?>
            </h2>
        </div>
        <?php if ($userRole === 'teacher' && in_array($userClassRole, ['owner', 'contributor'])): ?>
            <div>
                <a href="<?= base_url("class/{$class['id']}/assignments/create") ?>" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    <i class="fas fa-plus"></i> Buat Tugas
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?= $this->include('partials/alerts') ?>

    <?php if (empty($assignments)): ?>
        <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800 text-gray-600 dark:text-gray-400">
            Tidak ada tugas ditemukan untuk kelas ini.
        </div>
    <?php else: ?>
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($assignments as $assignment): ?>
                <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                    <div class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                        <?= esc($assignment['title']) ?>
                    </div>
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <?= strlen($assignment['description']) > 100 ? substr(esc($assignment['description']), 0, 100) . '...' : esc($assignment['description']) ?>
                    </div>
                    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-calendar"></i>
                        Tenggat: <?= date('d M Y, H:i', strtotime($assignment['deadline'])) ?>
                    </div>

                    <?php if ($userRole === 'student' && isset($assignment['submission'])): ?>
                        <div class="mb-4">
                            <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full <?= getStatusBadgeClass($assignment['submission']['status'] ?? 'pending') ?>">
                                <?= ucfirst($assignment['submission']['status'] ?? 'pending') ?>
                            </span>

                            <?php if (isset($assignment['submission']['grade'])): ?>
                                <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full bg-blue-100 text-blue-700 dark:bg-blue-700 dark:text-blue-100 ml-2">
                                    Nilai: <?= $assignment['submission']['grade'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex mt-4 space-x-2">
                        <a href="<?= base_url("class/{$class['id']}/assignments/{$assignment['id']}") ?>" class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                            Lihat Detail
                        </a>

                        <?php if ($userRole === 'teacher' && in_array($userClassRole, ['owner', 'contributor'])): ?>
                            <a href="<?= base_url("class/{$class['id']}/assignments/{$assignment['id']}/edit") ?>" class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-gray-600 border border-transparent rounded-md active:bg-gray-600 hover:bg-gray-700 focus:outline-none focus:shadow-outline-gray">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?= base_url("class/{$class['id']}/assignments/{$assignment['id']}/delete") ?>"
                                class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-md active:bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Helper function for submission status badges
function getStatusBadgeClass($status)
{
    switch ($status) {
        case 'submitted':
            return 'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100';
        case 'late':
            return 'text-orange-700 bg-orange-100 dark:bg-orange-700 dark:text-orange-100';
        case 'graded':
            return 'text-blue-700 bg-blue-100 dark:bg-blue-700 dark:text-blue-100';
        case 'pending':
        default:
            return 'text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-100';
    }
}
?>
<?= $this->endSection() ?>