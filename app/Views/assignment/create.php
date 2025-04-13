<!-- app/Views/assignment/create.php -->
<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>Create Assignment - <?= $class['name'] ?? 'Class' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center mb-6">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Buat Tugas Baru
        </h2>
        <a href="<?= base_url("class/{$class['id']}/assignments") ?>"
            class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Tugas
        </a>
    </div>

    <?= $this->include('partials/alerts') ?>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <form action="<?= base_url("class/{$class['id']}/assignments/create") ?>" method="post" enctype="multipart/form-data" id="createAssignmentForm" onsubmit="return validateForm()">
            <!-- Hidden input for storing selected students -->
            <input type="hidden" name="selected_students" id="selected_students_input">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="title">
                    Judul Tugas
                </label>
                <input
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    type="text"
                    id="title"
                    name="title"
                    value="<?= old('title') ?>"
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
                    required><?= old('description') ?></textarea>
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
                    value="<?= old('deadline') ?>"
                    required />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400" for="file">
                    File Lampiran (Opsional)
                </label>
                <input
                    class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    type="file"
                    id="file"
                    name="file" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format yang didukung: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, JPG, PNG, ZIP, RAR, dll (Max: 10MB)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                    Pilih Siswa
                </label>
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="selectAll" class="mr-2" onchange="toggleAllStudents(this)">
                    <label for="selectAll" class="text-sm text-gray-700 dark:text-gray-400">
                        Pilih Semua Siswa
                    </label>
                </div>
                <div class="w-full overflow-hidden rounded-lg shadow-xs">
                    <div class="w-full overflow-x-auto">
                        <table class="w-full whitespace-no-wrap">
                            <thead>
                                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                    <th class="px-4 py-3">Pilih</th>
                                    <th class="px-4 py-3">Nama</th>
                                    <th class="px-4 py-3">Email</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                                <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-center text-gray-500">
                                            Tidak ada siswa dalam kelas ini
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($students as $student): ?>
                                        <tr class="text-gray-700 dark:text-gray-400">
                                            <td class="px-4 py-3">
                                                <input type="checkbox"
                                                    value="<?= $student['id'] ?>"
                                                    class="student-checkbox"
                                                    id="student_<?= $student['id'] ?>"
                                                    onclick="updateSelectedStudents()">
                                            </td>
                                            <td class="px-4 py-3">
                                                <label for="student_<?= $student['id'] ?>" class="flex items-center text-sm cursor-pointer">
                                                    <div class="relative hidden w-8 h-8 mr-3 rounded-full md:block">
                                                        <div class="absolute inset-0 rounded-full bg-purple-600 flex items-center justify-center text-white">
                                                            <?= substr($student['name'], 0, 1) ?>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold"><?= esc($student['name']) ?></p>
                                                    </div>
                                                </label>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <?= esc($student['email']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Siswa terpilih: <span id="selectedCount">0</span>
                </p>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Buat Tugas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAllStudents(checkbox) {
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        studentCheckboxes.forEach(box => {
            box.checked = checkbox.checked;
        });
        updateSelectedStudents();
    }

    function updateSelectedStudents() {
        const selectedStudents = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
        document.getElementById('selected_students_input').value = selectedStudents.join(',');
        document.getElementById('selectedCount').textContent = selectedStudents.length;

        // Update select all checkbox state
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(studentCheckboxes).some(cb => cb.checked);

        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
    }

    function validateForm() {
        const selectedStudents = document.getElementById('selected_students_input').value;
        if (!selectedStudents) {
            alert('Silakan pilih minimal satu siswa untuk tugas ini.');
            return false;
        }

        // Log form data before submission
        const formData = new FormData(document.getElementById('createAssignmentForm'));
        console.log('Form data before submission:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        return true;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSelectedStudents();
    });
</script>
<?= $this->endSection() ?>