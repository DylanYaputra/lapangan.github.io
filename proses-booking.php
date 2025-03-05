<?php
session_start();

// Function to generate unique booking number
function generateBookingNumber() {
    $prefix = 'BB';
    $year = date('Y');
    $monthDay = date('md');
    $randomDigits = sprintf('%04d', mt_rand(1, 9999));
    
    return $prefix . $year . '-' . $monthDay . '-' . $randomDigits;
}

// Function to calculate price
function hitungHarga($waktu, $jumlahLapangan) {
    $hargaPerJam = 0;
    
    if ($waktu >= '08:00' && $waktu < '12:00') {
        $hargaPerJam = 50000; // Pagi
    } elseif ($waktu >= '13:00' && $waktu < '17:00') {
        $hargaPerJam = 60000; // Siang
    } elseif ($waktu >= '17:00' && $waktu < '22:00') {
        $hargaPerJam = 75000; // Malam
    }
    
    $totalHarga = $hargaPerJam * $jumlahLapangan;
    
    return $totalHarga;
}

// Process booking form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate unique booking number
    $nomorBooking = generateBookingNumber();
    
    // Collect form data
    $tanggal = $_POST['tanggal'];
    $jenisLayanan = $_POST['jenis_tiket'];
    $rentangWaktu = $_POST['rentang_waktu'];
    $jamMain = $_POST['jam_main'];
    $jumlahLapangan = $_POST['jumlah_lapangan'];
    $catatanTambahan = $_POST['catatan'];
    
    // Calculate total price
    $totalHarga = hitungHarga($jamMain, $jumlahLapangan);
    
    // Prepare booking details
    $detailBooking = "Nomor Booking: $nomorBooking\n";
    $detailBooking .= "Tanggal: $tanggal\n";
    $detailBooking .= "Jenis Layanan: $jenisLayanan\n";
    $detailBooking .= "Rentang Waktu: $rentangWaktu\n";
    $detailBooking .= "Jam Main: $jamMain\n";
    $detailBooking .= "Jumlah Lapangan: $jumlahLapangan\n";
    $detailBooking .= "Catatan: $catatanTambahan\n";
    $detailBooking .= "Total Harga: Rp " . number_format($totalHarga, 0, ',', '.') . "\n";
    
    // Create bookings and uploads directories if not exists
    if (!is_dir('bookings')) {
        mkdir('bookings', 0777, true);
    }
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }
    
    // Save booking details
    $namaFile = 'bookings/' . $nomorBooking . '.txt';
    file_put_contents($namaFile, $detailBooking);
    
    // Store booking information in session for confirmation page
    $_SESSION['nomor_booking'] = $nomorBooking;
    $_SESSION['total_harga'] = $totalHarga;
    $_SESSION['detail_booking'] = $detailBooking;
    
    // Redirect to payment confirmation page
    header("Location: konfirmasi-pembayaran.php");
    exit();
}
?>