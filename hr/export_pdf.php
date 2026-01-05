<?php
require_once '../dompdf/vendor/autoload.php';
use Dompdf\Dompdf;

include '../config/db.php';

$dompdf = new Dompdf();

$html = '
<h3 style="text-align:center;">Laporan Absensi Karyawan</h3>
<table border="1" width="100%" cellpadding="5">
<tr>
    <th>Nama</th>
    <th>NIP</th>
    <th>Tanggal</th>
    <th>Masuk</th>
    <th>Pulang</th>
</tr>';

$data = mysqli_query($conn, "
    SELECT users.nama, karyawan.nip, absensi.tanggal,
           absensi.jam_masuk, absensi.jam_pulang
    FROM absensi
    JOIN karyawan ON absensi.karyawan_id = karyawan.id
    JOIN users ON users.id = karyawan.user_id
    ORDER BY absensi.tanggal DESC
");

while ($d = mysqli_fetch_assoc($data)) {
    $html .= "
    <tr>
        <td>{$d['nama']}</td>
        <td>{$d['nip']}</td>
        <td>{$d['tanggal']}</td>
        <td>{$d['jam_masuk']}</td>
        <td>{$d['jam_pulang']}</td>
    </tr>";
}

$html .= '</table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan-absensi.pdf", ["Attachment" => false]);
