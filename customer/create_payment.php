<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../db.php";

if (!isset($_GET['booking_id'])) {
    die("Invalid booking ID");
}

$booking_id = intval($_GET['booking_id']);
$result = mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $booking_id");
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Booking not found");
}

// Amount in centavos
$amount = $booking['total_price'] * 100;

$secret = getenv('PAYMONGO_SECRET');
$data = [
    "data" => [
        "attributes" => [
            "amount" => (int)$amount,
            "description" => "UrbanDrive Booking #$booking_id",
            "remarks" => "Car Rental Payment",
            "redirect" => [
                "success" => "http://localhost/php_kapoy/customer/payment_success.php?booking_id=$booking_id",
                "failed" => "http://localhost/php_kapoy/customer/payment_failed.php?booking_id=$booking_id"
            ]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/links");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($secret . ":")
]);
    


$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}

curl_close($ch);

$result = json_decode($response, true);

if(isset($result['errors'])) {
    echo "<h2>PayMongo API Error:</h2>";
    echo "<pre>";
    print_r($result['errors']);
    echo "</pre>";
    exit;
}

if(!isset($result['data']['attributes']['checkout_url'])) {
    echo "<h2>Failed to create payment link</h2>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    exit;
}

// Redirect to checkout page
$checkoutUrl = $result['data']['attributes']['checkout_url'];
header("Location: " . $checkoutUrl);
exit();