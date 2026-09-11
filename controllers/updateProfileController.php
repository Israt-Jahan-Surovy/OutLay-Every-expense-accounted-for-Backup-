<?php
session_start();
require_once __DIR__ . '/../models/usersModel.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id    = $_SESSION['user_id'];
    $user_name  = trim($_POST['user_name']);
    $user_email = trim($_POST['user_email']);
    $username   = trim($_POST['username']);

    if (!empty($user_name) && !empty($user_email) && !empty($username)) {
        // Updated function call name to match models/usersModel.php
        updateProfile($user_id, $user_name, $user_email, $username);
        
        // Refresh session variables
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_email'] = $user_email;
        $_SESSION['username'] = $username;
    }
}

header("Location: ../views/profile/profile.php");
exit();
?>