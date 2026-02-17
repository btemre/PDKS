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
                            {{-- tarih Filtreleme: --}}

                            <div class="row">
                                <div class="col col-sm-10">Tarihe Göre Filtreleme Yapabilirsiniz</div>
                                <div class="col col-sm-2">
                                    <input type="text" id="date_range" class="form-control" readonly />
                                </div>
                            </div>


                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                </div>
                            </div>
                            <table id="ajax-crud-pdksgecislog"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Birim</th>
                                        <th>Statu</th>
                                        <th>Personel</th>
                                        <th>Tarih</th>
                                        <th>Cihaz</th>
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

    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Bugünün tarihi (örnek: 29.07.2025)
            var today = new Date().toLocaleDateString('tr-TR');
            // Bu yılın başlangıç ve bitiş tarihlerini al
            var start_date = moment().startOf('month');
            var end_date = moment().endOf('month');
            // Date Range Picker Tanımlama
            
    // Tarih aralığını global olarak sakla
            var dateRange = start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD');
            $('#date_range').val(dateRange);
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
                    monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos',
                        'Eylül', 'Ekim', 'Kasım', 'Aralık'
                    ],
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
                    'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'Bu Yıl': [moment().startOf('year'), moment().endOf('year')],
                    'Geçen Yıl': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year')
                        .endOf('year')
                    ]
                }
            }, function(start, end) {
                dateRange = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                $('#date_range').val(dateRange);
                table.ajax.reload(); // Tarih değişince tabloyu yenile
            });
            $('#date_range').val(start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD'));
            let table = $('#ajax-crud-pdksgecislog').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pdks.gecislog') }}",
                    data: function(d) {
                        d.date_range = dateRange;
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'birim_ad',
                        name: 'birim_ad'
                    },
                    {
                        data: 'durum_ad',
                        name: 'durum_ad'
                    },
                    {
                        data: 'personel_adsoyad',
                        name: 'personel_adsoyad'
                    },
                    {
                        data: 'gecis_tarihi',
                        name: 'gecis_tarihi',
                        searchable: false,
                        render: function(data) {
                            if (!data) return '';
                            return moment(data).format('DD.MM.YYYY HH:mm');
                        }
                    },
                    {
                        data: 'cihaz_adi',
                        name: 'cihaz_adi'
                    },
                    //{ data: 'action', name: 'action', orderable: false },
                ],
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Tümü"]
                ],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength', // Satır sayısı seçimi
                    'excelHtml5', // Excel'e aktar
                    'print' // Yazdır
                ],
                order: [
                    [4, 'desc'],
                    [5, 'desc']
                ]
            });
            // Tarih aralığı iptal edilirse filtre kaldırılır
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });
        });
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
                    $('#ajax-crud-pdksgecislog').DataTable().ajax.reload(null, false);

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
                    $('#ajax-crud-pdksgecislog').DataTable().ajax.reload(null, false);

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
                    $('#ajax-crud-pdksgecislog').DataTable().ajax.reload(null, false);

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
