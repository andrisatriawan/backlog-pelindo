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

        body {
            position: relative;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 90pt;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            text-align: center;
        }

        .qr-code {
            margin: 0;
            left: -10px;
            margin-left: -10px;
        }
    </style>
</head>

<body>
    @if ($data->status != 2 && $data->last_stage <= 5)
        <div class="watermark">{{ $data->last_stage == 5 && $data->status == 1 ? 'DRAF FINAL' : 'DRAF' }}</div>
    @endif
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
                    @php
                        $counter = 0;
                    @endphp
                    @foreach ($data->rekomendasi as $key => $row)
                        {{-- @if ($data->last_stage == 6 && $row->is_spi == 0)
                            <tr>
                                <td style="width: 20px">{{ chr($counter + 97) }}.</td>
                                <td>{{ $row->nomor }} / {{ $row->deskripsi }}</td>
                            </tr>
                            @php
                                $counter++;
                            @endphp
                        @endif --}}
                        @if ($row->is_spi == null || $row->is_spi == 0)
                            <tr>
                                <td style="width: 20px">{{ chr($counter + 97) }}.</td>
                                <td>{{ $row->nomor }} / {{ $row->deskripsi }}</td>
                            </tr>
                            @php
                                $counter++;
                            @endphp
                        @endif
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
                    @php
                        $counter = 0;
                    @endphp
                    @foreach ($data->rekomendasi as $rekomendasi)
                        {{-- @if ($data->last_stage == 6 && $rekomendasi->is_spi)
                            @foreach ($rekomendasi->tindaklanjut as $key => $row)
                                @php
                                    $fileNames = $row->file->pluck('file.nama')->join(', ');
                                @endphp
                                <tr>
                                    <td style="width: 20px">{{ chr((int) $counter + 97) }}.</td>
                                    <td>{{ $rekomendasi->nomor }} / {{ $row->deskripsi }} ({{ $fileNames }})</td>
                                </tr>
                            @endforeach
                            @php
                                $counter++;
                            @endphp
                        @endif --}}
                        @if (!$rekomendasi->is_spi)
                            @foreach ($rekomendasi->tindaklanjut as $key => $row)
                                @php
                                    $fileNames = $row->file->pluck('file.nama')->join(', ');
                                @endphp
                                <tr>
                                    <td style="width: 20px">{{ chr((int) $counter + 97) }}.</td>
                                    <td>{{ $rekomendasi->nomor }} / {{ $row->deskripsi }} ({{ $fileNames }})</td>
                                </tr>
                            @endforeach
                            @php
                                $counter++;
                            @endphp
                        @endif
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td>6.</td>
            <td class="label">Dokumen Pendukung</td>
            <td>:</td>
            <td class="value">
                <table class="nested-table">
                    @php
                        $counter = 0;
                    @endphp
                    @foreach ($files as $file)
                        <tr>
                            <td style="width: 20px">{{ chr((int) $counter + 97) }}.</td>
                            <td>{{ $file->nama }}</td>
                        </tr>
                        @php
                            $counter++;
                        @endphp
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="value" colspan="3"></td>
        </tr>
    </table>

    <!-- Signature Section -->
    <div class="signature">
        <p>Medan, {{ formatTanggal(now()->toDateString(), 'j F Y') }}</p>
        <p>PIC</p>
        @if ($qrCode)
            <img src="{{ $qrCode }}" class="qr-code" width="100" height="100">
        @else
            <br><br><br>
        @endif
        @php
            $userPIC = $data->logStage()->where('stage', 4)->latest()->first();
        @endphp
        <p>{{ $userPIC->user->nama ?? '....................' }}<br>
            NIP: {{ $userPIC->user->nip ?? '....................' }}</p>
    </div>
</body>

</html>
