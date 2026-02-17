@extends('admin.admin_dashboard')
@section('title')

@endsection
@section('page-title')
@endsection
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ $title }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Anasayfa</a></li>
                                <li class="breadcrumb-item active">{{ $pagetitle }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg">
                <img src="assets/images/profile-bg.jpg" alt="" class="profile-wid-img" />
            </div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
            <div class="row g-4">
                <div class="col-auto">
                    <div class="avatar-lg">
                        <img src="{{ asset(
                            !empty($personel->personel_resim) && file_exists(public_path($personel->personel_resim))
                                ? $personel->personel_resim
                                : 'backend/assets/images/users/kgm.jpg',
                        ) }}" alt="user-img" class="img-thumbnail rounded-circle" />
                    </div>
                </div>
                <!--end col-->
                <div class="col">
                    <div class="p-2">
                        <h3 class="text-white mb-1">{{ $personel->personel_adsoyad }}</h3>
                        <p class="text-white text-opacity-75">{{ $personel->unvan->unvan_ad ?? '-' }}</p>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2"><i class="ri-map-pin-user-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{ $personel->personel_adres }}</div>
                            <div>
                                <i class="ri-building-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>{{ $personel->il->il_ad ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-12 col-lg-auto order-last order-lg-0">
                    <div class="row text text-white-50 text-center">
                        <div class="col-lg-6 col-4">
                            <div class="p-2">
                                <p class="fs-14 mb-0">{{ $personel->personel_kan }}</p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-4">
                            <div class="p-2">
                                <p class="fs-14 mb-0">{{ $personel->personel_derece}}/{{ $personel->personel_kademe }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->

            </div>
            <!--end row-->
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div>
                    <div class="d-flex profile-wrapper">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                    <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span class="d-none d-md-inline-block">Bilgilendirme</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Tab panes -->
                    <div class="tab-content pt-4 text-muted">
                        <div class="tab-pane active" id="overview-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3">Bilgiler</h5>
                                            <div class="table-responsive">
                                                <table class="table table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Ad Soyad:</th>
                                                            <td class="text-muted">{{ $personel->personel_adsoyad }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">TC Kimlik:</th>
                                                            <td class="text-muted">{{ $personel->personel_tc }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Sicil No:</th>
                                                            <td class="text-muted">{{ $personel->personel_sicilno }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Telefon:</th>
                                                            <td class="text-muted">0{{ $personel->personel_telefon }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">E-mail:</th>
                                                            <td class="text-muted">{{ $personel->personel_eposta }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">İşe Giriş:</th>
                                                            <td class="text-muted">{{ tarih($personel->personel_isegiristarih) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Doğum Tarihi:</th>
                                                            <td class="text-muted">{{ tarih($personel->personel_dogumtarihi) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Öğrenim:</th>
                                                            <td class="text-muted">{{ $personel->ogrenim->ogrenim_tur ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Okul:</th>
                                                            <td class="text-muted">{{ $personel->personel_okul ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Adres:</th>
                                                            <td class="text-muted">{{ $personel->personel_adres }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="ps-0" scope="row">Açıklama:</th>
                                                            <td class="text-muted">{{ $personel->personel_aciklama ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div><!-- end card body -->
                                    </div><!-- end card -->
                                </div>
                                <!--end col-->
                                <div class="col-xxl-9">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-header align-items-center d-flex">
                                                    <h4 class="card-title mb-0 me-2">Hareketler</h4>
                                                    <div class="flex-shrink-0 ms-auto">
                                                        <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-bs-toggle="tab" href="#documents" role="tab">
                                                                     Dokümanlar
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-bs-toggle="tab" href="#today" role="tab">
                                                                    Giriş/Çıkış
                                                                </a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-bs-toggle="tab" href="#weekly" role="tab">
                                                                    İzin Durumu
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="tab-content text-muted">
                                                       <!--  Dokümanlar -->
                                                        <div class="tab-pane fade show active" id="documents" role="tabpanel">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="d-flex align-items-center mb-4">
                                                                        <h5 class="card-title flex-grow-1 mb-0">Personel Dokümanları</h5>
                                                                        <div class="flex-shrink-0">
                                                                            <form action="{{ route('personel.dosya.upload', $personel->personel_id) }}" 
                                                                                method="POST" 
                                                                                enctype="multipart/form-data" 
                                                                                id="dosyaForm">
                                                                                @csrf
                                                                                <input class="form-control d-none" 
                                                                                    type="file" 
                                                                                    name="dosya" 
                                                                                    id="formFile" 
                                                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                                                                @if (Auth::guard('web')->user()->can('personel.dosyayukle'))
                                                                                    <label for="formFile" class="btn btn-primary">
                                                                                        <i class="ri-upload-2-fill me-1 align-bottom"></i> Personel Dosyası Yükleyin
                                                                                    </label>
                                                                                @endif
                                                                            </form>
                                                                            <script>
                                                                                document.getElementById('formFile').addEventListener('change', function() {
                                                                                    if (this.files.length > 0) {
                                                                                        document.getElementById('dosyaForm').submit();
                                                                                    }
                                                                                });
                                                                            </script>
                                                                        </div>
                                                                    </div>

                                                                    <div class="table-responsive">
                                                                        <table id="ajax-crud-dosya" 
                                                                            class="table table-bordered dt-responsive nowrap table-striped align-middle" 
                                                                            style="width:100%">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th>Dosya Adı</th>
                                                                                    <th>Tür</th>
                                                                                    <th>Boyut</th>
                                                                                    <th>Tarih</th>
                                                                                    <th class="text-center">İşlem</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @forelse($personel->dosyalar ?? [] as $dosya)
                                                                                    <tr>
                                                                                        <td>
                                                                                            <a href="{{ asset($dosya->dosya_yol) }}" target="_blank" class="fw-semibold text-primary">
                                                                                                {{ $dosya->dosya_ad }}
                                                                                            </a>
                                                                                        </td>
                                                                                        <td>{{ strtoupper($dosya->dosya_tur) }}</td>
                                                                                        <td>{{ $dosya->dosya_boyut }}</td>
                                                                                        <td>{{ tarihsaat($dosya->dosya_tarih) }}</td>
                                                                                        <td class="text-center">
                                                                                            <a href="{{ asset($dosya->dosya_yol) }}" class="btn btn-sm btn-success" download>
                                                                                                <i class="ri-download-2-line"></i> İndir
                                                                                            </a>
                                                                                            @if (Auth::guard('web')->user()->can('personel.dosyagoruntule'))
                                                                                                <a href="{{ asset($dosya->dosya_yol) }}" target="_blank" class="btn btn-sm btn-info">
                                                                                                    <i class="ri-eye-line"></i> Görüntüle
                                                                                                </a>
                                                                                            @endif
                                                                                            <form action="{{ route('personel.dosya.delete', $dosya->dosya_id) }}" 
                                                                                                method="POST" class="d-inline"
                                                                                                onsubmit="return confirm('Bu dosyayı silmek istediğinize emin misiniz?')">
                                                                                                @csrf
                                                                                                @method('DELETE')
                                                                                                @if (Auth::guard('web')->user()->can('personel.dosyasil'))
                                                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                                                        <i class="ri-delete-bin-5-line"></i> Sil
                                                                                                    </button>
                                                                                                @endif
                                                                                            </form>
                                                                                        </td>
                                                                                    </tr>
                                                                                @empty
                                                                                    <tr>
                                                                                        <td colspan="5" class="text-center text-muted">
                                                                                            Henüz dosya yüklenmemiş 📭
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforelse
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--  Giriş/Çıkış -->
                                                        <div class="tab-pane fade" id="today" role="tabpanel">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h4 class="card-title mb-0">Geçişler</h4>
                                                                </div>
                                                                <div class="card-body">
                                                                    <table id="ajax-crud-gecis" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Sıra</th>
                                                                                <th>Tarih</th>
                                                                                <th>Giriş</th>
                                                                                <th>Çıkış</th>
                                                                                <th>Sayı</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($gecisler as $key => $g)
                                                                                <tr>
                                                                                    <td>{{ $key + 1 }}</td>
                                                                                    <td>{{ tarih($g->tarih) }}</td>
                                                                                    <td>{{ $g->giris ? saat($g->giris) : '-' }}</td>
                                                                                    <td>{{ ($g->kayit_sayisi > 1 && $g->cikis) ? saat($g->cikis) : '-' }}</td>
                                                                                    <td>{{ $g->kayit_sayisi }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                
                                                        <!--  İzin Durumu -->
                                                        <div class="tab-pane fade" id="weekly" role="tabpanel">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h4 class="card-title mb-0">İzin Durumları</h4>
                                                                </div>
                                                                <div class="card-body">
                                                                    <table id="ajax-crud-izin" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Sıra</th>
                                                                                <th>İzin</th>
                                                                                <th>Başlayış</th>
                                                                                <th>Bitiş</th>
                                                                                <th>İşe Başlayış</th>
                                                                                <th>Süre</th>
                                                                                <th>Yıl</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($izinler as $key => $izin)
                                                                                <tr>
                                                                                    <td>{{ $key + 1 }}</td>
                                                                                    <td>{{ $izin->izin_ad }}</td>
                                                                                    <td>{{ tarih($izin->izin_baslayis) }}</td>
                                                                                    <td>{{ tarih($izin->izin_bitis) }}</td>
                                                                                    <td>{{ tarih($izin->izin_isebaslayis) }}</td>
                                                                                    <td>{{ $izin->izin_suresi }}</td>
                                                                                    <td>{{ $izin->izin_yil }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                
                                                    </div><!-- end tab-content -->
                                                </div><!-- end card-body -->
                                            </div><!-- end card -->
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div>
                                
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>                        
                        <!--end tab-pane-->
                        
                        
                        <!--end tab-pane-->
                    </div>
                    <!--end tab-content-->
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#ajax-crud-dosya').DataTable({
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                responsive: true,
                processing: true,
                pageLength: 10,
                lengthMenu: [[10], [10]],
                order: [
                    [0, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: ['pageLength','excelHtml5','print'],
            });
        });
        $(document).ready(function() {
            $('#ajax-crud-izin').DataTable({
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                responsive: true,
                processing: true,
                pageLength: 10,
                lengthMenu: [[10], [10]],
                order: [
                    [0, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: ['pageLength','excelHtml5','print'],
            });
        });
        $(document).ready(function() {
            $('#ajax-crud-gecis').DataTable({
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                responsive: true,
                processing: true,
                pageLength: 10,
                lengthMenu: [[10], [10]],
                order: [
                    [0, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: ['pageLength','excelHtml5','print'],
            });
        });
    </script>
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Hata',
            text: '{{ session('error') }}'
        });
    </script>
    @endif
    
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Başarılı',
            text: '{{ session('success') }}'
        });
    </script>
    @endif
    
@endsection
