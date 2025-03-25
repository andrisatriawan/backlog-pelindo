<?php

use Carbon\Carbon;

define('STATUS_LHA', [
    0 => 'Draf',
    1 => 'Proses',
    2 => 'Tolak',
    3 => 'Selesai'
]);

define('STATUS_REKOMENDASI', [
    0 => 'BD (Belum Ditindaklanjuti)',
    1 => 'BS (Belum Sesuai)',
    2 => 'Selesai',
    3 => 'TPTD'
]);

define('STATUS_TEMUAN', [
    0 => 'Draf',
    1 => 'Proses',
    2 => 'Selesai Internal',
    3 => 'Selesai',
    4 => 'Tolak',
]);

function formatTanggal($tanggal, $format = 'l, j F Y')
{
    return Carbon::parse($tanggal)
        ->locale('id')
        ->translatedFormat($format);
}
