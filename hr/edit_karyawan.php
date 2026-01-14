<?php
include '../config/db.php';

$id = $_GET['id'];

// Get user data
$user_data = mysqli_query($conn, "SELECT id, nama, email FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($user_data);

// Get karyawan data
$karyawan_data = mysqli_query($conn, "SELECT nip, jabatan, cabang_id FROM karyawan WHERE user_id='$id'");
$karyawan = mysqli_fetch_assoc($karyawan_data);

// Get cabang name
$cabang_name = '';
if ($karyawan['cabang_id']) {
    $cabang_data = mysqli_query($conn, "SELECT nama_cabang FROM cabang WHERE id='{$karyawan['cabang_id']}'");
    $cabang = mysqli_fetch_assoc($cabang_data);
    $cabang_name = $cabang['nama_cabang'];
}

// Combine data
$d = array_merge($user, $karyawan, ['nama_cabang' => $cabang_name]);

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nip = $_POST['nip'];
    $jabatan = $_POST['jabatan'];
    $cabang_id = $_POST['cabang_id'];

    mysqli_query($conn, "
        UPDATE users SET nama='$nama', email='$email'
        WHERE id='$id'
    ");

    mysqli_query($conn, "
        UPDATE karyawan SET nip='$nip', jabatan='$jabatan', cabang_id='$cabang_id'
        WHERE user_id='$id'
    ");

    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #FFC0CB 0%, #FFFFFF 100%);
            min-height: 100vh;
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

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #FF69B4, #FF1493);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .logo img {
            max-width: 100px;
            height: auto;
        }

        .btn-pink {
            background: linear-gradient(135deg, #FF69B4, #FF1493);
            color: white;
            border: none;
        }

        .btn-pink:hover {
            background: linear-gradient(135deg, #FF1493, #FF69B4);
            color: white;
        }

        label {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container py-5">

        <!-- Header -->
        <div class="header d-flex justify-content-between align-items-center mb-4">
            <div class="logo">
                <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi"
                    onerror="this.style.display='none';">
            </div>
            <h3 class="text-danger fw-bold">Edit Data Karyawan</h3>
            <a href="dashboard.php" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>

        <!-- Card Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Edit Karyawan</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control"
                                value="<?= htmlspecialchars($d['nama']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($d['email']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control"
                                value="<?= htmlspecialchars($d['nip']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan" class="form-control" required>
                                <option value="">-- Pilih Jabatan --</option>
                                <option value="Kepala Toko" <?= $d['jabatan'] == 'Kepala Toko' ? 'selected' : '' ?>>Kepala Toko</option>
                                <option value="Merchandiser" <?= $d['jabatan'] == 'Merchandiser' ? 'selected' : '' ?>>Merchandiser</option>
                                <option value="Admin (Konten+Olshop)" <?= $d['jabatan'] == 'Admin (Konten+Olshop)' ? 'selected' : '' ?>>Admin (Konten+Olshop)</option>
                                <option value="Kasir Utama" <?= $d['jabatan'] == 'Kasir Utama' ? 'selected' : '' ?>>Kasir Utama</option>
                                <option value="Kasir Backup" <?= $d['jabatan'] == 'Kasir Backup' ? 'selected' : '' ?>>Kasir Backup</option>
                                <option value="Security" <?= $d['jabatan'] == 'Security' ? 'selected' : '' ?>>Security</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Cabang</label>
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

                    <!-- Tombol -->
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" name="update" class="btn btn-pink px-4">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>

</html>