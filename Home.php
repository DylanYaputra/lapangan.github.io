<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pemesanan Lapangan Badminton</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color:rgb(23, 97, 61);
            --secondary-color: #f0f0f0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
        }

        .booking-section {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .time-slot {
            background-color: var(--secondary-color);
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .time-slot:hover, .time-slot.active {
            background-color: var(--primary-color);
            color: white;
        }

        .feature-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body>
    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Booking Lapangan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#booking">Booking</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fasilitas">Fasilitas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#harga">Harga</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Beranda -->
    <header class="container-fluid bg-success text-white text-center py-5" id="beranda">
        <div class="container">
            <h1 class="display-4">Sewa Lapangan Murah Meriah</h1>
            <p class="lead">Pesan Lapangan Dengan Mudah dan Cepat</p>
            <a href="#booking" class="btn btn-light mt-3">Pesan Sekarang</a>
        </div>
    </header>

    <!-- Formulir Booking -->
    <section class="container my-5" id="booking">
        <div class="row justify-content-center">
            <div class="col-md-8 booking-section">
                <h2 class="text-center mb-4">Formulir Pemesanan</h2>
                <form id="bookingForm" action="proses-booking.php" method="post">
                    <div class="row g-3">
                      <!-- Tanggal -->
                      <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>

                        <!-- Jenis Tiket -->
                        <div class="col-md-6">
                            <label class="form-label">Jenis Tiket</label>
                            <select name="jenis_tiket" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option>Badminton</option>
                                <option>Tennis</option>
                                <option>Futsal</option>
                            </select>
                        </div>

                        <!-- Waktu -->
                        <div class="col-12">
                            <label class="form-label">Rentang Waktu</label>
                            <div class="d-flex gap-2">
                                <div class="time-slot" data-value="08:00-18:00">08:00-18:00</div>
                                <div class="time-slot active" data-value="18:00-23:00">18:00-23:00</div>
                                <input type="hidden" name="rentang_waktu" id="selected-time-slot" value="18:00-23:00">
                            </div>
                        </div>

                        <!-- Jumlah Lapangan -->
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Lapangan</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" id="decreaseBtn">-</button>
                                <input type="text" class="form-control text-center" id="courtCount" name="jumlah_lapangan" value="1" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="increaseBtn">+</button>
                            </div>
                        </div>                        

                        <!-- Jam Main - Updated to include evening time slots -->
                        <div class="col-md-6">
                            <label class="form-label">Jam Main</label>
                            <select name="jam_main" class="form-select" id="jamMainSelect" required>
                                <!-- Daytime slots -->
                                <optgroup label="Siang" id="siangSlots" style="display:none;">
                                    <option>08:00-09:00</option>
                                    <option>09:00-10:00</option>
                                    <option>10:00-11:00</option>
                                    <option>11:00-12:00</option>
                                    <option>12:00-13:00</option>
                                    <option>13:00-14:00</option>
                                    <option>14:00-15:00</option>
                                    <option>15:00-16:00</option>
                                    <option>16:00-17:00</option>
                                    <option>17:00-18:00</option>
                                </optgroup>
                                
                                <!-- Evening slots -->
                                <optgroup label="Malam" id="malamSlots">
                                    <option>18:00-19:00</option>
                                    <option>19:00-20:00</option>
                                    <option>20:00-21:00</option>
                                    <option>21:00-22:00</option>
                                    <option>22:00-23:00</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Catatan Tambahan -->
                        <div class="col-12">
                            <label class="form-label">Catatan Tambahan</label>
                            <textarea name="catatan" class="form-control" rows="4" placeholder="Tulis catatan tambahan"></textarea>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100">Pesan Sekarang</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Fasilitas -->
    <section class="container my-5" id="fasilitas">
        <h2 class="text-center mb-4">Fasilitas Kami</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-volleyball-ball fa-3x mb-3 text-success"></i>
                    <h4>Lapangan Berkualitas</h4>
                    <p>Lapangan standar internasional</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-shower fa-3x mb-3 text-success"></i>
                    <h4>Fasilitas Lengkap</h4>
                    <p>Ruang ganti dan area istirahat</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <i class="fas fa-parking fa-3x mb-3 text-success"></i>
                    <h4>Parkir Luas</h4>
                    <p>Area parkir yang aman</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Harga -->
    <section class="container my-5" id="harga">
        <h2 class="text-center mb-4">Daftar Harga</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Harga per Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Pagi (08:00 - 12:00)</td>
                            <td>Rp 50.000</td>
                        </tr>
                        <tr>
                            <td>Siang (13:00 - 17:00)</td>
                            <td>Rp 60.000</td>
                        </tr>
                        <tr>
                            <td>Malam (17:00 - 22:00)</td>
                            <td>Rp 75.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p>&copy; 2024 Booking Lapangan Badminton</p>
            <div class="social-links">
                <a href="#" class="text-white mx-2"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const decreaseBtn = document.getElementById('decreaseBtn');
            const increaseBtn = document.getElementById('increaseBtn');
            const courtCount = document.getElementById('courtCount');
            const timeSlots = document.querySelectorAll('.time-slot');
            const timeSlotInput = document.getElementById('selected-time-slot');
            const jamMainSelect = document.getElementById('jamMainSelect');
            const siangSlots = document.getElementById('siangSlots');
            const malamSlots = document.getElementById('malamSlots');

            // Check for successful upload from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const uploadStatus = urlParams.get('upload');
            
            if (uploadStatus === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil',
                    text: 'Bukti pembayaran telah diupload!',
                    confirmButtonText: 'OK'
                });
            }

            // Court count logic
            decreaseBtn.addEventListener('click', () => {
                let count = parseInt(courtCount.value);
                if (count > 1) {
                    courtCount.value = count - 1;
                }
            });

            increaseBtn.addEventListener('click', () => {
                let count = parseInt(courtCount.value);
                courtCount.value = count + 1;
            });

            // Time slot selection with input field
            timeSlots.forEach(slot => {
                slot.addEventListener('click', () => {
                    // Remove active class from all slots
                    timeSlots.forEach(s => s.classList.remove('active'));
                    
                    // Add active class to clicked slot
                    slot.classList.add('active');
                    
                    // Update hidden input value
                    timeSlotInput.value = slot.getAttribute('data-value');
                    
                    // Toggle time slot options
                    if (slot.getAttribute('data-value') === '08:00-18:00') {
                        siangSlots.style.display = 'block';
                        malamSlots.style.display = 'none';
                        // Set default to daytime first option
                        jamMainSelect.value = '08:00-09:00';
                    } else {
                        siangSlots.style.display = 'none';
                        malamSlots.style.display = 'block';
                        // Set default to evening first option
                        jamMainSelect.value = '18:00-19:00';
                    }
                });
            });

            // Set initial state for evening time slots
            siangSlots.style.display = 'none';
            malamSlots.style.display = 'block';
        });
    </script>
</body>
</html>