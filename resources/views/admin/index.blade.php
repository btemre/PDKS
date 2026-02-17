@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Anasayfa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Anasayfa</a></li>
                                <li class ="breadcrumb-item active d-none ">Analytics</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            @if (Auth::guard('web')->user()->can('pdks.menu'))
            <div class="row">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-soft-primary py-2">
                            <h5 class="card-title mb-0"><i class="mdi mdi-account-key me-1"></i> PDKS Hızlı Erişim</h5>
                        </div>
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap gap-2">
                                @can('pdks.bugun')
                                    <a href="{{ route('pdks.bugun') }}" class="btn btn-soft-primary btn-sm">
                                        <i class="mdi mdi-shield-account me-1"></i> Bugün
                                    </a>
                                @endcan
                                @can('pdks.giriscikis')
                                    <a href="{{ route('pdks.giriscikis') }}" class="btn btn-soft-primary btn-sm">
                                        <i class="mdi mdi-exit-run me-1"></i> Giriş Çıkış
                                    </a>
                                @endcan
                                @can('pdks.gecgelen')
                                    <a href="{{ route('pdks.gecgelen') }}" class="btn btn-soft-info btn-sm">
                                        <i class="mdi mdi-account-arrow-up-outline me-1"></i> Geç Gelenler
                                    </a>
                                @endcan
                                @can('pdks.gelmeyen')
                                    <a href="{{ route('pdks.gelmeyen') }}" class="btn btn-soft-warning btn-sm">
                                        <i class="mdi mdi-account-alert-outline me-1"></i> Gelmeyenler
                                    </a>
                                @endcan
                                @can('pdks.rapor')
                                    <a href="{{ route('rapor.sayfa') }}" class="btn btn-soft-success btn-sm">
                                        <i class="mdi mdi-email-send-outline me-1"></i> Mail Raporu
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @include('admin.personelistat')
            @include('admin.gelmeyen')
            @include('admin.gecgelen')
            @include('admin.izinli')
            @include('admin.personelbilgikarti')
            <div class="row">
                <div class="col">
                </div>
            </div>
        </div>
    </div>
@endsection