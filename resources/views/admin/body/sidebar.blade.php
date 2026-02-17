<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/admin/dashboard" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="60">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="" height="60">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/admin/dashboard" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="" height="60">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('backend/assets/images/logo-light.png') }}" alt="" height="60">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                @php
                    date_default_timezone_set('Europe/Istanbul'); // Türkiye saati
                    $hour = now()->format('H');
                    if ($hour >= 6 && $hour < 12) {
                        $greeting = 'Günaydın';
                    } elseif ($hour >= 12 && $hour < 18) {
                        $greeting = 'İyi Günler';
                    } elseif ($hour >= 18 && $hour < 22) {
                        $greeting = 'İyi Akşamlar';
                    } else {
                        $greeting = 'İyi Geceler';
                    }
                @endphp
                <li class="menu-title text-center">
                    <span data-key="t-menu">{{ $greeting }}</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="mdi mdi-home"></i> <span data-key="t-home">Anasayfa</span>
                    </a>
                </li>
                @if (Auth::guard('web')->user()->can('trafik.menu'))
                    <!-- <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('kaza.listesi') ? 'active' : '' }}"
                            href="{{ route('kaza.listesi') }}">
                            <i class="mdi mdi-car-cog"></i> <span data-key="t-kaza">Trafik Kazaları</span>
                        </a>
                    </li> -->
                @endif
                @if (Auth::guard('web')->user()->can('pdks.menu'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPdks" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('pdks.*') || request()->routeIs('rapor.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarPdks">
                            <i class="mdi mdi-account-key"></i> <span data-key="t-pdks">PDKS İşlemleri</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->routeIs('pdks.*') || request()->routeIs('rapor.*') ? 'show' : '' }}" id="sidebarPdks">
                            <ul class="nav nav-sm flex-column">
                                @can('pdks.bugun')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('pdks.bugun') ? 'active' : '' }}" href="{{ route('pdks.bugun') }}">
                                            <i class="mdi mdi-shield-account"></i> <span data-key="t-pdksbugun">Bugün</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('pdks.giriscikis')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('pdks.giriscikis') ? 'active' : '' }}" href="{{ route('pdks.giriscikis') }}">
                                            <i class="mdi mdi-exit-run"></i> <span data-key="t-pdksgiriscikis">Giriş Çıkış</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('pdks.gecgelen')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('pdks.gecgelen') ? 'active' : '' }}" href="{{ route('pdks.gecgelen') }}">
                                            <i class="mdi mdi-account-arrow-up-outline"></i> <span data-key="t-pdksgecgelen">Geç Gelenler</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('pdks.erkencikan')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('pdks.erkencikan') ? 'active' : '' }}" href="{{ route('pdks.erkencikan') }}">
                                            <i class="mdi mdi-account-arrow-down-outline"></i> <span data-key="t-pdkserkencikan">Erken Çıkanlar</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('pdks.gelmeyen')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('pdks.gelmeyen') ? 'active' : '' }}" href="{{ route('pdks.gelmeyen') }}">
                                            <i class="mdi mdi-account-alert-outline"></i> <span data-key="t-pdksgelmeyen">Gelmeyenler</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('pdks.gecislog')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('pdks.gecislog') ? 'active' : '' }}" href="{{ route('pdks.gecislog') }}">
                                            <i class="mdi mdi-account-question-outline"></i> <span data-key="t-pdksgecislog">Cihaz Kayıtları</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('pdks.hareket')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('pdks.hareket') ? 'active' : '' }}" href="{{ route('pdks.hareket') }}">
                                            <i class="mdi mdi-account-alert-outline"></i> <span data-key="t-pdkshareket">Tüm Hareketler</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('pdks.rapor')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('rapor.sayfa') ? 'active' : '' }}" href="{{ route('rapor.sayfa') }}">
                                            <i class="mdi mdi-email-send-outline"></i> <span data-key="t-gunlukrapor">Mail Raporu Gönder</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::guard('web')->user()->can('personel.menu'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPersonel" data-bs-toggle="collapse"
                            role="button" aria-expanded="{{ request()->routeIs('personel.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarPersonel">
                            <i class="mdi mdi-account-group"></i> <span data-key="t-personel">Personel</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->routeIs('personel.*') ? 'show' : '' }}"
                            id="sidebarPersonel">
                            <ul class="nav nav-sm flex-column">
                                @if (Auth::guard('web')->user()->can('personel.liste'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('personel.listesi') ? 'active' : '' }}"
                                            href="{{ route('personel.listesi') }}">
                                            <i class="mdi mdi-account-circle-outline"></i> <span
                                                data-key="t-personel">Personel İşlemi</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::guard('web')->user()->can('personel.izin'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('personel.izin') ? 'active' : '' }}"
                                            href="{{ route('personel.izin') }}">
                                            <i class="mdi mdi-printer-settings"></i> <span
                                                data-key="t-izin">İzin/Mazeret
                                                İşlemi</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::guard('web')->user()->can('personel.izinkullanim'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('personel.izinkullanim') ? 'active' : '' }}"
                                            href="{{ route('personel.izinkullanim') }}">
                                            <i class="mdi mdi-account-details"></i> <span
                                                data-key="t-izinkullanim">İzinli
                                                Listesi</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::guard('web')->user()->can('personel.kartlistesi'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('personel.kartlistesi') ? 'active' : '' }}"
                                            href="{{ route('personel.kartlistesi') }}">
                                            <i class="mdi mdi-account-multiple-remove"></i> <span
                                                data-key="t-kartlistesi">Kart (ID) İşlemleri</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::guard('web')->user()->can('evrak.menu'))
                    <!-- <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarEvrak" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('evrak.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarEvrak">
                            <i class="mdi mdi-file-document"></i> <span data-key="t-evrak">Evrak İşlemleri</span>
                        </a>
                        @if (Auth::guard('web')->user()->can('evrak.liste'))
                            <div class="collapse menu-dropdown {{ request()->routeIs('evrak.*') ? 'show' : '' }}"
                                id="sidebarEvrak">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('evrak.listesi') ? 'active' : '' }}"
                                            href="{{ route('evrak.listesi') }}">
                                            <i class="mdi mdi-call-split"></i> <span data-key="t-evrak">Gelen-Giden
                                                Evrak</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </li> -->
                @endif
                @if (Auth::guard('web')->user()->can('tunel.menu'))
                <!-- <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarTunel" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ request()->routeIs('jetfan.*') || request()->routeIs('jenerator.*') ? 'true' : 'false' }}"
                        aria-controls="sidebarTunel">
                        <i class="mdi mdi-tunnel-outline"></i> 
                        <span data-key="t-tunel">Elektrik/Elektronik</span>
                    </a>
                    <div class="collapse menu-dropdown {{ request()->routeIs('jetfan.*') || request()->routeIs('jenerator.*') ? 'show' : '' }}"
                        id="sidebarTunel">
                        <ul class="nav nav-sm flex-column">
                            @if (Auth::guard('web')->user()->can('jetfan.menu'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('jetfan.listesi') ? 'active' : '' }}"
                                        href="{{ route('jetfan.listesi') }}">
                                        <i class="mdi mdi-fan"></i> 
                                        <span data-key="t-jetfan">Jetfan İşlemi</span>
                                    </a>
                                </li>
                            @endif
            
                            @if (Auth::guard('web')->user()->can('jenerator.menu'))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('jenerator.listesi') ? 'active' : '' }}"
                                        href="{{ route('jenerator.listesi') }}">
                                        <i class="mdi mdi-engine-outline"></i> 
                                        <span data-key="t-jenerator">Jeneratör İşlemi</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li> -->
            @endif
            
                @if (Auth::guard('web')->user()->can('tanim.menu'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTanim" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('cihaz.*') || request()->routeIs('arac.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarTanim">
                            <i class="mdi mdi-cube-outline"></i>
                            <span data-key="t-tanim">Tanımlama İşlemleri</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->routeIs('cihaz.*') || request()->routeIs('arac.*') ? 'show' : '' }}"
                            id="sidebarTanim">
                            <ul class="nav nav-sm flex-column">

                                @if (Auth::guard('web')->user()->can('cihaz.menu'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('cihaz.listesi') ? 'active' : '' }}"
                                            href="{{ route('cihaz.listesi') }}">
                                            <i class="mdi mdi-devices"></i>
                                            <span data-key="t-cihaz">Cihaz Tanımlama</span>
                                        </a>
                                    </li>
                                @endif

                                <!--@if (Auth::guard('web')->user()->can('arac.menu'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('arac.listesi') ? 'active' : '' }}"
                                            href="{{ route('arac.listesi') }}">
                                            <i class="mdi mdi-car"></i>
                                            <span data-key="t-arac">Araç Tanımlama</span>
                                        </a>
                                    </li>
                                @endif-->

                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::guard('web')->user()->can('yetkilendirme.menu'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPermission" data-bs-toggle="collapse"
                            role="button"
                            aria-expanded="{{ request()->routeIs('*.permission') || request()->routeIs('*.roles') || request()->routeIs('*.roles.permission') ? 'true' : 'false' }}"
                            aria-controls="sidebarPermission">
                            <i class="mdi mdi-account-cog-outline"></i>
                            <span data-key="t-permission">Yetkilendirme İşlemleri</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->routeIs('*.permission') || request()->routeIs('*.roles') || request()->routeIs('*.roles.permission') ? 'show' : '' }}"
                            id="sidebarPermission">
                            <ul class="nav nav-sm flex-column">

                                {{-- All Permission --}}
                                @if (Auth::guard('web')->user()->can('yetkilendirme.menu'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('*.permission') && !request()->routeIs('*.roles.permission') ? 'active' : '' }}"
                                            href="{{ route('all.permission') }}">
                                            <i class="mdi mdi-key-outline"></i>
                                            <span data-key="t-all-permission">Yetki İzinleri</span>
                                        </a>
                                    </li>
                                @endif
                                {{-- All Roles --}}
                                @if (Auth::guard('web')->user()->can('yetkilendirme.rol.menu'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('*.roles') && !request()->routeIs('*.roles.permission') ? 'active' : '' }}"
                                            href="{{ route('all.roles') }}">
                                            <i class="mdi mdi-shield-account-outline"></i>
                                            <span data-key="t-all-roles">Tüm Roller</span>
                                        </a>
                                    </li>
                                @endif
                                {{-- Add Role Permission --}}
                                @if (Auth::guard('web')->user()->can('yetkilendirme.rolizin.menu'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('add.roles.permission') ? 'active' : '' }}"
                                            href="{{ route('add.roles.permission') }}">
                                            <i class="mdi mdi-link-plus"></i>
                                            <span data-key="t-add-rolespermission">Rol izinleri Tanımlama</span>
                                        </a>
                                    </li>
                                @endif

                                {{-- All Role Permissions --}}
                                @if (Auth::guard('web')->user()->can('yetkilendirme.rolizin.menu'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('all.roles.permission') ? 'active' : '' }}"
                                            href="{{ route('all.roles.permission') }}">
                                            <i class="mdi mdi-link-lock"></i>
                                            <span data-key="t-all-rolespermission">Tüm Rol İzinleri</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::guard('web')->user()->can('kullanici.menu'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarAdmin" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('*.admin') || request()->routeIs('*.roles') || request()->routeIs('*.roles.admin') ? 'true' : 'false' }}"
                            aria-controls="sidebarAdmin">
                            <i class="mdi mdi-account-convert"></i>
                            <span data-key="t-admin">Kullanıcı İşlemleri</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->routeIs('*.admin') || request()->routeIs('*.roles') || request()->routeIs('*.roles.admin') ? 'show' : '' }}"
                            id="sidebarAdmin">
                            <ul class="nav nav-sm flex-column">
                                {{-- All Admin --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('*.admin') && !request()->routeIs('*.roles.admin') ? 'active' : '' }}"
                                        href="{{ route('all.admin') }}">
                                        <i class="mdi mdi-account-key"></i>
                                        <span data-key="t-all-admin">Kullanıcılar</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
                @if (Auth::guard('web')->user()->can('bilgisayar.menu'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarBilgisayar" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('bilgisayar.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarBilgisayar">
                            <i class="mdi mdi-server"></i>
                            <span data-key="t-bilgisayar">Envanter İşlemleri</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->routeIs('bilgisayar.*') ? 'show' : '' }}"
                            id="sidebarBilgisayar">
                            <ul class="nav nav-sm flex-column">
                                {{-- Bilgisayar Listesi --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('bilgisayar.listesi') ? 'active' : '' }}"
                                        href="{{ route('bilgisayar.listesi') }}">
                                        <i class="mdi mdi-laptop"></i>
                                        <span data-key="t-all-bilgisayar">Bilgisayar</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif

                @if (Auth::guard('web')->user()->can('ayar.menu'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarAyar" data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ request()->routeIs('ayar.*') || request()->routeIs('arac.*') ? 'true' : 'false' }}"
                            aria-controls="sidebarAyar">
                            <i class="mdi mdi-speedometer"></i>
                            <span data-key="t-ayar">Modül Ayarları</span>
                        </a>

                        <div class="collapse menu-dropdown {{ request()->routeIs('ayar.*') || request()->routeIs('birim.*') ? 'show' : '' }}"
                            id="sidebarAyar">
                            <ul class="nav nav-sm flex-column">

                                @if (Auth::guard('web')->user()->can('ayar.menu'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('ayar.listesi') ? 'active' : '' }}"
                                            href="{{ route('ayar.listesi') }}">
                                            <i class="mdi mdi-devices"></i>
                                            <span data-key="t-ayar">Genel Ayarlar</span>
                                        </a>
                                    </li>
                                @endif

                                @if (Auth::guard('web')->user()->can('birim.menu'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('birim.listesi') ? 'active' : '' }}"
                                            href="{{ route('birim.listesi') }}">
                                            <i class="mdi mdi-car"></i>
                                            <span data-key="t-birim">Birim Tanımlama</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::guard('web')->user()->can('yedek.al'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('ayar.backup') ? 'active' : '' }}"
                                            href="{{ route('ayar.backup') }}">
                                            <i class="mdi mdi-database"></i>
                                            <span data-key="t-birim">Veritabanını Yedekle</span>
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
