<!-- app/Views/assignment/list.php -->
<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>Assignments - <?= $class['name'] ?? 'Class' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center my-6">
        <div>
            <a href="<?= base_url("class/details/{$class['id']}") ?>" class="flex items-center text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Detail Kelas
            </a>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">Daftar Tugas</h2>
        </div>
        <?php if ($userRole === 'teacher' && in_array($userClassRole, ['owner', 'contributor'])): ?>
            <a href="<?= base_url("class/{$class['id']}/assignments/create") ?>"
                class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Buat Tugas
            </a>
        <?php endif; ?>
    </div>

    <?= $this->include('partials/alerts') ?>

    <?php if (empty($assignments)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xs p-4 text-center">
            <p class="text-gray-600 dark:text-gray-400">Belum ada tugas di kelas ini.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($assignments as $assignment): ?>
                <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full dark:text-purple-100 dark:bg-purple-500">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <a href="<?= base_url("class/{$class['id']}/assignments/{$assignment['id']}") ?>"
                                    class="text-lg font-semibold text-gray-700 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400">
                                    <?= esc($assignment['title']) ?>
                                </a>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    oleh <?= esc($assignment['creator_name']) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <?= nl2br(esc(substr($assignment['description'], 0, 100) . (strlen($assignment['description']) > 100 ? '...' : ''))) ?>
                    </div>

                    <?php if ($userRole === 'teacher'): ?>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Ditugaskan ke:</span>
                                <?= count($assignment['selected_students'] ?? []) ?> siswa
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if ($userRole === 'student' && isset($assignment['submission'])): ?>
                        <div class="mb-4">
                            <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full 
                                <?= getStatusBadgeClass($assignment['submission']['status']) ?>">
                                <?= ucfirst($assignment['submission']['status']) ?>
                            </span>
                            <?php if ($assignment['submission']['status'] === 'graded'): ?>
                                <span class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                    Nilai: <?= $assignment['submission']['grade'] ?>/100
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-center text-sm">
                        <div class="text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php
                            $deadline = new DateTime($assignment['deadline']);
                            $now = new DateTime();
                            $interval = $now->diff($deadline);
                            $isPast = $now > $deadline;
                            ?>
                            <?php if ($isPast): ?>
                                <span class="text-red-600 dark:text-red-400">Tenggat waktu telah lewat</span>
                            <?php else: ?>
                                Sisa <?= $interval->days ?> hari
                            <?php endif; ?>
                        </div>
                        <a href="<?= base_url("class/{$class['id']}/assignments/{$assignment['id']}") ?>"
                            class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                            Detail
                        </a>
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
            return 'text-yellow-700 bg-yellow-100 dark:bg-yellow-700 dark:text-yellow-100';
        case 'graded':
            return 'text-blue-700 bg-blue-100 dark:bg-blue-700 dark:text-blue-100';
        case 'pending':
        default:
            return 'text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-100';
    }
}
?>
<?= $this->endSection() ?>