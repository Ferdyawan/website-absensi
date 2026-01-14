<?php
session_start();
include '../config/db.php';

// Pastikan role HR
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'hr') {
    header("Location: ../index.php");
    exit;
}

// PILIH CABANG
if (isset($_POST['pilih_cabang'])) {
    $_SESSION['cabang_id'] = $_POST['cabang_id'];
    header("Location: branch_dashboard.php");
    exit;
}

// RESET CABANG
if (isset($_GET['reset_cabang'])) {
    unset($_SESSION['cabang_id']);
    header("Location: dashboard.php");
    exit;
}

// Jika cabang belum dipilih, tampilkan pilihan cabang
if (!isset($_SESSION['cabang_id'])) {
    // Ambil data cabang
    $cabang_data = mysqli_query($conn, "SELECT * FROM cabang");
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pilih Cabang - Dashboard HR</title>
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
                max-width: 500px;
                width: 100%;
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
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">
                <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
            </div>
            <h2>Pilih Cabang</h2>
            <form method="POST">
                <select name="cabang_id" class="form-control mb-3" required>
                    <option value="">-- Pilih Cabang --</option>
                    <?php while ($c = mysqli_fetch_assoc($cabang_data)) { ?>
                        <option value="<?= $c['id'] ?>"><?= $c['nama_cabang'] ?></option>
                    <?php } ?>
                </select>
                <button name="pilih_cabang" class="btn-custom">Pilih Cabang</button>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit;
} else {
    // Jika cabang sudah dipilih, redirect ke branch_dashboard
    header("Location: branch_dashboard.php");
    exit;
}

// TAMBAH KARYAWAN (ini tidak akan dijalankan karena redirect)
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
}

// AMBIL DATA KARYAWAN (JOIN)
$data = mysqli_query($conn, "
    SELECT
        users.id AS user_id,
        users.nama,
        users.email,
        karyawan.nip,
        karyawan.jabatan,
        cabang.nama_cabang
    FROM karyawan
    JOIN users ON users.id = karyawan.user_id
    JOIN cabang ON karyawan.cabang_id = cabang.id
");

// Ambil data cabang untuk dropdown
$cabang_options = mysqli_query($conn, "SELECT * FROM cabang");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard HR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FFC0CB 0%, #FFFFFF 100%);
            font-family: 'Arial', sans-serif;
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
        .logo img {
            max-width: 100px;
            height: auto;
        }
        h2 {
            color: #FF69B4;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-control, .btn {
            border-radius: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #FF1493 0%, #FF69B4 100%);
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-warning {
            background: #ffc107;
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
        .nav-links a {
            margin-left: 10px;
            color: #FF69B4;
            text-decoration: none;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="https://ik.imagekit.io/ferdyawans/LogoR.png" alt="Logo Absensi" onerror="this.style.display='none';">
            </div>
            <h2>Data Karyawan</h2>
            <div class="nav-links">
                <a href="../auth/logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
                <a href="laporan.php">Laporan</a>
            </div>
        </div>

        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <input name="nama" class="form-control" placeholder="Nama" required>
                </div>
                <div class="col-md-6">
                    <input name="email" type="email" class="form-control" placeholder="Email" required>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <input name="nip" class="form-control" placeholder="NIP" required>
                </div>
                <div class="col-md-4">
                    <input name="jabatan" class="form-control" placeholder="Jabatan" required>
                </div>
                <div class="col-md-4">
                    <select name="cabang_id" class="form-control" required>
                        <option value="">-- Pilih Cabang --</option>
                        <?php while ($c = mysqli_fetch_assoc($cabang_options)) { ?>
                            <option value="<?= $c['id'] ?>"><?= $c['nama_cabang'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <button name="tambah" class="btn btn-primary mt-2">Tambah</button>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Cabang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = mysqli_fetch_assoc($data)) { ?>
                    <tr>
                        <td><?= $d['nama'] ?></td>
                        <td><?= $d['email'] ?></td>
                        <td><?= $d['nip'] ?></td>
                        <td><?= $d['jabatan'] ?></td>
                        <td><?= $d['nama_cabang'] ?></td>
                        <td>
                            <a href="hapus_karyawan.php?id=<?= $d['user_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus karyawan ini?')">Hapus</a>
                            <a href="edit_karyawan.php?id=<?= $d['user_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>