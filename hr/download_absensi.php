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

// Ambil data karyawan dan absensi
$data = mysqli_query($conn, "
    SELECT
        karyawan.id AS id_karyawan,
        users.nama,
        karyawan.jabatan,
        SUM(CASE WHEN absensi.jam_masuk IS NOT NULL THEN 1 ELSE 0 END) AS hari_kerja,
        SUM(CASE WHEN ketidakhadiran.jenis='cuti' AND ketidakhadiran.status='approved' THEN DATEDIFF(ketidakhadiran.tanggal_selesai, ketidakhadiran.tanggal_mulai)+1 ELSE 0 END) AS cuti,
        SUM(CASE WHEN ketidakhadiran.jenis='sakit' THEN DATEDIFF(ketidakhadiran.tanggal_selesai, ketidakhadiran.tanggal_mulai)+1 ELSE 0 END) AS sakit,
        SUM(CASE WHEN ketidakhadiran.jenis='halfday' THEN 1 ELSE 0 END) AS halfday,
        0 AS alpha,
        SUM(absensi.total_lembur) AS total_lembur
    FROM karyawan
    JOIN users ON users.id = karyawan.user_id
    LEFT JOIN absensi ON absensi.karyawan_id = karyawan.id
    LEFT JOIN ketidakhadiran ON ketidakhadiran.karyawan_id = karyawan.id
    WHERE karyawan.cabang_id = '$cabang_id'
    GROUP BY karyawan.id, users.nama, karyawan.jabatan
    ORDER BY users.nama ASC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Absensi - <?= $cabang['nama_cabang'] ?></title>
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
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="text-center mb-4 logo">
                <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
            </div>
            <h2>Preview Absensi - <?= $cabang['nama_cabang'] ?></h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Karyawan</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Jumlah Hari Kerja</th>
                            <th>Cuti (hr)</th>
                            <th>Sakit (hr)</th>
                            <th>Halfday (hr)</th>
                            <th>Tanpa Keterangan (hr)</th>
                            <th>Lembur (jam)</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?= $d['id_karyawan'] ?></td>
                            <td><?= $d['nama'] ?></td>
                            <td><?= $d['jabatan'] ?></td>
                            <td><?= $d['hari_kerja'] ?></td>
                            <td><?= $d['cuti'] ?></td>
                            <td><?= $d['sakit'] ?></td>
                            <td><?= $d['halfday'] ?></td>
                            <td><?= $d['alpha'] ?></td>
                            <td><?= $d['total_lembur'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="text-center mt-3">
                <a href="export_pdf.php?cabang_id=<?= $cabang_id ?>" target="_blank" class="btn btn-custom me-2">Export PDF</a>
                <a href="branch_dashboard.php" class="btn btn-custom">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
