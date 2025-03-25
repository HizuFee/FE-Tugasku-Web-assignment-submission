<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>Assignment Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
    <!-- Header Section with Title and Actions -->
    <div class="flex justify-between items-center my-6">
        <div>
            <a href="<?= base_url("class/{$classId}/assignments") ?>" class="flex items-center text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Tugas
            </a>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200"><?= esc($assignment['title'] ?? 'Detail Tugas') ?></h2>
        </div>
        <?php if ($userRole === 'teacher' && in_array($userClassRole, ['owner', 'contributor'])): ?>
            <div class="flex space-x-2">
                <a href="<?= base_url("class/{$classId}/assignments/{$assignment['id']}/edit") ?>"
                    class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    Edit Tugas
                </a>
                <a href="<?= base_url("class/{$classId}/assignments/{$assignment['id']}/delete") ?>"
                    class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?= $this->include('partials/alerts') ?>

    <div class="grid gap-6 mb-8 md:grid-cols-3">
        <!-- Left Column - Assignment Details and Submissions -->
        <div class="md:col-span-2">
            <!-- Assignment Details Card -->
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Detail Tugas
                </h4>
                <div class="mb-4">
                    <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 border-b pb-2">Deskripsi</h5>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md mt-2 text-gray-700 dark:text-gray-300">
                        <?= nl2br(esc($assignment['description'] ?? '')) ?>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <strong>Dibuat oleh:</strong> <?= esc($assignment['creator_name'] ?? 'Guru') ?>
                    </div>
                    <div class="text-gray-600 dark:text-gray-400 <?= strtotime($assignment['deadline'] ?? 'now') < time() ? 'text-red-600 dark:text-red-400' : '' ?>">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <strong>Tenggat waktu:</strong> <?= date('d M Y, H:i', strtotime($assignment['deadline'] ?? 'now')) ?>
                        <?= strtotime($assignment['deadline'] ?? 'now') < time() ? '<span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100 ml-2">Lewat</span>' : '' ?>
                    </div>
                </div>
            </div>

            <!-- Teacher view - List of submissions -->
            <?php if ($userRole === 'teacher' && !empty($submissions)): ?>
                <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 mt-6">
                    <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Pengumpulan Siswa
                    </h4>
                    <div class="w-full overflow-hidden rounded-lg shadow-xs">
                        <div class="w-full overflow-x-auto">
                            <table class="w-full whitespace-no-wrap">
                                <thead>
                                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                        <th class="px-4 py-3">Siswa</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Dikumpulkan</th>
                                        <th class="px-4 py-3">Nilai</th>
                                        <th class="px-4 py-3">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                                    <?php foreach ($submissions as $submission): ?>
                                        <tr class="text-gray-700 dark:text-gray-400">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center text-sm">
                                                    <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                                                        <div class="absolute inset-0 rounded-full bg-purple-600 flex items-center justify-center text-white">
                                                            <?= substr($submission['student_name'], 0, 1) ?>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold"><?= esc($submission['student_name']) ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="px-2 py-1 font-semibold leading-tight rounded-full <?= getStatusBadgeClass($submission['status']) ?>">
                                                    <?= ucfirst($submission['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <?= !empty($submission['submitted_at']) ? date('d M Y, H:i', strtotime($submission['submitted_at'])) : '-' ?>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-semibold">
                                                <?= isset($submission['grade']) ? "{$submission['grade']}/100" : '-' ?>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex items-center space-x-2">
                                                    <?php if (in_array($submission['status'], ['submitted', 'late'])): ?>
                                                        <?php if (!empty($submission['file_path'])): ?>
                                                            <a href="<?= base_url('../uploads/' . basename($submission['file_path'])) ?>"
                                                                class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                                target="_blank" title="Unduh pengumpulan">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                                </svg>
                                                            </a>
                                                        <?php endif; ?>

                                                        <button type="button"
                                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-green-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#gradeModal<?= $submission['id'] ?>" title="Nilai pengumpulan">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </button>

                                                        <!-- Grade Modal -->
                                                        <div class="modal fade" id="gradeModal<?= $submission['id'] ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Nilai Pengumpulan - <?= esc($submission['student_name']) ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form action="<?= base_url("class/{$classId}/assignments/submission/{$submission['id']}/grade") ?>" method="post">
                                                                        <div class="modal-body">
                                                                            <div class="mb-3">
                                                                                <label for="submissionNotes<?= $submission['id'] ?>" class="form-label">Catatan Siswa</label>
                                                                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md">
                                                                                    <?= nl2br(esc($submission['notes'] ?? 'Tidak ada catatan')) ?>
                                                                                </div>
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label for="grade<?= $submission['id'] ?>" class="form-label">Nilai (0-100)</label>
                                                                                <input type="number" class="form-control" id="grade<?= $submission['id'] ?>"
                                                                                    name="grade" min="0" max="100" required
                                                                                    value="<?= $submission['grade'] ?? '' ?>">
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label for="feedback<?= $submission['id'] ?>" class="form-label">Umpan Balik</label>
                                                                                <textarea class="form-control" id="feedback<?= $submission['id'] ?>"
                                                                                    name="feedback" rows="4" required><?= $submission['feedback'] ?? '' ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:shadow-outline-gray" data-bs-dismiss="modal">Tutup</button>
                                                                            <button type="submit" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">Simpan Nilai</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php elseif ($submission['status'] === 'graded'): ?>
                                                        <button type="button"
                                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewGradeModal<?= $submission['id'] ?>" title="Lihat detail nilai">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </button>

                                                        <!-- View Grade Modal -->
                                                        <div class="modal fade" id="viewGradeModal<?= $submission['id'] ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Detail Nilai - <?= esc($submission['student_name']) ?></h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Siswa</label>
                                                                            <p class="font-semibold"><?= esc($submission['student_name']) ?></p>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Nilai</label>
                                                                            <p class="font-semibold"><?= $submission['grade'] ?>/100</p>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Umpan Balik</label>
                                                                            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md">
                                                                                <?= nl2br(esc($submission['feedback'] ?? 'Tidak ada umpan balik')) ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Dinilai Pada</label>
                                                                            <p><?= date('d M Y, H:i', strtotime($submission['graded_at'])) ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:shadow-outline-gray" data-bs-dismiss="modal">Tutup</button>
                                                                        <button type="button"
                                                                            class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#gradeModal<?= $submission['id'] ?>"
                                                                            data-bs-dismiss="modal">
                                                                            Edit Nilai
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-gray-500 italic">Belum dikumpulkan</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Submission Form or Stats -->
        <div class="md:col-span-1">
            <!-- Student view - Submission form or status -->
            <?php if ($userRole === 'student'): ?>
                <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                    <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Pengumpulan Anda
                    </h4>
                    <?php if (isset($userSubmission) && in_array($userSubmission['status'], ['submitted', 'late', 'graded'])): ?>
                        <div class="p-3 mb-4 text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-800 dark:text-blue-200">
                            <p class="flex items-center">
                                <strong>Status:</strong>
                                <span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full <?= getStatusBadgeClass($userSubmission['status']) ?> ml-2">
                                    <?= ucfirst($userSubmission['status']) ?>
                                </span>
                            </p>

                            <?php if (!empty($userSubmission['submitted_at'])): ?>
                                <p class="mt-2"><strong>Dikumpulkan pada:</strong> <?= date('d M Y, H:i', strtotime($userSubmission['submitted_at'])) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($userSubmission['file_path'])): ?>
                                <p class="mt-2 flex items-center">
                                    <strong>File Anda:</strong>
                                    <a href="<?= base_url('../uploads/' . basename($userSubmission['file_path'])) ?>" class="ml-2 px-2 py-1 text-xs font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple" target="_blank">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Unduh
                                    </a>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($userSubmission['notes'])): ?>
                                <p class="mt-2"><strong>Catatan Anda:</strong></p>
                                <div class="p-2 bg-white dark:bg-gray-700 rounded-md mt-1 mb-2">
                                    <?= nl2br(esc($userSubmission['notes'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($userSubmission['status'] === 'graded'): ?>
                                <div class="mt-3 p-3 bg-white dark:bg-gray-700 rounded-md border border-gray-200 dark:border-gray-600">
                                    <h5 class="flex items-center text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Nilai: <span class="ml-2 px-2 py-1 text-xs font-semibold leading-tight rounded-full <?= $userSubmission['grade'] >= 60 ? 'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' : 'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' ?>"><?= $userSubmission['grade'] ?>/100</span>
                                    </h5>
                                    <p class="mt-3 mb-1 text-gray-700 dark:text-gray-300"><strong>Umpan Balik:</strong></p>
                                    <div class="italic text-gray-600 dark:text-gray-400">
                                        <?= nl2br(esc($userSubmission['feedback'] ?? 'Tidak ada umpan balik')) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (strtotime($assignment['deadline']) > time() && $userSubmission['status'] !== 'graded'): ?>
                                <div class="mt-3">
                                    <p class="text-gray-600 dark:text-gray-400">Anda masih dapat memperbarui pengumpulan sebelum tenggat waktu.</p>
                                    <button class="w-full px-4 py-2 mt-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#updateSubmission"
                                        aria-expanded="false">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        Perbarui Pengumpulan
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (strtotime($assignment['deadline']) > time() && $userSubmission['status'] !== 'graded'): ?>
                            <div class="collapse mt-3" id="updateSubmission">
                                <div class="p-4 border border-purple-200 rounded-lg dark:border-purple-800">
                                    <h5 class="mb-3 font-semibold text-gray-700 dark:text-gray-300">
                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Perbarui Pengumpulan Anda
                                    </h5>
                                    <form action="<?= base_url("class/{$classId}/assignments/{$assignment['id']}/submit") ?>"
                                        method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="file" class="block text-sm text-gray-700 dark:text-gray-400">Unggah File (opsional)</label>
                                            <input type="file" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" id="file" name="file">
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Ukuran file maksimal: 10MB
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="block text-sm text-gray-700 dark:text-gray-400">Catatan (opsional)</label>
                                            <textarea class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray" id="notes" name="notes" rows="4"><?= old('notes', $userSubmission['notes'] ?? '') ?></textarea>
                                        </div>

                                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                            </svg>
                                            Perbarui Pengumpulan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <?php if (strtotime($assignment['deadline']) < time()): ?>
                            <div class="p-3 mb-4 text-red-700 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <strong>Tenggat waktu telah lewat.</strong> Anda masih dapat mengumpulkan tetapi akan ditandai sebagai terlambat.
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url("class/{$classId}/assignments/{$assignment['id']}/submit") ?>"
                            method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="file" class="block text-sm text-gray-700 dark:text-gray-400">Unggah File (opsional)</label>
                                <input type="file" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" id="file" name="file">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ukuran file maksimal: 10MB
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="block text-sm text-gray-700 dark:text-gray-400">Catatan (opsional)</label>
                                <textarea class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray" id="notes" name="notes" rows="4"><?= old('notes') ?></textarea>
                            </div>

                            <button type="submit" class="w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Kumpulkan Tugas
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Assignment Stats (for teachers) -->
            <?php if ($userRole === 'teacher'): ?>
                <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
                    <h4 class="mb-4 font-semibold text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Statistik Tugas
                    </h4>
                    <div class="mb-4">
                        <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 border-b pb-2">Status Pengumpulan</h5>
                        <?php
                        $totalStudents = count($submissions);
                        $submittedCount = 0;
                        $gradedCount = 0;
                        $lateCount = 0;

                        foreach ($submissions as $sub) {
                            if (in_array($sub['status'], ['submitted', 'late', 'graded'])) {
                                $submittedCount++;
                            }
                            if ($sub['status'] === 'graded') {
                                $gradedCount++;
                            }
                            if ($sub['status'] === 'late') {
                                $lateCount++;
                            }
                        }

                        $submittedPercent = $totalStudents > 0 ? ($submittedCount / $totalStudents) * 100 : 0;
                        $gradedPercent = $totalStudents > 0 ? ($gradedCount / $totalStudents) * 100 : 0;
                        $latePercent = $totalStudents > 0 ? ($lateCount / $totalStudents) * 100 : 0;
                        ?>
                        <div class="w-full h-4 mt-3 bg-gray-200 rounded-full dark:bg-gray-700">
                            <div class="h-4 bg-purple-600 rounded-full" style="width: <?= $submittedPercent ?>%"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?= $submittedCount ?> dari <?= $totalStudents ?> siswa telah mengumpulkan (<?= number_format($submittedPercent, 1) ?>%)
                        </p>

                        <div class="grid grid-cols-2 gap-4 mt-3">
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md text-center">
                                <h5 class="mb-1 text-xl font-bold text-purple-600 dark:text-purple-400"><?= $gradedCount ?></h5>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 inline-block mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Dinilai
                                </p>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md text-center">
                                <h5 class="mb-1 text-xl font-bold text-yellow-500 dark:text-yellow-400"><?= $lateCount ?></h5>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 inline-block mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Terlambat
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if ($gradedCount > 0): ?>
                        <div class="mt-4">
                            <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 border-b pb-2">Distribusi Nilai</h5>
                            <?php
                            $gradeRanges = [
                                '90-100' => 0,
                                '80-89' => 0,
                                '70-79' => 0,
                                '60-69' => 0,
                                '0-59' => 0
                            ];

                            $sumGrades = 0;
                            $gradeCount = 0;

                            foreach ($submissions as $sub) {
                                if (isset($sub['grade'])) {
                                    $grade = (int)$sub['grade'];
                                    $sumGrades += $grade;
                                    $gradeCount++;

                                    if ($grade >= 90) {
                                        $gradeRanges['90-100']++;
                                    } elseif ($grade >= 80) {
                                        $gradeRanges['80-89']++;
                                    } elseif ($grade >= 70) {
                                        $gradeRanges['70-79']++;
                                    } elseif ($grade >= 60) {
                                        $gradeRanges['60-69']++;
                                    } else {
                                        $gradeRanges['0-59']++;
                                    }
                                }
                            }

                            $avgGrade = $gradeCount > 0 ? $sumGrades / $gradeCount : 0;
                            ?>

                            <div class="mb-3 mt-3">
                                <div class="p-3 text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-800 dark:text-blue-200 flex items-center justify-between">
                                    <span><strong>Rata-rata Nilai:</strong></span>
                                    <span class="px-2 py-1 text-xs font-semibold leading-tight text-white bg-purple-600 rounded-full dark:bg-purple-500"><?= number_format($avgGrade, 1) ?>/100</span>
                                </div>
                            </div>

                            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                                <div class="w-full overflow-x-auto">
                                    <table class="w-full whitespace-no-wrap">
                                        <thead>
                                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                                <th class="px-3 py-2">Rentang</th>
                                                <th class="px-3 py-2">Jumlah</th>
                                                <th class="px-3 py-2">Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                                            <?php foreach ($gradeRanges as $range => $count): ?>
                                                <tr class="text-gray-700 dark:text-gray-400">
                                                    <td class="px-3 py-2 text-sm">
                                                        <?php
                                                        $rangeClass = '';
                                                        if ($range === '90-100') $rangeClass = 'text-green-600 dark:text-green-400 font-semibold';
                                                        else if ($range === '0-59') $rangeClass = 'text-red-600 dark:text-red-400';
                                                        ?>
                                                        <span class="<?= $rangeClass ?>"><?= $range ?></span>
                                                    </td>
                                                    <td class="px-3 py-2 text-sm"><?= $count ?></td>
                                                    <td class="px-3 py-2 text-sm">
                                                        <?= $gradeCount > 0 ? number_format(($count / $gradeCount) * 100, 1) : 0 ?>%
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
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