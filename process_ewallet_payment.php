<?php
session_start();
include('db_connect.php');

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $booking_id = $_POST['booking_id'] ?? 0;
    $field_type = $_POST['field_type'] ?? '';
    $ewallet_type = $_POST['ewallet_type'] ?? '';
    $payment_reference = $_POST['payment_reference'] ?? '';
    
    // Validasi data
    if (empty($booking_id) || empty($field_type) || empty($ewallet_type)) {
        $_SESSION['payment_error'] = "Data pembayaran tidak lengkap";
        header("Location: payment_ewallet.php?id=$booking_id&type=$field_type");
        exit();
    }
    
    // Tentukan tabel booking berdasarkan jenis lapangan
    $booking_table = '';
    switch ($field_type) {
        case 'futsal':
            $booking_table = 'futsal_booking';
            break;
        case 'badminton':
            $booking_table = 'badminton_booking';
            break;
        case 'tennis':
            $booking_table = 'tennis_booking';
            break;
        default:
            $_SESSION['payment_error'] = "Jenis lapangan tidak valid";
            header("Location: payment_ewallet.php?id=$booking_id&type=$field_type");
            exit();
    }
    
    // Cek apakah data booking ada
    $check_query = "SELECT * FROM $booking_table WHERE id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $check_result = $stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $_SESSION['payment_error'] = "Data booking tidak ditemukan";
        header("Location: booking_history.php");
        exit();
    }
    
    // Pada implementasi nyata, di sini akan dilakukan verifikasi dengan payment gateway
    // untuk memastikan pembayaran sudah diterima
    
    // Update status booking dan tambahkan data pembayaran
    $payment_date = date('Y-m-d H:i:s');
    $update_query = "UPDATE $booking_table SET 
                     status = 'pending_confirmation', 
                     payment_method = ?, 
                     payment_date = ?, 
                     payment_reference = ? 
                     WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $payment_method = "e-wallet ($ewallet_type)";
    $stmt->bind_param("sssi", $payment_method, $payment_date, $payment_reference, $booking_id);
    
    if ($stmt->execute()) {
        $_SESSION['payment_success'] = "Pembayaran berhasil dikonfirmasi. Status booking Anda akan diperbarui setelah diverifikasi oleh admin.";
        header("Location: booking_history.php");
        exit();
    } else {
        $_SESSION['payment_error'] = "Gagal menyimpan data pembayaran: " . $stmt->error;
        header("Location: payment_ewallet.php?id=$booking_id&type=$field_type");
        exit();
    }
} else {
    // Jika bukan method POST, redirect ke halaman booking history
    header("Location: booking_history.php");
    exit();
}
?> 