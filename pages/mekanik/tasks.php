<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
checkRole(['mechanic']);

/*
Sesuaikan mekanik_id dari session jika project sudah menyimpannya.
*/
echo 'Mechanic Flow Placeholder';
