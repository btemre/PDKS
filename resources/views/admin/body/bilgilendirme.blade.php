<div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
        aria-expanded="false">
        <i class='bx bx-bell fs-22'></i>
        @if ($totalNotifications > 0)
            <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                {{ $totalNotifications }}
            </span>
        @endif

    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
        aria-labelledby="page-header-notifications-dropdown">

        <div class="dropdown-head bg-primary bg-pattern rounded-top">
            <div class="p-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 fs-16 fw-semibold text-white">Bilgilendirme</h6>
                    </div>
                    <div class="col-auto dropdown-tabs">
                        @if ($totalNotifications > 0)
                            <span class="badge bg-light-subtle text-body fs-13">
                                {{ $totalNotifications }} Bildirim
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-2 pt-2">
                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                    id="notificationItemsTab" role="tablist">
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab"
                            aria-selected="true">
                            Dönüş
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab"
                            aria-selected="false">
                            Araçlar
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab"
                            aria-selected="false">
                            Onay
                        </a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#cihaz-tab" role="tab" aria-selected="false">
                            Cihaz
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content position-relative" id="notificationItemsTabContent">
            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    @if ($izinDonus->count() > 0)
                    @foreach ($izinDonus as $value)
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
                                        <h6 class="mt-0 mb-2 lh-base">
                                            <b>{{ $value->personel_adsoyad }}</b>
                                        </h6>
                                        <span class="text-secondary">{{ $value->izin_ad }}</span>
                                    </a>
                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                        <span><i class="mdi mdi-clock-outline"></i> {{ $value->izin_suresi }} gün</span>
                                    </p>
                                </div>

                                <div class="px-2 fs-15">
                                    <div class="form-check notification-check d-none">
                                        <input class="form-check-input" type="checkbox" value="" id="all-notification-check01" disabled>
                                        <label class="form-check-label" for="all-notification-check01"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @else
                        <div class="empty-notification-elem">
                            <div class="w-25 w-sm-50 pt-3 mx-auto">
                                <img src="{{ asset('backend/assets/images/svg/bell.svg') }}" class="img-fluid" alt="user-pic">
                            </div>
                            <div class="text-center pb-5 mt-2">
                                <h6 class="fs-18 fw-semibold lh-base">Bildirim Bulunmamakta!</h6>
                            </div>
                        </div>
                    @endif

                    <div class="my-3 text-center view-all">
                        <a href="{{ route('personel.izin') }}" class="btn btn-soft-success waves-effect waves-light">
                            Tümünü Görüntüle <i class="ri-arrow-right-line align-middle"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel" aria-labelledby="messages-tab">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    @if ($aracMuayene->count() > 0)
                        @foreach ($aracMuayene as $value)
                            <div class="text-reset notification-item d-block dropdown-item">
                                <div class="d-flex">
                                    <img src="{{ asset('backend/assets/images/users/kgm.jpg') }}"
                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                    <div class="flex-grow-1">
                                        <a class="stretched-link">
                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">{{ $value->arac_plaka }}</h6>
                                        </a>
                                        <div class="fs-13 text-muted">
                                            <p class="mb-1">Muayene Tarihi: {{ tarih($value->arac_ilkmuayene) }}</p>
                                        </div>
                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                            <span><i class="mdi mdi-clock-outline"></i> {{ $value->muayeneDurum }}</span>
                                        </p>
                                    </div>
                                    <div class="px-2 fs-15">
                                        <div class="form-check notification-check d-none">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="messages-notification-check01" disabled>
                                            <label class="form-check-label" for="messages-notification-check01"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
            
                        <div class="my-3 text-center view-all">
                            <a href="{{ route('arac.listesi') }}" class="btn btn-soft-success waves-effect waves-light">
                                Tümünü Görüntüle <i class="ri-arrow-right-line align-middle"></i>
                            </a>
                        </div>
                        @else
                        <div class="empty-notification-elem">
                            <div class="w-25 w-sm-50 pt-3 mx-auto">
                                <img src="{{ asset('backend/assets/images/svg/bell.svg') }}" class="img-fluid" alt="user-pic">
                            </div>
                            <div class="text-center pb-5 mt-2">
                                <h6 class="fs-18 fw-semibold lh-base">Bildirim Bulunmamakta!</h6>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="tab-pane fade py-2 ps-2" id="alerts-tab" role="tabpanel" aria-labelledby="alerts-tab">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    @forelse ($izinOnay as $value)
                        <div class="text-reset notification-item d-block dropdown-item">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('backend/assets/images/users/kgm.jpg') }}" 
                                     class="me-3 rounded-circle avatar-xs" 
                                     alt="user-avatar">
            
                                <div class="flex-grow-1">
                                    <a class="stretched-link">
                                        <h6 class="mt-0 mb-1 fs-13 fw-semibold text-truncate">
                                            {{ $value->personel_adsoyad }}
                                        </h6>
                                    </a>
                                    <div class="fs-13 text-muted text-truncate">
                                        <p class="mb-1">{{ $value->izin_ad }}</p>
                                    </div>
                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                        <i class="mdi mdi-clock-outline"></i> 
                                        {{ $value->izin_suresi }} Gün
                                    </p>
                                </div>
            
                                <div class="px-2 fs-15">
                                    <div class="form-check notification-check mb-0">
                                        <input class="form-check-input izin-checkbox" 
                                               type="checkbox"
                                               value="{{ $value->izin_id }}" 
                                               id="izin-check-{{ $value->izin_id }}">
                                        <label class="form-check-label" for="izin-check-{{ $value->izin_id }}"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <div class="empty-notification-elem">
                        <div class="w-25 w-sm-50 pt-3 mx-auto">
                            <img src="{{ asset('backend/assets/images/svg/bell.svg') }}" class="img-fluid" alt="user-pic">
                        </div>
                        <div class="text-center pb-5 mt-2">
                            <h6 class="fs-18 fw-semibold lh-base">Bildirim Bulunmamakta!</h6>
                        </div>
                    </div>
                    @endforelse
            
                    <div class="my-3 text-center view-all">
                        <a href="{{ route('personel.izinonay') }}" 
                           class="btn btn-soft-success waves-effect waves-light">
                            Tümünü Görüntüle 
                            <i class="ri-arrow-right-line align-middle"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade py-2 ps-2" id="cihaz-tab" role="tabpanel" aria-labelledby="cihaz-tab">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    @forelse ($cihaz as $value)
                        <div class="text-reset notification-item d-block dropdown-item">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('backend/assets/images/users/kgm.jpg') }}"
                                     class="me-3 rounded-circle avatar-xs"
                                     alt="device-avatar">
            
                                <div class="flex-grow-1">
                                    <a class="stretched-link">
                                        <h6 class="mt-0 mb-1 fs-13 fw-semibold text-truncate">
                                            {{ $value->cihaz_adi }}
                                        </h6>
                                    </a>
                                    <div class="fs-13 text-muted text-truncate">
                                        <p class="mb-1">{{ $value->cihaz_ip }}</p>
                                    </div>
                                    <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                        <i class="mdi mdi-clock-outline"></i>
                                        {{ tarihsaat($value->son_baglanti_zamani) }}
                                    </p>
                                </div>
            
                                <div class="px-2 fs-15">
                                    <div class="form-check notification-check d-none mb-0">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="cihaz-check-{{ $value->id }}" 
                                               disabled>
                                        <label class="form-check-label" for="cihaz-check-{{ $value->id }}"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <div class="empty-notification-elem">
                        <div class="w-25 w-sm-50 pt-3 mx-auto">
                            <img src="{{ asset('backend/assets/images/svg/bell.svg') }}" class="img-fluid" alt="user-pic">
                        </div>
                        <div class="text-center pb-5 mt-2">
                            <h6 class="fs-18 fw-semibold lh-base">Bildirim Bulunmamakta!</h6>
                        </div>
                    </div>
                    @endforelse
            
                    <div class="my-3 text-center view-all">
                        <a href="{{ route('cihaz.listesi') }}" 
                           class="btn btn-soft-success waves-effect waves-light">
                            Tümünü Görüntüle 
                            <i class="ri-arrow-right-line align-middle"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="notification-actions" id="notification-actions">
                <div class="d-flex text-muted justify-content-center">
                    Seçilen <div id="select-content" class="text-body fw-semibold px-1">0</div> izin
                    <button type="button" class="btn btn-link link-success p-0 ms-3" id="topluOnayBtn">
                        Onayla
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $('#topluOnayBtn').on('click', function() {
        let secilenIzinler = [];
        $('.izin-checkbox:checked').each(function() {
            secilenIzinler.push($(this).val());
        });

        if (secilenIzinler.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: 'Lütfen en az bir izin seçiniz.'
            });
            return;
        }

        Swal.fire({
            title: 'Emin misiniz?',
            text: secilenIzinler.length + " adet izin onaylanacak.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, Onayla',
            cancelButtonText: 'Vazgeç'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('izin.topluOnay') }}",
                    method: "POST",
                    data: {
                        izin_ids: secilenIzinler,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı',
                            text: res.message
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: err.responseJSON.message
                        });
                    }
                });
            }
        });
    });
</script>
