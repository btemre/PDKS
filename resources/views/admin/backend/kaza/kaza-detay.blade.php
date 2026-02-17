@extends('admin.admin_dashboard')
@section('title')
    {{ $title }}
@endsection
@section('page-title')
    {{ $pagetitle }}
@endsection
@section('admin')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0"> {{ $title }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Anasayfa</a></li>
                                <li class="breadcrumb-item"><a href="{{route('kaza.listesi')}}">Trafik Kazaları</a></li>
                                <li class="breadcrumb-item active"> {{ $title }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">{{ $pagetitle }}</h4>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="row g-4 mb-3">

                            </div>
                            <table id="ajax-crud-dt-kaza"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th>Plaka</th>
                                        <th>Araç Cinsi</th>
                                        <th>KKNO</th>
                                        <th>KM</th>
                                        <th>Kaza Yeri</th>
                                        <th>Kaza</th>
                                        <th>Vefat</th>
                                        <th>Çarpışma</th>
                                        <th>Devrilme</th>
                                        <th>Cisme Çarpma</th>
                                        <th>Durana Çarpma</th>
                                        <th>Yayaya Çarpma</th>
                                        <th>Araçtan Düşme</th>
                                        <th>Diğer</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Ana Blade dosyanıza (@extends('admin.admin_dashboard') olan dosya) bu modalı ekleyin --}}
{{-- Mevcut #kaza-modal'ın bittiği yerin altına ekleyebilirsiniz. --}}

<div id="kaza-detay-modal" class="modal fade" tabindex="-1" aria-labelledby="kaza-detay-modal-label" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl"> {{-- Daha geniş olması için modal-xl yaptım --}}
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-primary p-3">
                <h5 class="modal-title" id="kaza-detay-modal-label">Kaza Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Kaza bilgileri buraya dinamik olarak gelecek --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Plaka:</dt>
                            <dd class="col-sm-8" id="detay-kaza-plaka"></dd>

                            <dt class="col-sm-4">Tarih / Saat:</dt>
                            <dd class="col-sm-8" id="detay-kaza-tarih-saat"></dd>

                            <dt class="col-sm-4">Kaza Yeri:</dt>
                            <dd class="col-sm-8" id="detay-kaza-yeri"></dd>
                        </dl>
                    </div>
                     <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Vefat / Yaralı:</dt>
                            <dd class="col-sm-8" id="detay-kaza-vefat-yarali"></dd>

                             <dt class="col-sm-4">Açıklama:</dt>
                            <dd class="col-sm-8" id="detay-kaza-aciklama"></dd>
                        </dl>
                    </div>
                </div>

                <hr>
                
                <h5 class="mb-3">Kaza Resimleri</h5>
                {{-- Resimler buraya dinamik olarak eklenecek --}}
                <div id="kaza-resimler-container" class="row g-3">
                    {{-- Örnek: <div class="col-md-3"><img src="..." class="img-fluid rounded"></div> --}}
                </div>
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#ajax-crud-dt-kaza').DataTable({
                processing: true,
                serverSide: true,
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                lengthMenu: [[-1, 25, 50, 100], ["Tümü", 25, 50, 100]],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength',
                    'excelHtml5',
                    'print'
                ],
                ajax: "{{ url('/kaza/detay/' . $yil . '/' . $ay) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {
                        data: 'kaza_tarih',
                        name: 'kaza_tarih',
                        render: function (data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
                        }
                    },
                    {
                        data: 'kaza_saat',
                        name: 'kaza_saat',
                        render: function (data) {
                            return data ? data.slice(0, 5) : '';
                        }
                    },
                    { data: 'kaza_plaka', name: 'kaza_plaka' },
                    { data: 'kaza_arac', name: 'kaza_arac' },
                    { data: 'kaza_kkno', name: 'kaza_kkno' },
                    { data: 'kaza_km', name: 'kaza_km' },
                    { data: 'kaza_yeri', name: 'kaza_yeri' },
                    { data: 'kaza_sayisi', name: 'kaza_sayisi' },
                    { data: 'kaza_vefat', name: 'kaza_vefat' },
                    { data: 'kaza_carpisma', name: 'kaza_carpisma' },
                    { data: 'kaza_devrilme', name: 'kaza_devrilme' },
                    { data: 'kaza_cismecarpma', name: 'kaza_cismecarpma' },
                    { data: 'kaza_duranaracacarpma', name: 'kaza_duranaracacarpma' },
                    { data: 'kaza_yayacarpma', name: 'kaza_yayacarpma' },
                    { data: 'kaza_aractandusme', name: 'kaza_aractandusme' },
                    { data: 'kaza_diger', name: 'kaza_diger' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'desc']]
            });

        });
        function deleteFunc(kaza_id) {
            Swal.fire({
                title: 'Silmek istediğinize emin misiniz?',
                text: "Bu işlem geri alınamaz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Hayır, iptal et'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('kaza.delete') }}",
                        data: { kaza_id: kaza_id },
                        dataType: 'json',
                        success: function (res) {
                            // DataTable'ı güncelle
                            $('#ajax-crud-dt-kaza').DataTable().ajax.reload();

                            // Controller'dan gelen status'e göre mesaj göster
                            Swal.fire({
                                title: res.status === 'success' ? 'Başarılı!' : 'Hata!',
                                text: res.message,  // Controller'dan gelen mesaj
                                icon: res.status === 'success' ? 'success' : 'error',
                                confirmButtonText: 'Tamam'
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: "Hata!",
                                text: xhr.responseJSON?.message || "Bir hata oluştu.",
                                icon: "error",
                                confirmButtonText: "Tamam"
                            });
                        }
                    });
                }
            });
        }
        // Mevcut script'lerinizin olduğu yere, örneğin deleteFunc'tan önce veya sonra ekleyebilirsiniz.

function showDetailsFunc(kaza_id) {
    $.ajax({
        type: "POST",
        url: "{{ route('kaza.show') }}", // Yeni oluşturduğumuz route
        data: { kaza_id: kaza_id },
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                const kaza = res.data;

                // Modal'daki alanları doldur
                $('#detay-kaza-plaka').text(kaza.kaza_plaka || 'Belirtilmemiş');
                $('#detay-kaza-tarih-saat').text(kaza.kaza_tarih + ' / ' + kaza.kaza_saat);
                $('#detay-kaza-yeri').text(kaza.kaza_yeri || 'Belirtilmemiş');
                $('#detay-kaza-vefat-yarali').text(kaza.kaza_vefat + ' Vefat / ' + kaza.kaza_yarali + ' Yaralı');
                $('#detay-kaza-aciklama').text(kaza.kaza_aciklama || 'Açıklama yok.');

                // Resim container'ını temizle ve yeniden doldur
                const resimContainer = $('#kaza-resimler-container');
                resimContainer.empty(); // Önceki resimleri temizle

                if (kaza.resimler && kaza.resimler.length > 0) {
                    $.each(kaza.resimler, function(index, resim) {
                        // Laravel public path'ini doğru şekilde kullanmak için başına / ekliyoruz
                        const resimHtml = `
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <a href="/${resim.resim_yolu}" data-lightbox="kaza-resimleri-${kaza.kaza_id}">
                                    <img src="/${resim.resim_yolu}" class="img-fluid rounded" alt="Kaza Resmi">
                                </a>
                            </div>
                        `;
                        resimContainer.append(resimHtml);
                    });
                } else {
                    resimContainer.html('<div class="col-12"><p class="text-muted">Bu kazaya ait resim bulunamadı.</p></div>');
                }

                // Modalı göster
                $('#kaza-detay-modal').modal('show');
            }
        },
        error: function (xhr) {
            Swal.fire({
                title: "Hata!",
                text: xhr.responseJSON?.message || "Detaylar getirilirken bir hata oluştu.",
                icon: "error",
                confirmButtonText: "Tamam"
            });
        }
    });
}
    </script>
@endsection