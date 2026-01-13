<?php
session_start();
include '../config/db.php';

// Pastikan role HR dan cabang sudah dipilih
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'hr' || !isset($_SESSION['cabang_id'])) {
    header("Location: dashboard.php");
    exit;
}

$cabang_id = $_SESSION['cabang_id'];
$cabang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cabang WHERE id='$cabang_id'"));

// Ambil data absensi hari ini untuk cabang ini
$tanggal_hari_ini = date('Y-m-d');
$data = mysqli_query($conn, "
    SELECT
        users.nama,
        absensi.jam_masuk,
        absensi.jam_pulang,
        absensi.total_lembur,
        absensi.shift
    FROM absensi
    JOIN karyawan ON karyawan.id = absensi.karyawan_id
    JOIN users ON users.id = karyawan.user_id
    WHERE absensi.tanggal = '$tanggal_hari_ini'
    AND karyawan.cabang_id = '$cabang_id'
    ORDER BY absensi.jam_masuk DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Absen - <?= $cabang['nama_cabang'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FFC0CB 0%, #FFFFFF 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 100px;
            height: auto;
        }
        h2 {
            color: #FF69B4;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #FF1493 0%, #FF69B4 100%);
            color: white;
        }
        table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        th {
            background: #FF69B4;
            color: white;
        }
        .status-masuk {
            color: #28a745;
            font-weight: bold;
        }
        .status-belum {
            color: #ffc107;
            font-weight: bold;
        }
        .refresh-info {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="text-center mb-4 logo">
                <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
            </div>
            <h2>Monitoring Absen - <?= $cabang['nama_cabang'] ?></h2>
            <div class="refresh-info">
                Data diperbarui otomatis setiap 30 detik • Tanggal: <?= date('d/m/Y') ?>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Overtime (Jam)</th>
                            <th>Shift</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                <tbody id="absensi-data">
                    <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?= $d['nama'] ?></td>
                            <td><?= $d['jam_masuk'] ? date('H:i:s', strtotime($d['jam_masuk'])) : '-' ?></td>
                            <td><?= $d['jam_pulang'] ? date('H:i:s', strtotime($d['jam_pulang'])) : '-' ?></td>
                            <td><?= $d['total_lembur'] ? $d['total_lembur'] : '-' ?></td>
                            <td><?= $d['shift'] ?: '-' ?></td>
                            <td>
                                <?php if ($d['jam_masuk'] && $d['jam_pulang']) { ?>
                                    <span class="status-masuk">✓ Lengkap</span>
                                <?php } elseif ($d['jam_masuk']) { ?>
                                    <span class="status-belum">○ Belum Pulang</span>
                                <?php } else { ?>
                                    <span class="status-belum">✗ Belum Absen</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="text-center mt-3">
                <button onclick="refreshData()" class="btn btn-custom me-2">Refresh Sekarang</button>
                <a href="branch_dashboard.php" class="btn btn-custom">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function refreshData() {
            location.reload();
        }

        // Auto refresh setiap 30 detik
        setInterval(function() {
            refreshData();
        }, 30000);
    </script>
</body>

</html>
