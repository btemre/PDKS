<div class="hstack gap-3 fs-15">
    @if (Auth::guard('web')->user()->can('izin.onay'))
        <a href="javascript:void(0);" onClick="onayFunc({{ $izin_id }})" class="link-success" title="Onayla">
            <i class="ri-check-fill fs-16"></i>
        </a>
    @endif
</div>


