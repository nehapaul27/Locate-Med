<?php
require "db_connect.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location:../frontend/login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $quantity = (int) $_POST['quantity'];
    $price    = (float) $_POST['price'];

    if ($name === "" || $quantity < 0) {
        die("Please enter a valid medicine name and quantity");
    }

    // Get pharmacy_id for this session (looked up once, then cached)
    if (!isset($_SESSION['pharmacy_id'])) {
        $user_id = (int) $_SESSION['user_id'];
        $pharmacy_result = mysqli_query($conn, "SELECT id FROM pharmacies WHERE user_id = '$user_id' LIMIT 1");
        $pharmacy_row = mysqli_fetch_assoc($pharmacy_result);

        if (!$pharmacy_row) {
            die("No pharmacy found for this account");
        }
        $_SESSION['pharmacy_id'] = $pharmacy_row['id'];
    }
    $pharmacy_id = $_SESSION['pharmacy_id'];

    // Check whether this medicine already exists for this pharmacy
    $check_sql = "SELECT id, quantity FROM medicines WHERE pharmacy_id = '$pharmacy_id' AND LOWER(name) = LOWER('$name') LIMIT 1";
    $check_result = mysqli_query($conn, $check_sql);
    $existing = mysqli_fetch_assoc($check_result);

    if ($existing) {
        // Already have it — add to the existing quantity instead of duplicating the row
        $new_quantity = $existing['quantity'] + $quantity;
        $sql = "UPDATE medicines SET quantity = '$new_quantity', category = '$category', price = '$price' WHERE id = '{$existing['id']}'";
    } else {
        // New medicine — insert it
        $sql = "INSERT INTO medicines(pharmacy_id, name, category, quantity, price, created_at) VALUES('$pharmacy_id', '$name', '$category', '$quantity', '$price', NOW())";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['medicine_saved'] = true;
        header("location:../frontend/pharmacist-dashboard.php");
        exit();
    } else {
        die("Could not save medicine: " . mysqli_error($conn));
    }
}

mysqli_close($conn);
?>