<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'hr') {
    header("Location: ../index.php");
    exit;
}

$data_absensi = mysqli_query($conn, "
    SELECT users.nama, karyawan.nip, absensi.tanggal,
           absensi.jam_masuk, absensi.jam_pulang
    FROM absensi
    JOIN karyawan ON absensi.karyawan_id = karyawan.id
    JOIN users ON users.id = karyawan.user_id
    ORDER BY absensi.tanggal DESC
");

$data_ketidakhadiran = mysqli_query($conn, "
    SELECT ketidakhadiran.*, users.nama, karyawan.nip
    FROM ketidakhadiran
    JOIN karyawan ON ketidakhadiran.karyawan_id = karyawan.id
    JOIN users ON users.id = karyawan.user_id
    ORDER BY ketidakhadiran.created_at DESC
");

if (isset($_POST['approve']) || isset($_POST['reject'])) {
    $id = $_POST['id'];
    $status = isset($_POST['approve']) ? 'approved' : 'rejected';
    mysqli_query($conn, "UPDATE ketidakhadiran SET status='$status' WHERE id='$id'");
    header("Location: laporan.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
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
        .header {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo img {
            max-width: 100px;
            height: auto;
        }
        h2 {
            color: #FF69B4;
            margin: 0;
        }
        .btn-custom {
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: bold;
            text-decoration: none;
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
        .back-link {
            margin-top: 20px;
            display: inline-block;
        }
        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
            </div>
            <h2>Laporan Absensi Karyawan</h2>
            <a href="export_pdf.php" target="_blank" class="btn-custom">Download PDF</a>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = mysqli_fetch_assoc($data_absensi)) { ?>
                    <tr>
                        <td><?= $d['nama'] ?></td>
                        <td><?= $d['nip'] ?></td>
                        <td><?= $d['tanggal'] ?></td>
                        <td><?= $d['jam_masuk'] ?></td>
                        <td><?= $d['jam_pulang'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3 class="mt-5">Laporan Ketidakhadiran</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jenis</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = mysqli_fetch_assoc($data_ketidakhadiran)) { ?>
                    <tr>
                        <td><?= $d['nama'] ?></td>
                        <td><?= $d['nip'] ?></td>
                        <td><?php echo ucfirst($d['jenis']); ?></td>
                        <td><?php echo $d['tanggal_mulai'] . ' - ' . $d['tanggal_selesai']; ?></td>
                        <td class="status-<?php echo $d['status']; ?>"><?php echo ucfirst($d['status']); ?></td>
                        <td><?php echo $d['alasan']; ?></td>
                        <td>
                            <?php if ($d['jenis'] == 'cuti' && $d['status'] == 'pending') { ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php } ?>
                            <?php if ($d['file_surat']) { ?>
                                <a href="<?= $d['file_surat'] ?>" target="_blank" class="btn btn-info btn-sm">Lihat Surat</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="back-link">
            <a href="dashboard.php" class="btn-custom">Kembali ke Dashboard</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
