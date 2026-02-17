

<div class="hstack gap-3 fs-15">
    @if (Auth::guard('web')->user()->can('ayar.duzenle'))
    <a href="javascript:void(0);" onClick="editFunc({{ $ayar_id }})" class="link-primary" title="Düzenle"><i class="ri-edit-2-line"></i></a>
    @endif
    @if (Auth::guard('web')->user()->can('ayar.sil'))
    <a href="javascript:void(0);" onClick="deleteFunc({{ $ayar_id }})" class="link-danger" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
    @endif
</div>
