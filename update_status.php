<?php
session_start();
include('db_connect.php');

// Set header untuk output sebagai JSON
header('Content-Type: application/json');

// Coba update status di tabel futsal_booking
$futsal_result = $conn->query("UPDATE futsal_booking SET status='pending_confirmation' WHERE status='booked'");
$futsal_count = $conn->affected_rows;

// Coba update status di tabel badminton_booking
$badminton_result = $conn->query("UPDATE badminton_booking SET status='pending_confirmation' WHERE status='booked'");
$badminton_count = $conn->affected_rows;

// Coba update status di tabel tennis_booking
$tennis_result = $conn->query("UPDATE tennis_booking SET status='pending_confirmation' WHERE status='booked'");
$tennis_count = $conn->affected_rows;

// Siapkan response
$response = [
    'success' => true,
    'message' => 'Status berhasil diupdate',
    'updated' => [
        'futsal' => $futsal_count,
        'badminton' => $badminton_count,
        'tennis' => $tennis_count
    ],
    'total' => $futsal_count + $badminton_count + $tennis_count
];

// Output response sebagai JSON
echo json_encode($response);
?> 