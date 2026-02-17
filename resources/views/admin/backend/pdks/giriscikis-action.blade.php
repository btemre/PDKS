@if (!empty($is_hafta_sonu))
    <span class="badge bg-secondary fs-12">HAFTA SONU</span>
@elseif (!empty($izin_ad))
    <div class="hstack gap-3 fs-15">
        <button type="button" class="btn btn-secondary btn-animation waves-effect waves-light"
        data-text="{{ $izinmazeret_aciklama }}"
            onclick="showIzinModal(
                    '{{ $personel_adsoyad }}',
                    '{{ $izinmazeret_baslayis }}',
                    '{{ $izin_ad }}',
                    '{{ $izinmazeret_baslayissaat }}',
                    '{{ $izinmazeret_bitissaat }}',
                    '{{ $izinmazeret_aciklama }}'
                )">
           <span> {{ $izin_ad }}</span>
           
        </button>
    </div>
@endif