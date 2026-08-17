<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Santri Telat Kembali</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <h2>Santri Telat Kembali dari Izin</h2>
    <p>Sistem mendeteksi santri berikut belum kembali melewati rencana tanggal kembali:</p>
    <table cellpadding="6" style="border-collapse: collapse; border: 1px solid #d1d5db;">
        <tr>
            <td style="border: 1px solid #d1d5db;"><strong>Nama Santri</strong></td>
            <td style="border: 1px solid #d1d5db;">{{ $santri->nama_lengkap }} (NIS {{ $santri->nis }})</td>
        </tr>
        <tr>
            <td style="border: 1px solid #d1d5db;"><strong>Jenis Izin</strong></td>
            <td style="border: 1px solid #d1d5db;">{{ $izin->jenis_izin }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #d1d5db;"><strong>Tanggal Keluar</strong></td>
            <td style="border: 1px solid #d1d5db;">{{ $izin->tanggal_keluar->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #d1d5db;"><strong>Rencana Kembali</strong></td>
            <td style="border: 1px solid #d1d5db;">{{ $izin->rencana_kembali->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #d1d5db;"><strong>Penjemput</strong></td>
            <td style="border: 1px solid #d1d5db;">{{ $izin->penjemput }} ({{ $izin->kontak_penjemput }})</td>
        </tr>
    </table>
    <p>Mohon segera ditindaklanjuti.</p>
    <p style="color: #6b7280; font-size: 12px;">Email otomatis dari SIAP AFP — Sistem Informasi Administrasi Pondok Al Falah Putra.</p>
</body>
</html>
