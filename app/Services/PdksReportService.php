<?php
/*namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PdksReportService
{
    public function getGelmeyen($birim_id, $start, $end)
    {
        $data = DB::table('personeller as p')
            ->leftJoin('pdks_kayitlar as k', function ($join) use ($start, $end) {
                $join->on('p.id', '=', 'k.personel_id')
                     ->whereBetween('k.tarih', [$start, $end]);
            })
            ->where('p.birim_id', $birim_id)
            ->whereNull('k.id') // hiç giriş kaydı yok = gelmeyen
            ->select('p.adsoyad as Personel', DB::raw("'Gelmeyen' as Durum"))
            ->get()
            ->toArray();

        return $data;
    }

    public function getGecGelen($birim_id, $start, $end)
    {
        $data = DB::table('pdks_kayitlar as k')
            ->join('personeller as p', 'p.id', '=', 'k.personel_id')
            ->where('p.birim_id', $birim_id)
            ->whereBetween('k.tarih', [$start, $end])
            ->whereTime('k.giris_saati', '>', '09:00:00') // mesela 09:00’dan sonra giriş yapanlar
            ->select('p.adsoyad as Personel', 'k.giris_saati as GirisSaati')
            ->get()
            ->toArray();

        return $data;
    }

    public function getGirisCikis($birim_id, $start, $end)
    {
        $data = DB::table('pdks_kayitlar as k')
            ->join('personeller as p', 'p.id', '=', 'k.personel_id')
            ->where('p.birim_id', $birim_id)
            ->whereBetween('k.tarih', [$start, $end])
            ->select('p.adsoyad as Personel', 'k.giris_saati as Giris', 'k.cikis_saati as Cikis')
            ->get()
            ->toArray();

        return $data;
    }

    public function getErkenCikan($birim_id, $start, $end)
    {
        $data = DB::table('pdks_kayitlar as k')
            ->join('personeller as p', 'p.id', '=', 'k.personel_id')
            ->where('p.birim_id', $birim_id)
            ->whereBetween('k.tarih', [$start, $end])
            ->whereTime('k.cikis_saati', '<', '17:00:00') // mesai bitiminden önce çıkmış
            ->select('p.adsoyad as Personel', 'k.cikis_saati as CikisSaati')
            ->get()
            ->toArray();

        return $data;
    }
}
*/
