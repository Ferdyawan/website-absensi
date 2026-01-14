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

// TAMBAH KARYAWAN
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nip = $_POST['nip'];
    $jabatan = $_POST['jabatan'];
    $cabang_id = $_POST['cabang_id'];

    // password default karyawan
    $password = password_hash('123456', PASSWORD_DEFAULT);

    // 1. insert ke tabel users
    mysqli_query($conn, "
        INSERT INTO users (nama, email, password, role)
        VALUES ('$nama', '$email', '$password', 'karyawan')
    ");

    // 2. ambil user_id terakhir
    $user_id = mysqli_insert_id($conn);

    // 3. insert ke tabel karyawan
    mysqli_query($conn, "
        INSERT INTO karyawan (user_id, nip, jabatan, cabang_id)
        VALUES ('$user_id', '$nip', '$jabatan', '$cabang_id')
    ");

    header("Location: data_karyawan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Karyawan - <?= $cabang['nama_cabang'] ?></title>
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
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .logo img {
            max-width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        h2 {
            color: #FF69B4;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .form-control, .btn {
            border-radius: 10px;
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
        <div class="text-center mb-4 logo">
            <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
        </div>
        <h2>Tambah Data Karyawan - <?= $cabang['nama_cabang'] ?></h2>
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <input name="nama" class="form-control mb-3" placeholder="Nama" required>
                </div>
                <div class="col-md-6">
                    <input name="email" type="email" class="form-control mb-3" placeholder="Email" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <input name="nip" class="form-control mb-3" placeholder="NIP" required>
                </div>
                <div class="col-md-4">
                    <select name="jabatan" class="form-control mb-3" required>
                        <option value="">-- Pilih Jabatan --</option>
                        <option value="Kepala Toko">Kepala Toko</option>
                        <option value="Merchandiser">Merchandiser</option>
                        <option value="Admin (Konten+Olshop)">Admin (Konten+Olshop)</option>
                        <option value="Kasir Utama">Kasir Utama</option>
                        <option value="Kasir Backup">Kasir Backup</option>
                        <option value="Security">Security</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="cabang_id" class="form-control mb-3" required>
                        <option value="">-- Pilih Cabang --</option>
                        <?php 
                        $cabang_options = mysqli_query($conn, "SELECT * FROM cabang");
                        while ($c = mysqli_fetch_assoc($cabang_options)) { 
                        ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nama_cabang']) ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="text-center">
                <button name="tambah" class="btn btn-custom me-2">Tambah</button>
                <a href="data_karyawan.php" class="btn btn-secondary-custom">Kembali</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
