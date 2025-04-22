<?php
require_once __DIR__ . '/config.php';
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: tasks.php');
    exit;
}
header('Location: login.php');
exit;
?>