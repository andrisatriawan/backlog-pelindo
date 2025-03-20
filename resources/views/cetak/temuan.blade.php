<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Form Tindak Lanjut LHA</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            margin: 15mm 20mm;
            line-height: 1.4;
        }

        h2,
        p.header-text {
            margin: 0;
            text-align: center;
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
                TINDAK LANJUT SATUAN PENGAWASAN INTERNAL<br>
                {{ strtoupper($data->unit->nama) }}
            </strong>
        </p>
    </div>

    <!-- Main Table Content -->
    <table>
        <tr>
            <td style="width: 20px">1.</td>
            <td class="label">Tahun temuan</td>
            <td style="width: 20px">:</td>
            <td class="value">{{ $data->lha->periode }}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td class="label">Divisi/Bidang</td>
            <td>:</td>
            <td class="value">
                {{ $data->divisi->nama }}{{ $data->departemen ? '/' . $data->departemen->nama : '' }}
            </td>
        </tr>
        <tr>
            <td>3.</td>
            <td class="label">No./Judul temuan</td>
            <td>:</td>
            <td class="value">{{ $data->nomor }} / {{ $data->judul }}</td>
        </tr>
        <tr>
            <td>4.</td>
            <td class="label">Rekomendasi</td>
            <td>:</td>
            <td>
                <table class="nested-table">
                    @foreach ($data->rekomendasi as $key => $row)
                        <tr>
                            <td style="width: 20px">{{ chr($key + 97) }}.</td>
                            <td>{{ $row->nomor }} / {{ $row->deskripsi }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td>5.</td>
            <td class="label">Tindak lanjut</td>
            <td>:</td>
            <td class="value">
                <table class="nested-table">
                    @foreach ($data->rekomendasi as $rekomendasi)
                        @foreach ($rekomendasi->tindaklanjut as $key => $row)
                            <tr>
                                <td style="width: 20px">{{ chr((int) $key + 97) }}.</td>
                                <td>{{ $rekomendasi->nomor }} / {{ $row->deskripsi }}</td>
                            </tr>
                        @endforeach
                    @endforeach

                </table>
            </td>
        </tr>
        <tr>
            <td>6.</td>
            <td class="label" colspan="3">Dokumen Pendukung (Lampiran)</td>
        </tr>
        <tr>
            <td></td>
            <td class="value" colspan="3"></td>
        </tr>
    </table>

    <!-- Signature Section -->
    <div class="signature">
        <p>Medan, ................... 2024</p>
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
