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
    </style>
</head>

<body>
    <!-- Header Section -->

    <div class="header">
        <p class="header-text">
            <strong>
                HASIL MONITORING TINDAKLANJUT REKOMENDASI AUDIT TAHUN {{ $data->periode ?? date('Y') }}<br>
                PT. PELABUHAN INDONESIA (PERSERO) {{ strtoupper($data->periode) }}
            </strong>
        </p>

        <p>
            Penanggungjawab : {{ '...................' }}
        </p>
    </div>

    <!-- Main Table Content -->
    <table>
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
        @foreach ($data->temuan->groupBy('divisi_id') as $row)
            <tr>
                <td>{{ $row-> }}</td>
            </tr>
        @endforeach
    </table>

    <!-- Signature Section -->
    <div class="signature">
        <p>Medan, {{ formatTanggal(now()->toDateString(), 'j F Y') }}</p>
        <p>PIC</p>
        <br><br><br>
        @php
            $userPIC = $data->logStage()->where('stage', 3)->latest()->first();
        @endphp
        <p>{{ $userPIC->user->nama ?? '....................' }}<br>
            NIPP: {{ $userPIC->user->nip ?? '....................' }}</p>
    </div>
</body>

</html>
