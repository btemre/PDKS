

<div class="hstack gap-3 fs-15">
    @if (Auth::guard('web')->user()->can('jetfan.duzenle'))
    <a href="javascript:void(0);" onClick="editFunc({{ $jetfan_id }})" class="link-primary" title="Düzenle"><i class="ri-edit-2-line"></i></a>
    @endif
    @if (Auth::guard('web')->user()->can('jetfan.sil'))
    <a href="javascript:void(0);" onClick="deleteFunc({{ $jetfan_id }})" class="link-danger" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
    @endif
</div>
