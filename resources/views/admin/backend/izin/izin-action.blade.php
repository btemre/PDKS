<div class="hstack gap-3 fs-15">
    @if (Auth::guard('web')->user()->can('izin.yazdir'))
    <a href="{{ route('izin.yazdir', ['id' => $izin_id]) }}" target="_blank" class="link-success" title="Yazdır">
        <i class="ri-printer-fill fs-16"></i>
    </a> @endif
    @if (Auth::guard('web')->user()->can('izin.sil'))
     <a href="javascript:void(0);" onClick="deleteFunc({{ $izin_id }})" class="link-danger" title="Sil"><i
            class="ri-delete-bin-5-fill fs-16"></i></a>
    @endif
</div>



<!--
<a href="javascript:void(0)" data-toggle="tooltip" onClick="editFunc({{ $personel_id }})" data-original-title="Edit" class="edit btn btn-success edit">
    Edit
    </a>
    <a href="javascript:void(0);" id="delete-compnay" onClick="deleteFunc({{ $personel_id }})" data-toggle="tooltip" data-original-title="Delete" class="delete btn btn-danger">
    Delete
    </a>
    <a href="javascript:void(0);" onClick="editFunc({{ $izin_id }})" class="link-primary" title="Düzenle"><i class="ri-edit-2-line"></i></a>
-->