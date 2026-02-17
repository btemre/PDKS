<div class="hstack gap-2 flex-wrap">
    @if (!empty($izin_ad))
        <button type="button" class="btn btn-secondary btn-sm"
            onclick="showIzinModal(
                    '{{ $personel_adsoyad }}',
                    '{{ $izinmazeret_baslayis ?? '' }}',
                    '{{ $izin_ad }}',
                    '{{ $izinmazeret_baslayissaat ?? '' }}',
                    '{{ $izinmazeret_bitissaat ?? '' }}',
                    '{{ $izinmazeret_aciklama ?? '' }}'
                )">
            <i class="mdi mdi-calendar-check me-1"></i>{{ $izin_ad }}
        </button>
    @endif
    <button type="button" class="btn btn-soft-info btn-sm" title="Not ekle/düzenle"
        onclick="showGunlukNotModal({{ $personel_id ?? 0 }}, '{{ $tarih ?? '' }}', 'erken_cikan')">
        <i class="mdi mdi-note-text-outline me-1"></i>Not
    </button>
</div>