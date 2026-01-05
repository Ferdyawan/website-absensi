<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'karyawan') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id'];
$karyawan_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM karyawan WHERE user_id='$user_id'"))['id'];

// Ambil data karyawan
$karyawan_data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT users.nama, karyawan.jabatan 
    FROM users 
    JOIN karyawan ON users.id = karyawan.user_id 
    WHERE users.id='$user_id'
"));
$nama = $karyawan_data['nama'];
$jabatan = $karyawan_data['jabatan'];

$message = '';

if (isset($_POST['submit'])) {
    $jenis = $_POST['jenis'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $alasan = $_POST['alasan'] ?? '';
    $jenis_cuti = $_POST['jenis_cuti'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $no_telp = $_POST['no_telp'] ?? '';
    $file_surat = '';

    if ($jenis == 'sakit' && isset($_FILES['file_surat'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = basename($_FILES["file_surat"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        if (move_uploaded_file($_FILES["file_surat"]["tmp_name"], $target_file)) {
            $file_surat = $target_file;
        }
    }

    $status = ($jenis == 'cuti') ? 'pending' : 'approved'; // Cuti pending, lainnya langsung approved

    mysqli_query($conn, "
        INSERT INTO ketidakhadiran (karyawan_id, jenis, tanggal_mulai, tanggal_selesai, status, alasan, jenis_cuti, alamat, no_telp, file_surat)
        VALUES ('$karyawan_id', '$jenis', '$tanggal_mulai', '$tanggal_selesai', '$status', '$alasan', '$jenis_cuti', '$alamat', '$no_telp', '$file_surat')
    ");
    $message = 'Pengajuan berhasil dikirim!';
}

// Ambil data ketidakhadiran karyawan
$data = mysqli_query($conn, "SELECT * FROM ketidakhadiran WHERE karyawan_id='$karyawan_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ketidakhadiran</title>
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
        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="text-center mb-4 logo">
                <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
            </div>
            <h2>Ketidakhadiran</h2>
            <?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Jenis Ketidakhadiran</label>
                    <select name="jenis" class="form-control" id="jenis" required>
                        <option value="">Pilih</option>
                        <option value="cuti">Cuti</option>
                        <option value="sakit">Sakit</option>
                        <option value="half_day">Half Day</option>
                        <option value="tanpa_keterangan">Tanpa Keterangan</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" required>
                    </div>
                </div>

                <div id="cuti-fields" style="display:none;">
                    <h5>Data Karyawan</h5>
                    <p>Nama: <?php echo $nama; ?></p>
                    <p>Jabatan: <?php echo $jabatan; ?></p>
                    <div class="mb-3">
                        <label class="form-label">Jenis Cuti</label>
                        <input type="text" name="jenis_cuti" class="form-control" placeholder="Jenis Cuti">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Selama Cuti</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telp</label>
                        <input type="text" name="no_telp" class="form-control" placeholder="No. Telp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Cuti</label>
                        <textarea name="alasan" class="form-control" placeholder="Alasan"></textarea>
                    </div>
                </div>

                <div id="sakit-fields" style="display:none;">
                    <h5>Surat Sakit</h5>
                    <div class="mb-3">
                        <label class="form-label">Upload Surat Sakit</label>
                        <input type="file" name="file_surat" class="form-control" accept="image/*,.pdf">
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-custom">Submit</button>
            </form>
        </div>

        <div class="card">
            <h3>Riwayat Ketidakhadiran</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                        <tr>
                            <td><?php echo ucfirst($d['jenis']); ?></td>
                            <td><?php echo $d['tanggal_mulai'] . ' - ' . $d['tanggal_selesai']; ?></td>
                            <td class="status-<?php echo $d['status']; ?>"><?php echo ucfirst($d['status']); ?></td>
                            <td><?php echo $d['alasan']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-custom">Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
        document.getElementById('jenis').addEventListener('change', function() {
            var jenis = this.value;
            document.getElementById('cuti-fields').style.display = (jenis == 'cuti') ? 'block' : 'none';
            document.getElementById('sakit-fields').style.display = (jenis == 'sakit') ? 'block' : 'none';
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>