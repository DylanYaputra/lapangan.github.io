<?php
session_start();
include('db_connect.php');

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data booking dari database
if (!isset($_GET['id']) || !isset($_GET['type'])) {
    header("Location: booking_calendar.php");
    exit();
}

$booking_id = $_GET['id'];
$field_type = $_GET['type'];

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
        header("Location: booking_calendar.php");
        exit();
}

// Ambil data booking
$query = "SELECT b.*, f.price_per_hour FROM $booking_table b 
          JOIN fields f ON b.field = f.field_name 
          WHERE b.id = ? AND f.field_type = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $booking_id, $field_type);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: booking_calendar.php");
    exit();
}

$booking_data = $result->fetch_assoc();
$price = $booking_data['price_per_hour'];

// Update status booking untuk metode pembayaran cash
$update_query = "UPDATE $booking_table SET 
                status = 'pending_payment', 
                payment_method = 'cash' 
                WHERE id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Tunai - SportField</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
        }
        .payment-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .payment-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            margin-bottom: 20px;
        }
        .success-checkmark .check-icon {
            width: 80px;
            height: 80px;
            position: relative;
            border-radius: 50%;
            box-sizing: content-box;
            border: 4px solid #4CAF50;
        }
        .success-checkmark .check-icon::before {
            top: 3px;
            left: -2px;
            width: 30px;
            transform-origin: 100% 50%;
            border-radius: 100px 0 0 100px;
        }
        .success-checkmark .check-icon::after {
            top: 0;
            left: 30px;
            width: 60px;
            transform-origin: 0 50%;
            border-radius: 0 100px 100px 0;
            animation: rotate-circle 4.25s ease-in;
        }
        .success-checkmark .check-icon::before, .success-checkmark .check-icon::after {
            content: '';
            height: 100px;
            position: absolute;
            background: #FFFFFF;
            transform: rotate(-45deg);
        }
        .success-checkmark .check-icon .icon-line {
            height: 5px;
            background-color: #4CAF50;
            display: block;
            border-radius: 2px;
            position: absolute;
            z-index: 10;
        }
        .success-checkmark .check-icon .icon-line.line-tip {
            top: 46px;
            left: 14px;
            width: 25px;
            transform: rotate(45deg);
            animation: icon-line-tip 0.75s;
        }
        .success-checkmark .check-icon .icon-line.line-long {
            top: 38px;
            right: 8px;
            width: 47px;
            transform: rotate(-45deg);
            animation: icon-line-long 0.75s;
        }
        .success-checkmark .check-icon .icon-circle {
            top: -4px;
            left: -4px;
            z-index: 10;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            position: absolute;
            box-sizing: content-box;
            border: 4px solid rgba(76, 175, 80, .5);
        }
        .success-checkmark .check-icon .icon-fix {
            top: 8px;
            width: 5px;
            left: 26px;
            z-index: 1;
            height: 85px;
            position: absolute;
            transform: rotate(-45deg);
            background-color: #FFFFFF;
        }
        @keyframes rotate-circle {
            0% {
                transform: rotate(-45deg);
            }
            5% {
                transform: rotate(-45deg);
            }
            12% {
                transform: rotate(-405deg);
            }
            100% {
                transform: rotate(-405deg);
            }
        }
        @keyframes icon-line-tip {
            0% {
                width: 0;
                left: 1px;
                top: 19px;
            }
            54% {
                width: 0;
                left: 1px;
                top: 19px;
            }
            70% {
                width: 50px;
                left: -8px;
                top: 37px;
            }
            84% {
                width: 17px;
                left: 21px;
                top: 48px;
            }
            100% {
                width: 25px;
                left: 14px;
                top: 45px;
            }
        }
        @keyframes icon-line-long {
            0% {
                width: 0;
                right: 46px;
                top: 54px;
            }
            65% {
                width: 0;
                right: 46px;
                top: 54px;
            }
            84% {
                width: 55px;
                right: 0px;
                top: 35px;
            }
            100% {
                width: 47px;
                right: 8px;
                top: 38px;
            }
        }
        .booking-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .btn-download {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="payment-header text-center">
                <h3><i class="fas fa-money-bill-wave mr-2"></i> Pembayaran Tunai</h3>
            </div>
            
            <div class="text-center mb-4">
                <div class="success-checkmark">
                    <div class="check-icon">
                        <span class="icon-line line-tip"></span>
                        <span class="icon-line line-long"></span>
                        <div class="icon-circle"></div>
                        <div class="icon-fix"></div>
                    </div>
                </div>
                <h4 class="text-success">Booking Berhasil!</h4>
                <p>Pesanan Anda telah berhasil dibuat. Silakan bayar di tempat saat Anda tiba.</p>
            </div>
            
            <div class="booking-info">
                <h5>Detail Pesanan:</h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td>ID Booking</td>
                                <td>: #<?php echo $booking_id; ?></td>
                            </tr>
                            <tr>
                                <td>Lapangan</td>
                                <td>: <?php echo $booking_data['field']; ?></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td>: <?php echo date('d-m-Y', strtotime($booking_data['booking_date'])); ?></td>
                            </tr>
                            <tr>
                                <td>Waktu</td>
                                <td>: <?php echo date('H:i', strtotime($booking_data['booking_time'])); ?> WIB</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Total Pembayaran</h5>
                                <h3 class="text-primary">Rp. <?php echo number_format($price, 0, ',', '.'); ?></h3>
                                <p class="card-text"><small class="text-muted">Silakan bayar pada petugas di lokasi</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle mr-2"></i> Informasi Penting:</h5>
                <ol>
                    <li>Harap datang 15 menit sebelum waktu booking</li>
                    <li>Pembayaran dilakukan di lokasi sebelum mulai bermain</li>
                    <li>Bawa bukti booking ini (dapat dicetak atau ditunjukkan dari smartphone)</li>
                    <li>Jika ada pertanyaan, silakan hubungi customer service kami di (021) 123-456</li>
                </ol>
            </div>
            
            <div class="action-buttons">
                <button type="button" class="btn btn-outline-primary btn-download" onclick="window.print()">
                    <i class="fas fa-print mr-2"></i> Cetak Bukti Booking
                </button>
                <a href="booking_history.php" class="btn btn-primary">
                    <i class="fas fa-history mr-2"></i> Lihat Riwayat Booking
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 