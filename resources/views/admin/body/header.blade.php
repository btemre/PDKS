<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="50">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="50">
                        </span>
                    </a>

                    <a href="index.html" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="50">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('backend/assets/images/logo-light.png') }}" alt=""
                                height="50">
                        </span>
                    </a>
                </div>

                <button type="button"
                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>


            </div>

            <div class="d-flex align-items-center">

       

                <div class="dropdown ms-1 topbar-head-dropdown header-item d-none">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img id="header-lang-img" src="{{ asset('backend/assets/images/flags/tr.svg') }}"
                            alt="Header Language" height="20" class="rounded">
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item language py-2" data-lang="tr"
                            title="Türkçe">
                            <img src="{{ asset('backend/assets/images/flags/tr.svg') }}" alt="user-image"
                                class="me-2 rounded" height="18">
                            <span class="align-middle">Türkçe</span>
                        </a>
                        <!-- item-->

                    </div>
                </div>
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>

                
                @include('admin.body.dgunu')
                @include('admin.body.bilgilendirme')
                @php
                    $id = Auth::user()->id;
                    $profileData = App\Models\User::find($id);
                @endphp
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                src="{{ !empty($profileData->photo) ? url('upload/admin_images/' . $profileData->photo) : url('upload/avatar.png') }}"
                                alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span
                                    class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ $profileData->name }}</span>
                                <span
                                    class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{ $profileData->username }}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Hoş Geldin {{ $profileData->name }}</h6>
                        <a class="dropdown-item" href="{{ route('admin.profile') }}"><i
                                class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Profil</span></a>
                        <a class="dropdown-item" href="{{ route('admin.change.password') }}"><i
                                class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Şifremi Güncelle</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="pages-profile-settings.html"><span
                                class="badge bg-success-subtle text-success mt-1 float-end">29.09.2025</span><i
                                class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">Versiyon</span></a>
                        <a class="dropdown-item" href="{{ route('admin.logout') }}"><i
                                class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle" data-key="t-logout">Çıkış</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
