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
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Anasayfa</a></li>
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
                                    @if (Auth::guard('web')->user()->can('evrak.ekle'))
                                    <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Evrak Ekle</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-dt-evrak"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Evrak No</th>
                                        <th>Tür</th>
                                        <th>Tarih</th>
                                        <th>Çıkış Tarihi</th>
                                        <th>Birimi</th>
                                        <th>Konusu</th>
                                        <th>Sayısı</th>
                                        <th>Açıklama</th>
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
    <!-- Modal -->
    <div id="evrak-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="EvrakForm" id="EvrakForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="evrak_id" id="evrak_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">
                            <div class="col-lg-2">
                                <div>
                                    <label for="evrak_sira" class="form-label">Sıra</label>
                                    <input type="number" id="evrak_sira" class="form-control" name="evrak_sira" disabled />
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div>
                                    <label for="evrak_tur-field" class="form-label">Evrak Türü</label>
                                    <select class="form-select" id="evrak_tur" name="evrak_tur">
                                        <option selected value="">Seçim Yapınız</option>
                                        <option value="Gelen Evrak">Gelen Evrak</option>
                                        <option value="Giden Evrak">Giden Evrak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="evrak_konu" class="form-label">Konu</label>
                                    <input type="text" id="evrak_konu" class="form-control" name="evrak_konu" />
                                </div>
                            </div>


                            <div class="col-lg-4">
                                <div>
                                    <label for="evrak_birim" class="form-label">Kurum/Kuruluş/Birim</label>
                                    <input type="text" id="evrak_birim" class="form-control" name="evrak_birim" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="evrak_sayi" class="form-label">Sayı</label>
                                    <input type="number" id="evrak_sayi" class="form-control" name="evrak_sayi" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="evrak_tarihi" class="form-label">Evrak Tarihi</label>
                                    <input type="date" id="evrak_tarihi" class="form-control" name="evrak_tarihi" />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="evrak_cikistarihi" class="form-label">Evrak Çıkış Tarihi</label>
                                    <input type="date" id="evrak_cikistarihi" class="form-control"
                                        name="evrak_cikistarihi" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="aciklama-field" class="form-label">Açıklama</label>
                                <input type="text" id="evrak_aciklama" class="form-control" name="evrak_aciklama" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-success" id="btn-save">Kaydet</button>
                        </div>
                    </div>
                </form>
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
            // Bu yılın başlangıç ve bitiş tarihlerini al
            var today = new Date().toLocaleDateString('tr-TR');
            var start_date = moment().startOf('year');
            var end_date = moment().endOf('year');
                // Tarih aralığını global olarak sakla
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
                autoUpdateInput: true, // Sayfa yüklendiğinde tarih gözüksün
                startDate: start_date,
                endDate: end_date,
                ranges: {
                    'Bugün': [moment(), moment()],
                    'Dün': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                    'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
                    'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
                    'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Bu Yıl': [moment().startOf('year'), moment().endOf('year')],
                    'Geçen Yıl': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]

                }
            },  function (start, end) {
        dateRange = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
        $('#date_range').val(dateRange);
        table.ajax.reload(); // Tarih değişince tabloyu yenile
    });
 

            let table = $('#ajax-dt-evrak').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('evrak.listesi') }}",
                    data: function (d) {
                        d.date_range = dateRange; // Tarih aralığını global değişkenden al
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {
                        data: null,
                        name: 'evrak_sira',
                        render: function (data, type, row) {
                            if (row.evrak_tarihi && row.evrak_sira) {
                                let yil = new Date(row.evrak_tarihi).getFullYear();
                                return yil + '/' + row.evrak_sira;
                            }
                            return '';
                        }
                    },
                    { data: 'evrak_tur', name: 'evrak_tur' },
                    {
                        data: 'evrak_tarihi',
                        name: 'evrak_tarihi',
                        render: function (data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
                        }
                    },
                    {
                        data: 'evrak_cikistarihi',
                        name: 'evrak_cikistarihi',
                        render: function (data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
                        }
                    },
                    { data: 'evrak_birim', name: 'evrak_birim' },
                    { data: 'evrak_konu', name: 'evrak_konu' },
                    { data: 'evrak_sayi', name: 'evrak_sayi' },
                    { data: 'evrak_aciklama', name: 'evrak_aciklama' },
                    { data: 'action', name: 'action', orderable: false },
                ],
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tümü"]],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength',
                    'excelHtml5',
                    'print'
                ],
                order: [
                    [1, 'desc']
                ]
            });

            // Date Range Temizleme (Boş bırakılırsa tüm veriler gelir)
            $('#date_range').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });
        });
        function add() {
            $('#EvrakForm').trigger("reset");
            $('#EvrakModal').modal('Add Evrak');
            $('#evrak-modal').modal('show');
            $('#evrak_id').val('');
            // Sıra numarasını getir
            $.ajax({
                url: "{{ route('evrak.nextSira') }}",
                type: 'GET',
                success: function (response) {
                    $('#evrak_sira').val(response.sira);
                },
                error: function () {
                    $('#evrak_sira').val('');
                    console.error("Sıra bilgisi alınamadı.");
                }
            });

        }
        function deleteFunc(evrak_id) {
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
                        url: "{{ route('evrak.delete') }}",
                        data: { evrak_id: evrak_id },
                        dataType: 'json',
                        success: function (res) {
                            // DataTable'ı güncelle
                            $('#ajax-dt-evrak').DataTable().ajax.reload();

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
        $('#EvrakForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('evrak.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function (response) {
                    $("#evrak-modal").modal('hide');
                    $('#ajax-dt-evrak').DataTable().ajax.reload(null, false);

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