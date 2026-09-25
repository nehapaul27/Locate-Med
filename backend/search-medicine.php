<?php

// 1. Get customer's latitude, longitude and medicine
// 2. Find pharmacies having that medicine
// 3. Calculate distance for each pharmacy
// 4. Sort by distance
// 5. Return the results

require "db_connect.php";
session_start();

header("Content-Type: application/json");


// ==========================================
// 1. CHECK LOGIN
// ==========================================

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit();
}


// ==========================================
// 2. DATABASE CONNECTION
// ==========================================

// ==========================================
// 3. GET DATA FROM CUSTOMER
// ==========================================

$data = json_decode(
    file_get_contents("php://input"),
    true
);


$medicine = trim($data["medicine"] ?? "");

$userLatitude = $data["latitude"] ?? null;

$userLongitude = $data["longitude"] ?? null;


// ==========================================
// 4. CHECK DATA
// ==========================================

if (
    empty($medicine) ||
    $userLatitude === null ||
    $userLongitude === null
) {

    echo json_encode([
        "success" => false,
        "message" => "Medicine and location are required."
    ]);

    exit();
}


$userLatitude = floatval($userLatitude);

$userLongitude = floatval($userLongitude);


// ==========================================
// 5. FIND PHARMACIES WITH THIS MEDICINE
// ==========================================

$sql = "
    SELECT
        p.id,
        p.pharmacy_name AS name,
        p.address,
        p.city,
        p.pincode,
        p.latitude,
        p.longitude,

        m.name AS medicine_name,
        m.quantity,
        m.price

    FROM pharmacies p

    JOIN medicines m
        ON p.id = m.pharmacy_id

    WHERE
        m.name LIKE ?

        AND m.quantity > 0

        AND p.latitude IS NOT NULL

        AND p.longitude IS NOT NULL
";


// ==========================================
// 6. PREPARE SEARCH
// ==========================================

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare the medicine search."
    ]);
    exit();
}


$searchMedicine = "%" . $medicine . "%";


$stmt->bind_param(
    "s",
    $searchMedicine
);


$stmt->execute();


$result = $stmt->get_result();


// ==========================================
// 7. CREATE EMPTY ARRAY
// ==========================================

$pharmacies = [];


// ==========================================
// 8. CALCULATE DISTANCE
// ==========================================

while ($row = $result->fetch_assoc()) {


    // Pharmacy coordinates

    $pharmacyLatitude =
        floatval($row["latitude"]);

    $pharmacyLongitude =
        floatval($row["longitude"]);


    // ======================================
    // HAVERSINE FORMULA
    // ======================================

    $earthRadius = 6371;


    $latDifference = deg2rad(
        $pharmacyLatitude - $userLatitude
    );


    $lonDifference = deg2rad(
        $pharmacyLongitude - $userLongitude
    );


    $a =
        sin($latDifference / 2) *
        sin($latDifference / 2)

        +

        cos(deg2rad($userLatitude)) *
        cos(deg2rad($pharmacyLatitude)) *
        sin($lonDifference / 2) *
        sin($lonDifference / 2);


    $c =
        2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );


    $distance =
        $earthRadius * $c;


    // ======================================
    // STORE PHARMACY + DISTANCE
    // ======================================

    $pharmacies[] = [

        "id" =>
            $row["id"],

        "name" =>
            $row["name"],

        "address" =>
            $row["address"],

        "city" =>
            $row["city"],

        "pincode" =>
            $row["pincode"],

        "medicine" =>
            $row["medicine_name"],

        "quantity" =>
            $row["quantity"],

        "price" =>
            $row["price"],

        "distance" =>
            round($distance, 2)

    ];
}


// ==========================================
// 9. SORT BY DISTANCE
// ==========================================

usort(
    $pharmacies,
    function ($a, $b) {

        return $a["distance"]
             <=> $b["distance"];

    }
);


// ==========================================
// 10. CHECK IF NO PHARMACY FOUND
// ==========================================

if (empty($pharmacies)) {

    echo json_encode([

        "success" => true,

        "message" =>
            "No pharmacy found with this medicine.",

        "results" => []

    ]);

    exit();
}


// ==========================================
// 11. SEND RESULTS TO CUSTOMER
// ==========================================

echo json_encode([

    "success" => true,

    "message" =>
        "Pharmacies found successfully.",

    "results" =>
        $pharmacies

]);


$stmt->close();

$conn->close();

?>