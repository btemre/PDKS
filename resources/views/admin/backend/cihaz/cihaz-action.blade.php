<div class="hstack gap-3 fs-15">
    @if (Auth::guard('web')->user()->can('cihaz.sil'))
        <a href="javascript:void(0);" onClick="deleteFunc({{ $cihaz_id }})" class="link-danger" title="Sil"><i
                class="ri-delete-bin-5-fill fs-16"></i></a>
    @endif
</div>
