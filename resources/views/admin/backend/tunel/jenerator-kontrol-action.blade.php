<div class="hstack gap-3 fs-15">
    {{-- Düzenle 
    @if (Auth::guard('web')->user()->can('jenerator.duzenle'))
        <a href="javascript:void(0);" 
           onClick="editKontrolFunc({{ $kontrol_id }})" 
           class="link-primary" 
           title="Düzenle">
            <i class="ri-edit-2-line"></i>
        </a>
    @endif


    @if (Auth::guard('web')->user()->can('jenerator.duzenle'))
        <a href="javascript:void(0);" 
           onClick="deleteKontrolFunc({{ $kontrol_id }})" 
           class="link-danger" 
           title="Sil">
            <i class="ri-delete-bin-line"></i>
        </a>
    @endif--}}
</div>
