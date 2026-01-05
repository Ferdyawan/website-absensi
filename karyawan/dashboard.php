<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../index.php");
    exit;
}

// Ambil nama karyawan
$nama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM users WHERE id='{$_SESSION['id']}'"))['nama'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan</title>
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
        .dashboard-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
            height: auto;
        }
        h2 {
            color: #FF69B4;
            margin-bottom: 20px;
            font-weight: bold;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: transform 0.2s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #FF1493 0%, #FF69B4 100%);
            color: white;
            text-decoration: none;
        }
        .btn-logout {
            background: #dc3545;
        }
        .btn-logout:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="logo">
            <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
        </div>
        <h2>Dashboard Karyawan</h2>
        <p>Selamat datang, <?php echo $nama; ?>!</p>
        <div>
            <a href="absensi.php" class="btn-custom">Absensi Hari Ini</a>
            <a href="ketidakhadiran.php" class="btn-custom">Ketidakhadiran</a>
            <a href="../auth/logout.php" class="btn-custom btn-logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
