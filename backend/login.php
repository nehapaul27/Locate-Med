<?php
require "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $account_type = $_POST['accountType']; // 'user' or 'pharmacist' — from the radio buttons

    // Look up the user by email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Check password matches the stored hash
        if (password_verify($password, $row['password_hash'])) {

            // Check they picked the correct account type for this email
            if ($row['role'] !== $account_type) {
                die("This email is registered as a " . $row['role'] . ", not a " . $account_type . ". Please select the correct account type.");
            }

            // Store common session info
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'user') {
                $_SESSION['fullname'] = $row['full_name'];
                header("location:../frontend/user-dashboard.php");
                exit();

            } elseif ($row['role'] === 'pharmacist') {
                // Pull the linked pharmacy details too
                $pharm_sql = "SELECT * FROM pharmacies WHERE user_id = '" . $row['id'] . "'";
                $pharm_result = mysqli_query($conn, $pharm_sql);
                $pharmacy = mysqli_fetch_assoc($pharm_result);

                $_SESSION['owner_name'] = $row['full_name'];
                $_SESSION['pharmacy_name'] = $pharmacy['pharmacy_name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['phone'] = $row['phone'];
                $_SESSION['license'] = $pharmacy['license_number'];
                $_SESSION['address'] = $pharmacy['address'];
                $_SESSION['city'] = $pharmacy['city'];
                $_SESSION['pincode'] = $pharmacy['pincode'];

                header("location:../frontend/pharmacist-dashboard.php");
                exit();
            }

        } else {
            echo '<script>alert("Incorrect password"); window.history.back();</script>';
            exit();
        }
    } else {
        echo '<script>alert("No account found with that email"); window.history.back();</script>';
        exit();
    }
}

mysqli_close($conn);
?>