<?php
include '../config/db.php';

$id = $_GET['id'];

$data = mysqli_query($conn, "
    SELECT users.id, users.nama, users.email,
           karyawan.nip, karyawan.jabatan, karyawan.alamat
    FROM users
    JOIN karyawan ON users.id = karyawan.user_id
    WHERE users.id='$id'
");
$d = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $nama    = $_POST['nama'];
    $email   = $_POST['email'];
    $nip     = $_POST['nip'];
    $jabatan = $_POST['jabatan'];
    $alamat  = $_POST['alamat'];

    mysqli_query($conn, "
        UPDATE users SET nama='$nama', email='$email'
        WHERE id='$id'
    ");

    mysqli_query($conn, "
        UPDATE karyawan SET nip='$nip', jabatan='$jabatan', alamat='$alamat'
        WHERE user_id='$id'
    ");

    header("Location: dashboard.php");
}
?>

<h2>Edit Karyawan</h2>
<form method="POST">
    <input name="nama" value="<?= $d['nama'] ?>" required>
    <input name="email" value="<?= $d['email'] ?>" required>
    <input name="nip" value="<?= $d['nip'] ?>" required>
    <input name="jabatan" value="<?= $d['jabatan'] ?>" required>
    <textarea name="alamat"><?= $d['alamat'] ?></textarea>
    <button name="update">Update</button>
</form>
