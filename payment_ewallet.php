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
$payment_deadline = date('Y-m-d H:i:s', strtotime('+24 hours'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran E-Wallet - SportField</title>
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
        .ewallet-options {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px dashed #ccc;
        }
        .ewallet-option {
            cursor: pointer;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .ewallet-option:hover, .ewallet-option.selected {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .ewallet-option.selected {
            background-color: #f0f7ff;
        }
        .ewallet-logo {
            height: 40px;
            object-fit: contain;
        }
        .alert-warning {
            font-size: 14px;
        }
        .countdown {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        #qrCodeSection {
            display: none;
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 1px dashed #ccc;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="payment-header text-center">
                <h3><i class="fas fa-mobile-alt mr-2"></i> Instruksi Pembayaran</h3>
                <p class="mb-0">E-Wallet</p>
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-clock mr-2"></i> Harap selesaikan pembayaran sebelum:
                <div class="countdown" id="countdown"><?php echo $payment_deadline; ?></div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Detail Pesanan:</h5>
                    <table class="table table-sm">
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
                    <h5>Total Pembayaran:</h5>
                    <div class="h3 text-primary">Rp. <?php echo number_format($price, 0, ',', '.'); ?></div>
                </div>
            </div>
            
            <div class="ewallet-options">
                <h5><i class="fas fa-wallet mr-2"></i> Pilih E-Wallet:</h5>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="ewallet-option" data-type="gopay" onclick="selectEwallet(this)">
                            <div class="d-flex align-items-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Gopay_logo.svg/200px-Gopay_logo.svg.png" alt="GoPay" class="ewallet-logo mr-3">
                                <div>
                                    <h6 class="mb-0">GoPay</h6>
                                    <small class="text-muted">Pembayaran melalui aplikasi Gojek</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ewallet-option" data-type="ovo" onclick="selectEwallet(this)">
                            <div class="d-flex align-items-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Logo_ovo_purple.svg/200px-Logo_ovo_purple.svg.png" alt="OVO" class="ewallet-logo mr-3">
                                <div>
                                    <h6 class="mb-0">OVO</h6>
                                    <small class="text-muted">Pembayaran melalui aplikasi OVO</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ewallet-option" data-type="dana" onclick="selectEwallet(this)">
                            <div class="d-flex align-items-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Logo_dana_blue.svg/200px-Logo_dana_blue.svg.png" alt="DANA" class="ewallet-logo mr-3">
                                <div>
                                    <h6 class="mb-0">DANA</h6>
                                    <small class="text-muted">Pembayaran melalui aplikasi DANA</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="qrCodeSection">
                <h5 class="mb-4">Pindai QR Code berikut:</h5>
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://example.com/pay-<?php echo $booking_id; ?>" alt="QR Code" width="170">
                </div>
                <div class="mt-3">
                    <p class="mb-0">Atau buka aplikasi <span id="walletName">E-Wallet</span> di handphone Anda</p>
                    <p>Nominal: <strong>Rp <?php echo number_format($price, 0, ',', '.'); ?></strong></p>
                </div>
                <div class="alert alert-info mt-3">
                    <small><i class="fas fa-info-circle mr-1"></i> Setelah melakukan pembayaran, klik tombol "Konfirmasi Pembayaran" di bawah</small>
                </div>
            </div>
            
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle mr-2"></i> Petunjuk Pembayaran:</h5>
                <ol>
                    <li>Pilih e-wallet yang ingin digunakan</li>
                    <li>Scan QR Code menggunakan aplikasi e-wallet Anda</li>
                    <li>Masukkan nominal sesuai dengan total pembayaran</li>
                    <li>Selesaikan pembayaran sesuai instruksi pada aplikasi</li>
                    <li>Klik "Konfirmasi Pembayaran" setelah berhasil melakukan pembayaran</li>
                </ol>
            </div>
            
            <div class="action-buttons">
                <a href="booking_history.php" class="btn btn-outline-secondary"><i class="fas fa-history mr-2"></i> Lihat Riwayat Booking</a>
                <button type="button" id="payButton" class="btn btn-primary" onclick="showConfirmModal()" disabled><i class="fas fa-check-circle mr-2"></i> Konfirmasi Pembayaran</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Konfirmasi Pembayaran -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="process_ewallet_payment.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                        <input type="hidden" name="field_type" value="<?php echo $field_type; ?>">
                        <input type="hidden" id="ewallet_type" name="ewallet_type" value="">
                        
                        <p>Apakah Anda sudah melakukan pembayaran melalui <span id="modalWalletName">e-wallet</span>?</p>
                        
                        <div class="form-group">
                            <label for="payment_reference">ID Referensi/Transaksi</label>
                            <input type="text" class="form-control" id="payment_reference" name="payment_reference" placeholder="Masukkan ID transaksi (opsional)">
                            <small class="form-text text-muted">ID transaksi dapat ditemukan pada riwayat transaksi di aplikasi e-wallet Anda</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Script untuk countdown timer
        const deadlineStr = "<?php echo $payment_deadline; ?>";
        const deadline = new Date(deadlineStr).getTime();
        
        const countdownTimer = setInterval(function() {
            const now = new Date().getTime();
            const distance = deadline - now;
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById("countdown").innerHTML = hours + ":" + minutes + ":" + seconds;
            
            if (distance < 0) {
                clearInterval(countdownTimer);
                document.getElementById("countdown").innerHTML = "WAKTU HABIS";
            }
        }, 1000);
        
        // Function untuk memilih e-wallet
        function selectEwallet(element) {
            // Reset semua pilihan
            document.querySelectorAll('.ewallet-option').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Pilih e-wallet yang diklik
            element.classList.add('selected');
            
            // Tampilkan QR code
            document.getElementById('qrCodeSection').style.display = 'block';
            
            // Enable tombol konfirmasi
            document.getElementById('payButton').disabled = false;
            
            // Set tipe e-wallet
            const ewalletType = element.getAttribute('data-type');
            document.getElementById('ewallet_type').value = ewalletType;
            
            // Update nama e-wallet
            let walletName = 'E-Wallet';
            if (ewalletType === 'gopay') walletName = 'GoPay';
            else if (ewalletType === 'ovo') walletName = 'OVO';
            else if (ewalletType === 'dana') walletName = 'DANA';
            
            document.getElementById('walletName').textContent = walletName;
            document.getElementById('modalWalletName').textContent = walletName;
        }
        
        // Function untuk menampilkan modal konfirmasi
        function showConfirmModal() {
            $('#confirmModal').modal('show');
        }
    </script>
</body>
</html> 