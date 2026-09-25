<?php
require "db_connect.php";
session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
   $owner_name=$_POST['owner-name'];
   $pharmacy_name=$_POST['pharmacy-name'];
   $email=$_POST['email'];
   $phone=$_POST['phone'];
   $password=$_POST['password'];
   $confirm_password=$_POST['confirm-password'];
   $license=$_POST['license'];
   $address=$_POST['address'];
   $city=$_POST['city'];
   $pincode=$_POST['pincode'];
   $latitude=$_POST['latitude'];
   $longitude=$_POST['longitude'];

   if($password !== $confirm_password){
      die("Passwords do not match");
   }

   $password_hash=password_hash($password, PASSWORD_DEFAULT);

   // Step 1: insert the account into users, role = pharmacist
   $sql1="INSERT INTO users(role,full_name,email,phone,password_hash) VALUES('pharmacist','$owner_name','$email','$phone','$password_hash')";

   if(mysqli_query($conn,$sql1)){
        $user_id=mysqli_insert_id($conn);

        // Step 2: insert the pharmacy details, linked to that user
        $sql2="INSERT INTO pharmacies(user_id,pharmacy_name,license_number,address,city,pincode,latitude,longitude) VALUES('$user_id','$pharmacy_name','$license','$address','$city','$pincode','$latitude','$longitude')";

        if(mysqli_query($conn,$sql2)){
            // Store everything the dashboard needs to display, right after signup
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = 'pharmacist';
            $_SESSION['owner_name'] = $owner_name;
            $_SESSION['pharmacy_name'] = $pharmacy_name;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['license'] = $license;
            $_SESSION['address'] = $address;
            $_SESSION['city'] = $city;
            $_SESSION['pincode'] = $pincode;

            header("location:../frontend/pharmacist-dashboard.php");
            exit();
        }
        else{
            die("pharmacy not registered " . mysqli_error($conn));
        }
   }
   else{
      die("not registered "  .  mysqli_error($conn));
   }
}

mysqli_close($conn);
?>