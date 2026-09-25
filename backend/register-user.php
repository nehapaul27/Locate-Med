<?php
require "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        die("Passwords do not match");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(role,full_name,email,phone,password_hash) VALUES('user','$fullname','$email','$phone','$password_hash')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['fullname'] = $fullname;
        $_SESSION['role'] = 'user';

        header("location:../frontend/user-dashboard.php");
        exit();
    } else {
        die("not registered " . mysqli_error($conn));
    }
}

mysqli_close($conn);
?>