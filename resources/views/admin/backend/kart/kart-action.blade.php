<div class="hstack gap-3 fs-15 justify-content-center">
    @if (Auth::guard('web')->user()->can('pdks.sil'))
    <a href="javascript:void(0);" onClick="deleteFunc({{ $kart_id }})" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center" title="Sil">
        <i class="ri-delete-bin-5-line me-1"></i>
    </a>
    @endif
    @if (Auth::guard('web')->user()->can('pdks.sil'))
<a href="javascript:void(0);" onClick="editFunc({{ $kart_id }}, '{{ $kart_numarasi }}')" 
   class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center" 
   title="Düzenle">
   <i class="ri-edit-line me-1"></i>
</a>
@endif

</div>