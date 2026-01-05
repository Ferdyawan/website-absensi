<?php
session_start();
include '../config/db.php';

// pastikan role karyawan
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id'];
$tanggal = date('Y-m-d');
$jam     = date('H:i:s');

// ambil id karyawan
$q = mysqli_query($conn, "SELECT id FROM karyawan WHERE user_id='$user_id'");
$karyawan = mysqli_fetch_assoc($q);
$karyawan_id = $karyawan['id'];

// cek absensi hari ini
$cek = mysqli_query($conn, "
    SELECT * FROM absensi
    WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'
");
$data = mysqli_fetch_assoc($cek);

// ABSEN MASUK
if (isset($_POST['masuk'])) {
    mysqli_query($conn, "
        INSERT INTO absensi (karyawan_id, tanggal, jam_masuk)
        VALUES ('$karyawan_id', '$tanggal', '$jam')
    ");
    header("Location: absensi.php");
}

// ABSEN PULANG
if (isset($_POST['pulang'])) {
    mysqli_query($conn, "
        UPDATE absensi
        SET jam_pulang='$jam'
        WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'
    ");
    header("Location: absensi.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FFC0CB 0%, #FFFFFF 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .absensi-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 100px;
            height: auto;
        }
        h2 {
            color: #FF69B4;
            margin-bottom: 20px;
            font-weight: bold;
        }
        p {
            color: #666;
            margin-bottom: 20px;
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
        }
        .btn-secondary-custom:hover {
            background: #5a6268;
        }
        .alert {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="absensi-container">
        <div class="logo">
            <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
        </div>
        <h2>Absensi Karyawan</h2>
        <p>Tanggal: <strong><?= $tanggal ?></strong></p>

        <?php if (!$data) { ?>
            <form method="POST">
                <button name="masuk" class="btn-custom">Absen Masuk</button>
            </form>
        <?php } elseif ($data && !$data['jam_pulang']) { ?>
            <div class="alert">
                <p>Jam Masuk: <strong><?= $data['jam_masuk'] ?></strong></p>
            </div>
            <form method="POST">
                <button name="pulang" class="btn-custom">Absen Pulang</button>
            </form>
        <?php } else { ?>
            <div class="alert">
                <p>Jam Masuk: <strong><?= $data['jam_masuk'] ?></strong></p>
                <p>Jam Pulang: <strong><?= $data['jam_pulang'] ?></strong></p>
                <p><b>Absensi hari ini sudah lengkap</b></p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary-custom">Kembali ke Dashboard</a>
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

