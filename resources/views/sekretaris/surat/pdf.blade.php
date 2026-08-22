<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $surat->nomor_surat }}</title>
    <style>
        @page { margin: 2.5cm 2.5cm 2cm 2.5cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #1a1a18; line-height: 1.5; }
        .kop { text-align: center; border-bottom: 3px double #1a1a18; padding-bottom: 10px; margin-bottom: 20px; }
        .kop h1 { margin: 0; font-size: 16pt; letter-spacing: 1px; }
        .kop p { margin: 2px 0; font-size: 10pt; }
        .meta { width: 100%; margin-bottom: 16px; }
        .meta td { padding: 1px 0; vertical-align: top; }
        .meta td.label { width: 90px; }
        .meta td.sep { width: 12px; }
        .isi { text-align: justify; margin-top: 16px; }
        .ttd { width: 100%; margin-top: 40px; }
        .ttd td { width: 50%; text-align: center; vertical-align: top; }
        .ttd .space { height: 60px; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>PONDOK AL FALAH PUTRA</h1>
        <p>Sistem Informasi Administrasi Pondok — SIAP AFP</p>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Nomor</td><td class="sep">:</td><td>{{ $surat->nomor_surat }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td><td class="sep">:</td><td>{{ $surat->perihal }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td><td class="sep">:</td><td>{{ $surat->tanggal->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <p>
        Kepada Yth.<br>
        <strong>{{ $surat->tujuan_pengirim }}</strong><br>
        di tempat
    </p>

    <div class="isi">
        {!! nl2br(e($surat->isi_lengkap ?? $surat->isi_ringkas ?? '')) !!}
    </div>

    @if ($surat->santri)
        <p style="margin-top: 12px;">
            Santri terkait: <strong>{{ $surat->santri->nama_lengkap }}</strong> (NIS {{ $surat->santri->nis }})
        </p>
    @endif

    <table class="ttd">
        <tr>
            <td></td>
            <td>
                {{ $surat->tanggal->translatedFormat('d F Y') }}<br>
                Sekretaris,
                <div class="space"></div>
                <strong>{{ $surat->pembuat->name ?? '—' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
