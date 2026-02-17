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
                        <h4 class="mb-sm-0">{{ $title }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Anasayfa</a></li>
                                <li class="breadcrumb-item active">{{ $title }}</li>
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
                            <p class="small text-muted d-md-none mb-2"><i class="mdi mdi-information-outline me-1"></i> Mobilde tabloyu yatay kaydırarak tüm sütunları görebilirsiniz.</p>
                            <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                    @if (Auth::guard('web')->user()->can('pdks.gecisekle'))
                                        <a class="btn btn-success" onclick="addpdksgecis()" href="javascript:void(0)">Geçiş
                                            Ekle</a>
                                    @endif
                                    @if (Auth::guard('web')->user()->can('pdks.izinekle'))
                                        <a class="btn btn-primary" onclick="addizinmazeret()"
                                            href="javascript:void(0)">Saatlik
                                            İzin Ekle</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-crud-pdksbugun"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Birim</th>
                                        <th>Statu</th>
                                        <th>Personel</th>
                                        <th>Tarih</th>
                                        <th>Giriş</th>
                                        <th>Çıkış</th>
                                        <th>Açıklama</th>
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
    <!-- Modal -->
    @include('admin.backend.modal.izinmazeretaciklamamodal')
    @include('admin.backend.modal.izinmazereteklemodal')
    @include('admin.backend.modal.pdksgeciseklemodal')
    <script type="text/javascript">
        $('#company-logo-input').change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $('#companylogo-img').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#ajax-crud-pdksbugun').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pdks.bugun') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // Sıralama için eklendi
                    {
                        data: 'birim_ad',
                        name: 'birim.birim_ad'
                    },
                    {
                        data: 'durum_ad',
                        name: 'durum.durum_ad'
                    },
                    {
                        data: 'personel_adsoyad',
                        name: 'personel.personel_adsoyad'
                    },

                    {
                        data: 'tarih',
                        name: 'tarih',
                        searchable: false,
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g,
                                '-') : '';
                        }
                    },
                    {
                        data: 'giris',
                        name: 'giris',
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data) return '';

                            const girisSaat = data.slice(11, 16);
                            const mesaiGiris = row.mesai_giris ? row.mesai_giris.slice(0, 5) : null;

                            if (mesaiGiris && girisSaat > mesaiGiris) {
                                return '<span class="text-danger">' + girisSaat +
                                '</span>'; // Geç geldi
                            }

                            return '<span class="text-success">' + girisSaat +
                            '</span>'; // Zamanında geldi
                        }
                    },
                    {
                        data: 'cikis',
                        name: 'cikis',
                        searchable: false,
                        render: function(data, type, row) {
                            const girisData = row.giris;
                            const isRecordToday = function(girisStr) {
                                if (!girisStr) return false;
                                const now = new Date();
                                const giris = new Date(girisStr);
                                return now.getFullYear() === giris.getFullYear() &&
                                    now.getMonth() === giris.getMonth() &&
                                    now.getDate() === giris.getDate();
                            };

                            // Çıkış yok veya mükerrer kart (3 dk içinde)
                            if (!data) {
                                return isRecordToday(girisData)
                                    ? '<span class="badge bg-dark">Çıkış Bekleniyor</span>'
                                    : 'Çıkış Yapılmadı';
                            }
                            if (girisData) {
                                const girisZaman = new Date(girisData);
                                const cikisZaman = new Date(data);
                                const farkDakika = (cikisZaman - girisZaman) / (1000 * 60);
                                if (farkDakika < 3) {
                                    return isRecordToday(girisData)
                                        ? '<span class="badge bg-dark">Çıkış Bekleniyor</span>'
                                        : 'Çıkış Yapılmadı';
                                }
                            }

                            const cikisSaat = data.slice(11, 16);
                            const mesaiCikis = row.mesai_cikis ? row.mesai_cikis.slice(0, 5) : null;

                            if (mesaiCikis && cikisSaat < mesaiCikis) {
                                return '<span class="text-danger">' + cikisSaat +
                                '</span>'; // Erken çıktı
                            }

                            return '<span class="text-success">' + cikisSaat +
                            '</span>'; // Zamanında çıktı
                        }
                    },
                    //{ data: 'izinmazeret_aciklama', name: 'izin_mazeret.izinmazeret_aciklama' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}',
                    emptyTable: 'Bu tarih için kayıt bulunamadı.',
                    zeroRecords: 'Bu tarih için kayıt bulunamadı.'
                },
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tümü"]],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength',
                    'excelHtml5',
                    'print'
                ],
                order: [
                    [5, 'desc']
                ]
            });
        });

        function add() {
            $('#PersonelForm').trigger("reset");
            $('#PersonelModal').modal('Add Personel');
            $('#personel-modal').modal('show');
            $('#personel_id').val('');
            $('#companylogo-img').attr('src', '/upload/avatar.png'); // resim önizlemesini varsayılana döndür
            $('#company-logo-input').val(''); // file inputu sıfırla
        }

        function addizinmazeret() {
            $('#IzinMazeretForm').trigger("reset");
            $('#IzinMazeretModal').modal('Add IzinMazeret');
            $('#izinmazeret-modal').modal('show');
            $('#izinmazeret_id').val('');
            //$('#companylogo-img').attr('src', '/upload/avatar.png'); // resim önizlemesini varsayılana döndür
            $('#company-logo-input').val(''); // file inputu sıfırla

        }

        function addpdksgecis() {
            $('#PdksGecisForm').trigger("reset");
            $('#PdksGecisModal').modal('Add PdksGecis');
            $('#pdksgecis-modal').modal('show');
            $('#pdksgecis_id').val('');
            //$('#companylogo-img').attr('src', '/upload/avatar.png'); // resim önizlemesini varsayılana döndür
            $('#company-logo-input').val(''); // file inputu sıfırla

        }

        function showIzinModal(ad, tarih, tur, baslangic, bitis, aciklama) {
            $('#modal-isim').text(ad);
            $('#modal-tarih').text(tarih);
            $('#modal-izin-turu').text(tur);
            $('#modal-baslangic').text(baslangic);
            $('#modal-bitis').text(bitis);
            $('#modal-aciklama').text(aciklama);
            $('#izin-detay-modal').modal('show');
        }
        $('#PersonelForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('personel.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#personel-modal").modal('hide');
                    $('#ajax-crud-pdksbugun').DataTable().ajax.reload(null, false);

                    Swal.fire({
                        title: "Başarılı!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                },
                error: function(xhr) {
                    Swal.fire({
                        title: "Hata!",
                        text: xhr.responseJSON?.message || "Bir hata oluştu.",
                        icon: "error",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                }
            });
        });
        $('#IzinMazeretForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('izinmazeret.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#izinmazeret-modal").modal('hide');
                    $('#ajax-crud-pdksbugun').DataTable().ajax.reload(null, false);

                    Swal.fire({
                        title: "Başarılı!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                },
                error: function(xhr) {
                    Swal.fire({
                        title: "Hata!",
                        text: xhr.responseJSON?.message || "Bir hata oluştu.",
                        icon: "error",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                }
            });
        });
        $('#PdksGecisForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('pdksgecisekle.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#pdksgecis-modal").modal('hide');
                    $('#ajax-crud-pdksbugun').DataTable().ajax.reload(null, false);

                    Swal.fire({
                        title: "Başarılı!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                },
                error: function(xhr) {
                    Swal.fire({
                        title: "Hata!",
                        text: xhr.responseJSON?.message || "Bir hata oluştu.",
                        icon: "error",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                }
            });
        });
    </script>
@endsection
