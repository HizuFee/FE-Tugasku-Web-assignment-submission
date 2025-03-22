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

    <div class="flex justify-between items-center">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            <?= esc($class['name']) ?>
        </h2>
        <a href="<?= site_url('dashboard') ?>" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Back to Dashboard
        </a>
    </div>

    <?php if ($userClassRole === 'owner'): ?>
        <div class="flex justify-end space-x-4 mt-4 mb-4">
            <a href="<?= site_url('class/edit/' . $class['id']) ?>"
                class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                Edit Class
            </a>

            <a href="#"
                onclick="confirmDelete(<?= $class['id'] ?>)"
                class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:shadow-outline-red">
                Delete Class
            </a>
        </div>

        <script>
            function confirmDelete(classId) {
                if (confirm('Are you sure you want to delete this class? This action cannot be undone.')) {
                    window.location.href = '<?= site_url('class/delete/') ?>' + classId;
                }
            }
        </script>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-8">
        <div class="flex justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Class Details</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                    <?= esc($class['description']) ?>
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                    Logged in as: <span class="font-medium"><?= esc(session()->get('user')['name']) ?></span>
                    <span class="mx-2">•</span>
                    <span class="capitalize"><?= esc(session()->get('user')['role']) ?></span>
                    <?php if (isset($class['joinedAt'])): ?>
                        <span class="mx-2">•</span>
                        Joined: <?= date('d M Y', strtotime($class['joinedAt'])) ?>
                    <?php endif; ?>
                </p>
            </div>

            <?php if ($userClassRole === 'owner'): ?>

                <div class="text-right">
                    <div class="mt-4 text-sm">
                        <span class="block text-gray-500">Class Code:</span>
                        <span class="font-bold text-lg text-gray-700 dark:text-gray-200"><?= esc($class['code']) ?></span>
                    </div>
                    <span class="px-3 py-1 text-white">Your role :</span>
                    <span class="px-3 py-1 text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">Owner</span>

                </div>

            <?php elseif ($userClassRole === 'contributor'): ?>
                <div class="text-right">
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Contributor</span>
                </div>
            <?php else: ?>
                <div class="text-right">
                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-sm">Student</span>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="grid gap-6 mb-8 md:grid-cols-2">
        <!-- Teachers/Contributors List -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Teachers</h3>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <?php foreach ($contributors as $contributor): ?>
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3"><?= esc($contributor['name']) ?></td>
                                <td class="px-4 py-3"><?= esc($contributor['email']) ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($contributor['role'] === 'owner'): ?>
                                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:bg-red-700 dark:text-red-100">
                                            Owner
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                            Contributor
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Students List -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Students</h3>
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                                <tr class="text-gray-700 dark:text-gray-400">
                                    <td class="px-4 py-3"><?= esc($student['name']) ?></td>
                                    <td class="px-4 py-3"><?= esc($student['email']) ?></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 font-semibold leading-tight  bg-blue-600 text-white rounded-full">
                                            Student
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td colspan="2" class="px-4 py-3 text-center">No students have joined this class yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Additional class functionality can go here -->
</div>
<?= $this->endSection() ?>