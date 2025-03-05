<?php
session_start();

// Pastikan ada sesi total harga
if (!isset($_SESSION['total_harga'])) {
    header("Location: Home.php");
    exit();
}

$nomorBooking = $_SESSION['nomor_booking'];
$totalHarga = $_SESSION['total_harga'];
$detailBooking = $_SESSION['detail_booking'];

// Proses upload bukti pembayaran
$uploadStatus = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['bukti_pembayaran'])) {
    $targetDir = "uploads/";
    $bookingsDir = "bookings/";
    
    $namaFile = $nomorBooking . '_bukti' . '.' . pathinfo($_FILES["bukti_pembayaran"]["name"], PATHINFO_EXTENSION);
    $targetFilePath = $targetDir . $namaFile;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
    // Validasi tipe file
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $targetFilePath)) {
            // Append payment proof path to booking details
            file_put_contents($bookingsDir . $nomorBooking . '.txt', 
                file_get_contents($bookingsDir . $nomorBooking . '.txt') . 
                "\nBukti Pembayaran: $targetFilePath"
            );
            
            // Redirect to Home.php with success status
            header("Location: Home.php?upload=success");
            exit();
        } else {
            // Redirect with error
            header("Location: Home.php?upload=error");
            exit();
        }
    } else {
        // Redirect with file type error
        header("Location: Home.php?upload=error_type");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="text-center">Konfirmasi Pembayaran</h3>
                    </div>
                    <div class="card-body">
                        <h4>Detail Booking</h4>
                        <pre><?php echo $detailBooking; ?></pre>
                        
                        <h4>Total yang Harus Dibayar</h4>
                        <p class="h3 text-success">Rp <?php echo number_format($totalHarga, 0, ',', '.'); ?></p>
                        
                        <div class="alert alert-info">
                            <strong>Nomor Booking Anda:</strong> 
                            <span class="h4 text-primary"><?php echo $nomorBooking; ?></span>
                            <p class="small">Simpan nomor booking ini untuk referensi</p>
                        </div>
                        
                        <form id="uploadForm" action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Upload Bukti Pembayaran</label>
                                <input type="file" name="bukti_pembayaran" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">Upload Bukti Pembayaran</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</body>
</html>