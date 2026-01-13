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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= $cabang['nama_cabang'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FFC0CB 0%, #FFFFFF 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 20px;
            text-align: center;
        }
        .logo img {
            max-width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        h2 {
            color: #FF69B4;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .btn-custom {
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            color: white;
            font-weight: bold;
            font-size: 16px;
            margin: 10px;
            transition: transform 0.2s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #FF1493 0%, #FF69B4 100%);
            color: white;
        }
        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: bold;
        }
        .btn-secondary-custom:hover {
            background: #5a6268;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
            </div>
            <h2><?= $cabang['nama_cabang'] ?></h2>
            <div>
                <a href="data_karyawan.php" class="btn btn-custom">Data Karyawan</a>
                <a href="monitoring_absen.php" class="btn btn-custom">Monitoring Absen</a>
                <a href="download_absensi.php" class="btn btn-custom">Download Absensi</a>
            </div>
            <div class="mt-3">
                <a href="dashboard.php?reset_cabang=1" class="btn btn-secondary-custom">Pilih Cabang Lain</a>
                <a href="../auth/logout.php" onclick="return confirm('Yakin ingin logout?')" class="btn btn-secondary-custom ms-2">Logout</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
