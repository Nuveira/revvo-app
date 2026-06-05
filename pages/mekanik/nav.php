<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

$nama = 'Mechanic';
$role = 'mechanic';
$profile_photo = null;

if ($user_id) {

    $stmt = $conn->prepare("
        SELECT name, role, profile_photo
        FROM users
        WHERE id = ?
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        $nama = $user['name'];
        $role = $user['role'];
        $profile_photo = $user['profile_photo'];
    }

    $stmt->close();
}
?>

<div class="w-72 bg-black text-white flex flex-col">

    <div class="p-6 border-b border-gray-800">

        <h1 class="text-3xl font-bold">
            REVVO
        </h1>

        <p class="text-gray-400 text-sm mt-1">
            Mechanic Panel
        </p>

    </div>

    <div class="flex items-center gap-3 p-5 border-b border-gray-800">

        <?php if (!empty($profile_photo)): ?>

            <img
                src="../../uploads/profile/<?= htmlspecialchars($profile_photo); ?>"
                class="w-12 h-12 rounded-full object-cover"
            >

        <?php else: ?>

            <div class="w-12 h-12 rounded-full bg-[#8E1616] flex items-center justify-center text-lg font-bold">
                <?= strtoupper(substr($nama, 0, 1)); ?>
            </div>

        <?php endif; ?>

        <div>

            <p class="font-semibold">
                <?= htmlspecialchars($nama); ?>
            </p>

            <p class="text-xs text-gray-400">
                <?= htmlspecialchars($role); ?>
            </p>

        </div>

    </div>

    <nav class="flex-1 p-4">

        <ul class="space-y-2">

            <li>
                <a
                    href="dashboard.php"
                    class="block px-4 py-3 rounded-lg hover:bg-[#8E1616] transition"
                >
                    Dashboard
                </a>
            </li>

            <li>
                <a
                    href="my_tasks.php"
                    class="block px-4 py-3 rounded-lg hover:bg-[#8E1616] transition"
                >
                    My Tasks
                </a>
            </li>

            <li>
                <a
                    href="history.php"
                    class="block px-4 py-3 rounded-lg hover:bg-[#8E1616] transition"
                >
                    History
                </a>
            </li>

        </ul>

    </nav>

    <div class="p-4 border-t border-gray-800">

        <a
            href="../auth/logout.php"
            class="block text-center bg-[#8E1616] py-3 rounded-lg hover:bg-[#6f1111]"
        >
            Logout
        </a>

    </div>

</div>