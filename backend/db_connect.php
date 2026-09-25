<?php
$server="localhost";
$username="root";
$password="";
$database="locatemed_db";

$conn=mysqli_connect($server,$username,$password,$database);

if(!$conn){
  die("connection error" . mysqli_connect_error());
}
?>