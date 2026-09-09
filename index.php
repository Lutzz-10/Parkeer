<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_user'])) {
    header("Location: /parkeer/" . $_SESSION['role'] . "/dashboard.php");
} else {
    header("Location: /parkeer/auth/login.php");
}
exit;