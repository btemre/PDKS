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
                        <h4 class="mb-sm-0">{{ $pagetitle }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Anasayfa</a></li>
                                <li class="breadcrumb-item active">{{ $pagetitle }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-header">
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
                            <table id="ajax-dt-izin"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Ad Soyad</th>
                                        <th>Statü</th>
                                        <th>Ünvan</th>
                                        <th>Dönem</th>
                                        <th>İzin Tür</th>
                                        <th>İzin Başlama</th>
                                        <th>İzin Bitiş</th>
                                        <th>İşe Başlama</th>
                                        <th>Süresi</th>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Bu yılın başlangıç ve bitiş tarihlerini al
            var start_date = moment().startOf('year');
            var end_date = moment().endOf('year');
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
                    monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos',
                        'Eylül', 'Ekim', 'Kasım', 'Aralık'
                    ],
                    firstDay: 1
                },
                autoUpdateInput: true, // Sayfa yüklendiğinde tarih gözüksün
                startDate: start_date,
                endDate: end_date,
                ranges: {
                    'Bugün': [moment(), moment()],
                    'Dün': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf(
                        'day')],
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
                $('#date_range').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                table.ajax.reload(); // Tarih seçildiğinde tabloyu yenile
            });
            // Varsayılan olarak bu yılın tarihlerini inputa yaz
            $('#date_range').val(start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD'));

            let table = $('#ajax-dt-izin').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('personel.izinonay') }}",
                    data: function(d) {
                        d.date_range = $('#date_range')
                            .val(); // Seçilen tarih aralığını AJAX isteğine ekler
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'personel_adsoyad',
                        name: 'personel_adsoyad'
                    },
                    {
                        data: 'durum_ad',
                        name: 'durum.durum_ad'
                    },
                    {
                        data: 'unvan_ad',
                        name: 'unvan.unvan_ad'
                    },
                    {
                        data: 'izin_yil',
                        name: 'izin.izin_yil'
                    },
                    {
                        data: 'izin_ad',
                        name: 'izin_turleri.izin_ad'
                    },
                    {
                        data: 'izin_baslayis',
                        name: 'izin.izin_baslayis',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g,
                                '-') : '';
                        }
                    },
                    {
                        data: 'izin_bitis',
                        name: 'izin.izin_bitis',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g,
                                '-') : '';
                        }
                    },
                    {
                        data: 'izin_isebaslayis',
                        name: 'izin.izin_isebaslayis',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g,
                                '-') : '';
                        }
                    },
                    {
                        data: 'izin_suresi',
                        name: 'izin.izin_suresi'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                order: [
                    [7, 'desc']
                ]
            });

            // Date Range Temizleme (Boş bırakılırsa tüm veriler gelir)
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });
        });
        $('#IzinForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('izin.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#izin-modal").modal('hide');
                    $('#ajax-dt-izin').DataTable().ajax.reload(null, false);

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
        function onayFunc(izin_id) {
            Swal.fire({
                title: "Emin misiniz?",
                text: "Bu izni onaylamak istiyor musunuz?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Evet, Onayla",
                cancelButtonText: "Vazgeç"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('izin.onayla') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            izin_id: izin_id
                        },
                        success: function(response) {
                            $('#ajax-dt-izin').DataTable().ajax.reload(null, false);

                            Swal.fire({
                                title: "Başarılı!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "Tamam"
                            });
                        },
                        error: function(xhr) {
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
        $('#topluOnayBtn').on('click', function() {
            if (selectedIds.length === 0) {
                Swal.fire("Uyarı", "Onaylamak için en az bir izin seçin!", "warning");
                return;
            }

            Swal.fire({
                title: "Emin misiniz?",
                text: selectedIds.length + " izin onaylanacak.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Evet, Onayla",
                cancelButtonText: "Vazgeç"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('izin.topluOnay') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            izin_ids: selectedIds
                        },
                        success: function(response) {
                            Swal.fire("Başarılı", response.message, "success");
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire("Hata", xhr.responseJSON?.message || "Bir hata oluştu.",
                                "error");
                        }
                    });
                }
            });
        });
        let selectedIds = [];
        $(document).on('change', '.izin-checkbox', function() {
            let izinId = $(this).val();
            if ($(this).is(':checked')) {
                selectedIds.push(izinId);
            } else {
                selectedIds = selectedIds.filter(id => id !== izinId);
            }
            $('#select-content').text(selectedIds.length);
        });
    </script>
@endsection
