<?php
session_start();
include '../config/db.php';
$email = $_POST['email'];
$password = $_POST['password'];


$q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($q);


if ($user && password_verify($password, $user['password'])) {
    $_SESSION['id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    header('Location: ../index.php');
} else {
    echo 'Login gagal';
}
?>
