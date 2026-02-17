@if (Auth::guard('web')->user()->can('evrak.sil'))
<div class="hstack gap-3 fs-15">
    <a href="javascript:void(0);" onClick="deleteFunc({{ $evrak_id }})" class="link-danger" title="Sil"><i
            class="ri-delete-bin-5-fill fs-16"></i></a>
</div>
@endif