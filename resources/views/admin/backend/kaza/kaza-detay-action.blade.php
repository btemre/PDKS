<div class="hstack gap-2">
    <a href="javascript:void(0);" 
       onClick="showDetailsFunc({{ $row->kaza_id }})" 
       class="btn btn-soft-info btn-sm btn-icon rounded-pill" 
       data-bs-toggle="tooltip" 
       data-bs-placement="top" 
       title="Kaza Resimlerini Görüntüle">
        <i class="ri-gallery-line fs-16"></i>
    </a>

    @if (Auth::guard('web')->user()->can('trafik.sil'))
    <a href="javascript:void(0);" 
       onClick="deleteFunc({{ $row->kaza_id }})" 
       class="btn btn-soft-danger btn-sm btn-icon rounded-pill" 
       data-bs-toggle="tooltip" 
       data-bs-placement="top" 
       title="Sil">
        <i class="ri-delete-bin-5-fill fs-16"></i>
    </a>
    @endif
</div>