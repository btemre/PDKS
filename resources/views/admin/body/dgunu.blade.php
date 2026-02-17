<div class="dropdown topbar-head-dropdown ms-1 header-item" id="birthdayDropdown">
    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
        id="page-header-birthday-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
        aria-expanded="false">
        <i class='bx bx-cake fs-22'></i>
        @if ($dogumGunleri->count() > 0)
            <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                {{ $dogumGunleri->count() }}
            </span>
        @endif

    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-birthday-dropdown">
        <div class="dropdown-head bg-success bg-pattern rounded-top">
            <div class="p-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 fs-16 fw-semibold text-white">Doğum Günü</h6>
                    </div>
                    <div class="col-auto dropdown-tabs">
                        @if ($dogumGunleri->count() > 0)
                            <span class="badge bg-light-subtle text-body fs-13">
                                {{ $dogumGunleri->count() }} Kişi
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content position-relative">
            <div class="tab-pane fade show active py-2 ps-2" role="tabpanel">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    @forelse ($dogumGunleri as $value)
                        <div class="text-reset notification-item d-block dropdown-item position-relative">
                            <div class="d-flex">
                                <img src="{{ asset(
                                    !empty($value->personel_resim) && file_exists(public_path($value->personel_resim))
                                        ? $value->personel_resim
                                        : 'backend/assets/images/users/kgm.jpg',
                                ) }}"
                                    class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                <div class="flex-grow-1">
                                    <a class="stretched-link">
                                        <h6 class="mt-0 mb-1 lh-base">
                                            🎂 <b>{{ $value->personel_adsoyad }}</b>
                                        </h6>
                                        <span class="text-secondary">Bugün doğum günü!</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <span class="text-muted">Bugün doğum günü olan yok</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
