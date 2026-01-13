<?php
require_once '../tcpdf/tcpdf.php';
include '../config/db.php';

// buat objek PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// judul
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'REKAP ABSENSI KARYAWAN', 0, 1, 'C');
$pdf->Ln(3);

// font tabel
$pdf->SetFont('helvetica', '', 9);

// Ambil cabang_id dari parameter GET jika ada
$cabang_filter = "";
if (isset($_GET['cabang_id'])) {
    $cabang_id = mysqli_real_escape_string($conn, $_GET['cabang_id']);
    $cabang_filter = "WHERE karyawan.cabang_id = '$cabang_id'";
}

// QUERY REKAP
$query = mysqli_query($conn, "
SELECT
    karyawan.id AS id_karyawan,
    users.nama,
    karyawan.jabatan,

    SUM(CASE WHEN absensi.jam_masuk IS NOT NULL THEN 1 ELSE 0 END) AS hari_kerja,
    SUM(CASE WHEN ket.jenis='cuti' AND ket.status='approved' THEN DATEDIFF(ket.tanggal_selesai, ket.tanggal_mulai)+1 ELSE 0 END) AS cuti,
    SUM(CASE WHEN ket.jenis='sakit' THEN DATEDIFF(ket.tanggal_selesai, ket.tanggal_mulai)+1 ELSE 0 END) AS sakit,
    SUM(CASE WHEN ket.jenis='halfday' THEN 1 ELSE 0 END) AS halfday,
    0 AS alpha,
    SUM(absensi.total_lembur) AS total_lembur

FROM karyawan
JOIN users ON users.id = karyawan.user_id
LEFT JOIN absensi ON absensi.karyawan_id = karyawan.id
LEFT JOIN ketidakhadiran ket ON ket.karyawan_id = karyawan.id
$cabang_filter
GROUP BY karyawan.id
ORDER BY users.nama ASC
");

// header tabel
$html = '
<table border="1" cellpadding="4">
<tr style="background-color:#f2f2f2; font-weight:bold;">
    <th width="5%">ID</th>
    <th width="15%">Nama</th>
    <th width="15%">Jabatan</th>
    <th width="8%">Hadir</th>
    <th width="8%">Cuti</th>
    <th width="8%">Sakit</th>
    <th width="8%">Halfday</th>
    <th width="8%">Alpha</th>
    <th width="10%">Lembur (Jam)</th>
</tr>
';

// isi tabel
while ($d = mysqli_fetch_assoc($query)) {
    $html .= '
    <tr>
        <td>'.$d['id_karyawan'].'</td>
        <td>'.$d['nama'].'</td>
        <td>'.$d['jabatan'].'</td>
        <td align="center">'.$d['hari_kerja'].'</td>
        <td align="center">'.$d['cuti'].'</td>
        <td align="center">'.$d['sakit'].'</td>
        <td align="center">'.$d['halfday'].'</td>
        <td align="center">'.$d['alpha'].'</td>
        <td align="center">'.$d['total_lembur'].'</td>
    </tr>
    ';
}

$html .= '</table>';

// render
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('rekap-absensi-karyawan.pdf', 'I');
