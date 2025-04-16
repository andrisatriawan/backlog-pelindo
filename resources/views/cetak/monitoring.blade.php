<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hasil Monitoring Tindaklanjut</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            margin: 3mm 3mm;
            line-height: 1.4;
        }

        h2,
        p.header-text {
            margin: 0;
            text-align: left;
        }

        .header {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table tr td {
            vertical-align: top;
            border: 1px solid black;
        }

        table tr th {
            border: 1px solid black;
        }

        .label {
            vertical-align: top;
            /* width: 10%; */
            white-space: nowrap;
        }

        .value {
            vertical-align: top;
        }

        .nested-table {
            width: 100%;
            border-collapse: collapse;
            vertical-align: top;
        }

        .nested-table td {
            padding: 2px 0;
        }

        .signature {
            margin-top: 40px;
        }

        .signature p {
            margin: 4px 0;
        }

        .centang {
            font-family: DejaVu Sans, sans-serif;
        }

        td {
            padding: 2px 5px;
            vertical-align: top;
        }
    </style>
</head>

<body>
    <div class="header">
        <p class="header-text">
            <strong>
                HASIL MONITORING TINDAKLANJUT REKOMENDASI AUDIT TAHUN {{ $data['periode'] ?? date('Y') }}<br>
                PT. PELABUHAN INDONESIA (PERSERO) {{ strtoupper($data['periode']) }}
            </strong>
        </p>
        {{--
        <p>
            Penanggungjawab : {{ '...................' }}
        </p> --}}
    </div>

    <!-- Main Table Content -->
    <table>
        <thead style="background-color: #87CEEB">
            <tr>
                <th rowspan="3">BIDANG</th>
                <th rowspan="3">JUMLAH TEMUAN</th>
                <th rowspan="3">JUMLAH REKOMENDASI</th>
                <th colspan="4">STATUS TINDAKLANJUT</th>
            </tr>
            <tr>
                <th rowspan="2">SESUAI</th>
                <th colspan="2">PANTAU</th>
                <th rowspan="2">TPTD</th>
            </tr>
            <tr>
                <th>BELUM SESUAI <br> (BS)</th>
                <th>BELUM DITINDAKLANJUTI <br> (BD)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalTemuan = 0;
                $totalRekomendasi = 0;
                $totalSesuai = 0;
                $totalBS = 0;
                $totalBD = 0;
                $totalTPTD = 0;
            @endphp
            @foreach ($data['temuan'] as $temuanDivisi)
                @php
                    $jumlahTemuan = count($temuanDivisi['data']);
                    $jumlahRekomendasi = 0;
                    $sesuai = 0;
                    $bs = 0;
                    $bd = 0;
                    $tptd = 0;

                    foreach ($temuanDivisi['data'] as $temuan) {
                        foreach ($temuan['rekomendasi'] as $rekom) {
                            $jumlahRekomendasi++;
                            switch ($rekom['status']) {
                                case '2':
                                    $sesuai++;
                                    break;
                                case '1':
                                    $bs++;
                                    break;
                                case '0':
                                    $bd++;
                                    break;
                                case '3': // kalau ada status TPTD
                                    $tptd++;
                                    break;
                            }
                        }
                    }

                    $totalTemuan += $jumlahTemuan;
                    $totalRekomendasi += $jumlahRekomendasi;
                    $totalSesuai += $sesuai;
                    $totalBS += $bs;
                    $totalBD += $bd;
                    $totalTPTD += $tptd;
                @endphp
                <tr>
                    <td>{{ strtoupper($temuanDivisi['nama_divisi']) }}</td>
                    <td style="text-align: center">{{ $jumlahTemuan }}</td>
                    <td style="text-align: center">{{ $jumlahRekomendasi }}</td>
                    <td style="text-align: center">{{ $sesuai }}</td>
                    <td style="text-align: center">{{ $bs }}</td>
                    <td style="text-align: center">{{ $bd }}</td>
                    <td style="text-align: center">{{ $tptd }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #87CEEB">
                <td style="text-align: center;font-weight: bold;">JUMLAH</td>
                <td style="text-align: center;font-weight: bold;">{{ $totalTemuan }}</td>
                <td style="text-align: center;font-weight: bold;">{{ $totalRekomendasi }}</td>
                <td style="text-align: center;font-weight: bold;">{{ $totalSesuai }}</td>
                <td style="text-align: center;font-weight: bold;">{{ $totalBS }}</td>
                <td style="text-align: center;font-weight: bold;">{{ $totalBD }}</td>
                <td style="text-align: center;font-weight: bold;">{{ $totalTPTD }}</td>
            </tr>
        </tbody>
    </table>
    <table>
        <thead style="background-color: #87CEEB">
            <tr>
                <th rowspan="3">NO</th>
                <th rowspan="3">TEMUAN HASIL PEMERIKSAAN</th>
                <th rowspan="3">REKOMENDASI</th>
                <th rowspan="3">BATAS WAKTU</th>
                <th rowspan="3">TGL SELESAI</th>
                <th rowspan="3">HASIL MONITORING</th>
                <th colspan="4">TINGKAT PENYELESAIAN</th>
            </tr>
            <tr>
                <th rowspan="2">SELESAI</th>
                <th colspan="2">PANTAU</th>
                <th rowspan="2">TPTD</th>
            </tr>
            <tr>
                <th>BS</th>
                <th>BD</th>
            </tr>
        </thead>
        <tbody>
            @php
                $abjad = range('A', 'Z');
                $idxDiv = 0;
            @endphp

            @foreach ($data['temuan'] as $divisi)
                @php $noTemuan = 1; @endphp
                <tr style="background-color: #FFA500">
                    <td>{{ $abjad[$idxDiv] }}</td>
                    <td>{{ strtoupper($divisi['nama_divisi']) }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                @foreach ($divisi['data'] as $temuan)
                    @foreach ($temuan['rekomendasi'] as $index => $rekom)
                        <tr>
                            @if ($index == 0)
                                <td>{{ $noTemuan++ }}</td>
                                <td>{{ $temuan['judul'] }}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif

                            <td>{{ chr(97 + $index) }}. {{ $rekom['deskripsi'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($rekom['batas_tanggal'])->translatedFormat('d F Y') }}</td>
                            <td>
                                @if ($rekom['tanggal_selesai'])
                                    {{ \Carbon\Carbon::parse($rekom['tanggal_selesai'])->translatedFormat('d F Y') }}
                                @endif
                            </td>
                            <td>
                                @foreach ($rekom['tindaklanjut'] as $tl)
                                    @foreach ($tl['files'] as $file)
                                        <div>
                                            {{ \Carbon\Carbon::parse($file['tanggal'] ?? now())->translatedFormat('d F Y') }}
                                        </div>
                                        <div>
                                            {{ $tl['deskripsi'] }}
                                        </div>
                                        <br>
                                    @endforeach
                                @endforeach
                            </td>

                            <td style="text-align: center" class="centang">{!! $rekom['status'] == '2' ? '&#10003;' : '' !!}</td>
                            {{-- Selesai --}}
                            <td style="text-align: center" class="centang">{!! $rekom['status'] == '1' ? '&#10003;' : '' !!}</td>
                            {{-- BS --}}
                            <td style="text-align: center" class="centang">{!! $rekom['status'] == '0' ? '&#10003;' : '' !!}</td>
                            {{-- BD --}}
                            <td style="text-align: center" class="centang">{!! $rekom['status'] == '3' ? '&#10003;' : '' !!}</td>
                            {{-- TPTD --}}
                        </tr>
                    @endforeach
                @endforeach

                @php $idxDiv++; @endphp
            @endforeach
        </tbody>
    </table>
</body>

</html>
