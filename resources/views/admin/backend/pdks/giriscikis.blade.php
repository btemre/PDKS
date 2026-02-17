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
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Anasayfa</a></li>
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
                            <div class="row align-items-center g-2 flex-wrap">
                                <div class="col-auto">
                                    <span class="text-muted me-2">Tarih:</span>
                                </div>
                                <div class="col-auto">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary pdks-quick-range" data-range="bugun">Bugün</button>
                                        <button type="button" class="btn btn-outline-primary pdks-quick-range" data-range="dun">Dün</button>
                                        <button type="button" class="btn btn-outline-primary pdks-quick-range active" data-range="bu_hafta">Bu Hafta</button>
                                        <button type="button" class="btn btn-outline-primary pdks-quick-range" data-range="bu_ay">Bu Ay</button>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <input type="text" id="date_range" class="form-control form-control-sm" readonly style="min-width: 220px;" />
                                </div>
                                <div class="col-auto ms-auto">
                                    <a href="#" id="pdks-export-excel" class="btn btn-success btn-sm"><i class="mdi mdi-file-excel me-1"></i> Excel'e Aktar</a>
                                </div>
                            </div>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
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
                                    <a class="btn btn-primary" onclick="addizinmazeret()" href="javascript:void(0)">Saatlik
                                        İzin Ekle</a>
                                        @endif
                                </div>
                            </div>
                            <table id="ajax-crud-pdksgiriscikis"
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
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Varsayılan: Bu Hafta (Pazartesi–Pazar)
    var start_date = moment().startOf('week'); // locale firstDay: 1 => Pazartesi
    var end_date = moment().endOf('week');
    var dateRange = start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD');
    $('#date_range').val(dateRange);

    // Date Range Picker Tanımlama
    $('#date_range').daterangepicker({
        locale: {
            applyLabel: 'Aralığı Seç',
            cancelLabel: 'Vazgeç',
            format: 'DD-MM-YYYY',
            customRangeLabel: 'Kendim Seçeceğim',
            separator: ' & ',
            fromLabel: 'From',
            toLabel: '&',
            weekLabel: 'W',
            daysOfWeek: ['Pzr', 'Pts', 'Sal', 'Çar', 'Per', 'Cum', 'Cts'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        },
        autoUpdateInput: true,
        startDate: start_date,
        endDate: end_date,
        ranges: {
            'Bugün': [moment(), moment()],
            'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
            'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
            'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Bu Yıl': [moment().startOf('year'), moment().endOf('year')],
            'Geçen Yıl': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        }
    }, function (start, end) {
        dateRange = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
        $('#date_range').val(dateRange);
        $('.pdks-quick-range').removeClass('active');
        table.ajax.reload();
    });

    // Hızlı filtre butonları
    $('.pdks-quick-range').on('click', function () {
        var range = $(this).data('range');
        var s, e;
        if (range === 'bugun') {
            s = moment(); e = moment();
        } else if (range === 'dun') {
            s = moment().subtract(1, 'days'); e = s.clone();
        } else if (range === 'bu_hafta') {
            s = moment().startOf('week'); e = moment().endOf('week');
        } else if (range === 'bu_ay') {
            s = moment().startOf('month'); e = moment().endOf('month');
        } else {
            return;
        }
        dateRange = s.format('YYYY-MM-DD') + ' - ' + e.format('YYYY-MM-DD');
        $('#date_range').val(dateRange);
        $('#date_range').data('daterangepicker').setStartDate(s);
        $('#date_range').data('daterangepicker').setEndDate(e);
        $('.pdks-quick-range').removeClass('active');
        $(this).addClass('active');
        table.ajax.reload();
    });

    // Excel'e Aktar butonu (header)
    $('#pdks-export-excel').on('click', function (e) {
        e.preventDefault();
        var url = "{{ route('pdks.giriscikis.export') }}?date_range=" + encodeURIComponent(dateRange);
        window.location.href = url;
    });

    // DataTable Tanımlama
    let table = $('#ajax-crud-pdksgiriscikis').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('pdks.giriscikis') }}",
            data: function (d) {
                d.date_range = dateRange;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'birim_ad', name: 'birim.birim_ad' },
            { data: 'durum_ad', name: 'durum.durum_ad' },
            { data: 'personel_adsoyad', name: 'personel.personel_adsoyad' },
            {
                data: 'tarih',
                name: 'tarih',
                searchable: false,
                render: function (data) {
                    return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
                }
            },
            {
                data: 'giris',
                name: 'giris',
                searchable: false,
                render: function (data, type, row) {
                    if (!data) return '';
                    const girisSaat = data.slice(11, 16);
                    const mesaiGiris = row.mesai_giris ? row.mesai_giris.slice(0, 5) : null;
                    if (mesaiGiris && girisSaat > mesaiGiris) {
                        return '<span class="text-danger">' + girisSaat + '</span>';
                    }
                    return '<span class="text-success">' + girisSaat + '</span>';
                }
            },
            {
                data: 'cikis',
                name: 'cikis',
                searchable: false,
                render: function (data, type, row) {
                    const girisData = row.giris;
                    const isRecordToday = function (girisStr) {
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
                        return '<span class="text-danger">' + cikisSaat + '</span>';
                    }
                    return '<span class="text-success">' + cikisSaat + '</span>';
                }
            },
            { data: 'action', name: 'action', orderable: false },
        ],
        language: {
            url: '{{ url('build/json/datatabletr.json') }}',
            emptyTable: 'Bu tarih aralığı için kayıt bulunamadı.',
            zeroRecords: 'Bu tarih aralığı için kayıt bulunamadı.'
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tümü"]],
        dom: 'Bfrtip',
        buttons: [
            'pageLength',
            {
                extend: 'excelHtml5',
                text: '<i class="mdi mdi-file-excel"></i> Excel',
                title: 'GirisCikis',
                action: function (e, dt, button, config) {
                    var url = "{{ route('pdks.giriscikis.export') }}?date_range=" + encodeURIComponent(dateRange);
                    window.location.href = url;
                }
            },
            'print'
        ],
        order: [[4, 'desc'], [5, 'desc']]
    });

    // Tarih aralığı iptal edilirse filtre kaldırılır
    $('#date_range').on('cancel.daterangepicker', function (ev, picker) {
        dateRange = '';
        $(this).val('');
        table.ajax.reload();
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
        $('#PersonelForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('personel.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function (response) {
                    $("#personel-modal").modal('hide');
                    $('#ajax-crud-pdksgiriscikis').DataTable().ajax.reload(null, false);

                    Swal.fire({
                        title: "Başarılı!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                },
                error: function (xhr) {
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
        $('#IzinMazeretForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('izinmazeret.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function (response) {
                    $("#izinmazeret-modal").modal('hide');
                    $('#ajax-crud-pdksgiriscikis').DataTable().ajax.reload(null, false);

                    Swal.fire({
                        title: "Başarılı!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                },
                error: function (xhr) {
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
        $('#PdksGecisForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('pdksgecisekle.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function (response) {
                    $("#pdksgecis-modal").modal('hide');
                    $('#ajax-crud-pdksgiriscikis').DataTable().ajax.reload(null, false);

                    Swal.fire({
                        title: "Başarılı!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "Tamam"
                    });

                    $("#btn-save").html('Kaydet').attr("disabled", false);
                },
                error: function (xhr) {
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