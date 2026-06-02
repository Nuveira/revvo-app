<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../includes/auth.php';
checkRole(['mechanic']);
