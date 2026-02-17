

<div class="hstack gap-3 fs-15">
    @if (Auth::guard('web')->user()->can('jenerator.duzenle'))
    <a href="javascript:void(0);" onClick="editFunc({{ $jenerator_id }})" class="link-primary" title="Düzenle"><i class="ri-edit-2-line"></i></a>
    @endif
    @if (Auth::guard('web')->user()->can('jenerator.kontrol'))
    <a href="{{ route('jenerator.haftalik.kontrol', $jenerator_id) }}" 
       class="link-info" title="Haftalık Kontroller">
        <i class="ri-calendar-check-line"></i>
    </a>
@endif
</div>
