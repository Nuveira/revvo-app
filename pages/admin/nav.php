<?php
$current_page  = basename($_SERVER['PHP_SELF']);
$nama          = $nama ?? ($_SESSION['name'] ?? 'Admin');
$role          = $role ?? ($_SESSION['role'] ?? '');
$profile_photo = $profile_photo ?? null;
?>

<div class="h-screen w-64 bg-gradient-to-b from-black via-black via-15% to-[#8E1616] text-white flex flex-col shrink-0">
    <div class="flex flex-col pt-6 gap-2 px-6">
        <span class="font-bold text-2xl tracking-tighter flex items-center gap-2 mx-5 pb-2">
            <img src="../../assets/images/logo.png" alt="Revvo Logo" class="h-8 w-auto invert brightness-0 invert">
            REVVO
        </span>
        <div class="flex items-center gap-3 m-2 pb-1">
            <?php if (!empty($profile_photo)): ?>
                <img src="../../uploads/profile/<?= htmlspecialchars($profile_photo) ?>" alt="Profile" class="w-10 h-10 shrink-0 rounded-full object-cover">
            <?php else: ?>
                <div class="w-10 h-10 shrink-0 rounded-full bg-white/20 flex items-center justify-center text-lg font-bold">
                    <?= strtoupper(substr($nama, 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="min-w-0">
                <p class="text-white font-semibold text-sm leading-tight truncate"><?= htmlspecialchars($nama) ?></p>
                <p class="text-white/70 text-xs mt-1"><?= htmlspecialchars($role) ?></p>
            </div>
        </div>
        <hr class="border-white/20">
    </div>

    <nav class="flex flex-col gap-1 px-6 py-4 overflow-y-auto">
      <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">space_dashboard</span>
          Dashboard
      </a>
      <a href="users.php" class="<?= $current_page === 'users.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">manage_accounts</span>
          Users
      </a>
      <a href="bookings.php" class="<?= $current_page === 'bookings.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">assignment</span>
          Bookings
      </a>
      <a href="service_types.php" class="<?= $current_page === 'service_types.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">build_circle</span>
          Service Types
      </a>
      <a href="mechanics.php" class="<?= $current_page === 'mechanics.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">construction</span>
          Mechanics
      </a>
      <a href="spare_parts.php" class="<?= $current_page === 'spare_parts.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">inventory</span>
          Spare Parts
      </a>
      <a href="time_slots.php" class="<?= $current_page === 'time_slots.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">event_available</span>
          Time Slots
      </a>
      <a href="audit_logs.php" class="<?= $current_page === 'audit_logs.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">history_edu</span>
          Audit Logs
      </a>
      <a href="reports.php" class="<?= $current_page === 'reports.php' ? 'bg-[#FF0000]' : 'hover:bg-white/10' ?> flex items-center text-white py-2 px-4 rounded transition-colors">
          <span class="material-symbols-outlined pr-3">bar_chart</span>
          Reports
      </a>
      <hr class="border-white/20 my-1">
      <a href="<?= url('pages/auth/logout.php') ?>" class="text-white py-2 px-4 hover:bg-white hover:text-black rounded flex items-center gap-2 transition-colors duration-200">
          <span class="material-symbols-outlined" style="font-size: 20px;">exit_to_app</span>
          Keluar
      </a>
  </nav>
</div>
