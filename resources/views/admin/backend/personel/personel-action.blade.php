<div class="hstack gap-3 fs-15 justify-content-center">
    @if (Auth::guard('web')->user()->can('personel.duzenle'))
    <a href="javascript:void(0);" onClick="editFunc({{ $personel_id }})" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center" title="Düzenle">
        <i class="ri-edit-2-line me-1"></i>
    </a>
    @endif
    @if (Auth::guard('web')->user()->can('personel.sil'))
    <a href="javascript:void(0);" onClick="deleteFunc({{ $personel_id }})" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center" title="Sil">
        <i class="ri-delete-bin-5-line me-1"></i>
    </a>
    @endif
    @if (Auth::guard('web')->user()->can('personel.goruntule'))
    <a href="{{ route('personel.bilgidetay', $personel_id) }}" class="btn btn-outline-info btn-sm d-flex align-items-center justify-content-center" title="Görüntüle">
        <i class="ri-eye-line me-1"></i>
    </a>
    @endif
</div>