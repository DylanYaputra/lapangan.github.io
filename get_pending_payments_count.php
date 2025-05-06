<?php
session_start();
include('db_connect.php');

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized', 'count' => 0]);
    exit();
}

// Hitung jumlah pembayaran yang menunggu verifikasi
$total_pending = 0;

// Hitung dari tabel futsal_booking
$futsal_query = "SELECT COUNT(*) as count FROM futsal_booking WHERE status = 'pending_confirmation'";
$futsal_result = $conn->query($futsal_query);
if ($futsal_result && $futsal_result->num_rows > 0) {
    $row = $futsal_result->fetch_assoc();
    $total_pending += $row['count'];
}

// Hitung dari tabel badminton_booking
$badminton_query = "SELECT COUNT(*) as count FROM badminton_booking WHERE status = 'pending_confirmation'";
$badminton_result = $conn->query($badminton_query);
if ($badminton_result && $badminton_result->num_rows > 0) {
    $row = $badminton_result->fetch_assoc();
    $total_pending += $row['count'];
}

// Hitung dari tabel tennis_booking
$tennis_query = "SELECT COUNT(*) as count FROM tennis_booking WHERE status = 'pending_confirmation'";
$tennis_result = $conn->query($tennis_query);
if ($tennis_result && $tennis_result->num_rows > 0) {
    $row = $tennis_result->fetch_assoc();
    $total_pending += $row['count'];
}

// Return hasil dalam format JSON
header('Content-Type: application/json');
echo json_encode(['count' => $total_pending]);
?> 