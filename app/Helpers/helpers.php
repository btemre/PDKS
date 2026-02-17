<?php
use Carbon\Carbon;
if (!function_exists('yaziylaSayi')) {
    function yaziylaSayi($sayi) {
        $o = [
            'birlik' => ['bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz'],
            'onluk' => ['on', 'yirmi', 'otuz', 'kırk', 'elli', 'altmış', 'yetmiş', 'seksen', 'doksan'],
            'basamak' => ['yüz', 'bin', 'milyon', 'milyar', 'trilyon', 'katrilyon']
        ];

        $basamak = array_reverse(str_split(implode('', array_reverse(str_split($sayi))), 3));
        $basamak_sayisi = count($basamak);

        for($i=0; $i < $basamak_sayisi; ++$i) {
            $basamak[$i] = implode(array_reverse(str_split($basamak[$i])));
            if(strlen($basamak[$i]) == 1)
                $basamak[$i] = '00' . $basamak[$i];
            elseif(strlen($basamak[$i]) == 2)
                $basamak[$i] = '0' . $basamak[$i];
        }

        $yenisayi = [];

        foreach($basamak as $k => $b) {
            if($b[0] > 0)
                $yenisayi[] = ($b[0] > 1 ? $o['birlik'][$b[0]-1] . ' ' : '') . $o['basamak'][0];

            if($b[1] > 0)
                $yenisayi[] = $o['onluk'][$b[1]-1];

            if($b[2] > 0)
                $yenisayi[] = $o['birlik'][$b[2]-1];

            if($basamak_sayisi > 1)
                $yenisayi[] = $o['basamak'][$basamak_sayisi-1];

            --$basamak_sayisi;
        }

        return implode(' ', $yenisayi);
    }
}

if (!function_exists('tarihsaat')) {
    /**
     * Tarihi GG.AA.YYYY - HH:MM formatında döndürür
     */
    function tarihsaat($date)
    {
        if (!$date) return null;
        return Carbon::parse($date)->format('d.m.Y - H:i');
    }
}

if (!function_exists('tarih')) {
    /**
     * Tarihi sadece GG.AA.YYYY formatında döndürür
     */
    function tarih($date)
    {
        if (!$date) return null;
        return Carbon::parse($date)->format('d.m.Y');
    }
}
if (!function_exists('saat')) {
    /**
     * Sadece saati SS:DD formatında döndürür
     */
    function saat($date)
    {
        if (!$date) return null;
        return Carbon::parse($date)->format('H:i');
    }
}