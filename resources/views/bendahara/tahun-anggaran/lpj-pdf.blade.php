<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>LPJ RAPB {{ $tahunAnggaran->nama }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10.5pt; color: #1a1a18; line-height: 1.4; }
        .kop { text-align: center; border-bottom: 3px double #1a1a18; padding-bottom: 10px; margin-bottom: 16px; }
        .kop h1 { margin: 0; font-size: 15pt; letter-spacing: 1px; }
        .kop h2 { margin: 4px 0 0; font-size: 12pt; }
        .kop p { margin: 2px 0; font-size: 9pt; }
        table.laporan { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.laporan th, table.laporan td { border: 1px solid #999; padding: 4px 6px; }
        table.laporan th { background: #eaf3ee; font-size: 9pt; text-align: left; }
        .kategori-row td { background: #f2ecdd; font-weight: bold; }
        .sub-row td { background: #fbf8f2; font-style: italic; }
        .num { text-align: right; white-space: nowrap; }
        .total-row td { background: #143326; color: #fff; font-weight: bold; }
        .summary { width: 100%; margin-top: 20px; }
        .summary td { padding: 3px 0; }
        .summary td.label { width: 200px; }
        .ttd { width: 100%; margin-top: 40px; }
        .ttd td { width: 50%; text-align: center; vertical-align: top; }
        .ttd .space { height: 60px; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>PONDOK AL FALAH PUTRA</h1>
        <h2>Laporan Pertanggungjawaban RAPB — {{ $tahunAnggaran->nama }}</h2>
        <p>Periode {{ $tahunAnggaran->tanggal_mulai->translatedFormat('d F Y') }} – {{ $tahunAnggaran->tanggal_selesai->translatedFormat('d F Y') }}</p>
    </div>

    <table class="laporan">
        <thead>
            <tr>
                <th style="width: 40%;">Uraian</th>
                <th style="width: 15%;">Jenis</th>
                <th class="num" style="width: 15%;">Rencana</th>
                <th class="num" style="width: 15%;">Realisasi</th>
                <th class="num" style="width: 15%;">Selisih</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tahunAnggaran->kategoris as $kategori)
                <tr class="kategori-row">
                    <td colspan="5">{{ $kategori->nama }}</td>
                </tr>
                @foreach ($kategori->subKategoris as $sub)
                    <tr class="sub-row">
                        <td colspan="5">{{ $sub->nama }}</td>
                    </tr>
                    @foreach ($sub->itemRincians as $item)
                        @php $realisasi = $item->realisasis->sum('jumlah'); @endphp
                        <tr>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $kategori->jenis }}</td>
                            <td class="num">{{ number_format($item->jumlah_rencana, 0, ',', '.') }}</td>
                            <td class="num">{{ number_format($realisasi, 0, ',', '.') }}</td>
                            <td class="num">{{ number_format($item->jumlah_rencana - $realisasi, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
            <tr class="total-row">
                <td colspan="2">TOTAL</td>
                <td class="num">{{ number_format($totalRencana, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($totalRencana - $totalRealisasi, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="summary">
        <tr><td class="label">Status Persetujuan</td><td>: {{ $tahunAnggaran->status_approval }}</td></tr>
        @if ($tahunAnggaran->disetujui_pada)
            <tr><td class="label">Disetujui Oleh</td><td>: {{ $tahunAnggaran->penyetuju->name ?? '—' }} pada {{ $tahunAnggaran->disetujui_pada->translatedFormat('d F Y H:i') }}</td></tr>
        @endif
    </table>

    <table class="ttd">
        <tr>
            <td></td>
            <td>
                {{ now()->translatedFormat('d F Y') }}<br>
                Bendahara,
                <div class="space"></div>
                <strong>________________________</strong>
            </td>
        </tr>
    </table>
</body>
</html>
