<div class="hstack gap-3 fs-15">
    {{-- Kaydı düzenlemek için bir buton --}}
    @if (Auth::guard('web')->user()->can('bilgisayar.duzenle'))
        <a href="javascript:void(0);" onClick="editFunc({{ $id }})" class="link-primary" title="Düzenle"><i class="ri-edit-2-line"></i></a>
    @endif
    
    {{-- Kaydı silmek için bir buton --}}
    @if (Auth::guard('web')->user()->can('bilgisayar.sil'))
        <a href="javascript:void(0);" onClick="deleteFunc({{ $id }})" class="link-danger" title="Sil"><i class="ri-delete-bin-5-line"></i></a>
    @endif
</div>

