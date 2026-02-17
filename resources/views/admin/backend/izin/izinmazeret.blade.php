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
                                <li class="breadcrumb-item"><a href="{{route('personel.izin')}}">İzin</a></li>
                                <li class="breadcrumb-item active">Saatlik İzin İşlemleri</li>
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

                                    <a class="btn btn-primary" onclick="addizinmazeret()" href="javascript:void(0)">Saatlik
                                        İzin Ekle</a>

                                </div>
                            </div>
                            <table id="ajax-dt-izinmazeret"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Ad Soyad</th>
                                        <th>Statü</th>
                                        <th>Ünvan</th>
                                        <th>Dönem</th>
                                        <th>İzin Türü</th>
                                        <th>İzin Tarihi</th>
                                        <th>İzin Başlama</th>
                                        <th>İzin Bitiş</th>
                                        <th>Süresi</th>
                                        <th>Açıklama</th>
                                        <th></th>
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

    <div id="izinmazeret-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="IzinMazeretForm" id="IzinMazeretForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="izinmazeret_id" id="izinmazeret_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">

                            <div class="col-lg-6">
                                <div>
                                    <label for="izinmazeret_personel" class="form-label">Personel</label>
                                    <select id="izinmazeret_personel" class="form-control" name="izinmazeret_personel"
                                        required>
                                        <option value="">Seçiniz</option>
                                        @foreach ($personel as $value)
                                            <option value="{{ $value->personel_id }}"
                                                data-calisan-tipi="{{ $value->personel_durumid }}">
                                                {{ $value->personel_adsoyad }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="izinmazeret_turid" class="form-label">İzin Türü</label>
                                    <select id="izinmazeret_turid" class="form-control" name="izinmazeret_turid" required>
                                        <option value="">Seçiniz</option>
                                        @foreach ($izintur as $value)
                                            <option value="{{ $value->izin_turid }}">{{ $value->izin_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-3">
                                <div>
                                    <label for="izinTarihi" class="form-label">İzin Tarihi</label>
                                    <input type="date" id="izinmazeret_baslayis" name="izinmazeret_baslayis"
                                        class="form-control" required="required">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="izinBaslamaSaati" class="form-label">İzin Başlama Saati</label>
                                    <input type="time" id="izinmazeret_baslayissaat" name="izinmazeret_baslayissaat"
                                        class="form-control" required="required">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="izinBitisSaati" class="form-label">İzin Bitiş Saati</label>
                                    <input type="time" id="izinmazeret_bitissaat" name="izinmazeret_bitissaat"
                                        class="form-control" required="required">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="aciklama-field" class="form-label">Açıklama</label>
                                <input type="text" id="izinmazeret_aciklama" class="form-control"
                                    name="izinmazeret_aciklama" />
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
            }, function (start, end) {
                $('#date_range').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                table.ajax.reload(); // Tarih seçildiğinde tabloyu yenile
            });

            // Varsayılan olarak bu yılın tarihlerini inputa yaz
            $('#date_range').val(start_date.format('YYYY-MM-DD') + ' - ' + end_date.format('YYYY-MM-DD'));

            let table = $('#ajax-dt-izinmazeret').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('personel.izinmazeret') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val(); // Seçilen tarih aralığını AJAX isteğine ekler
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'personel_adsoyad', name: 'personel_adsoyad' },
                    { data: 'durum_ad', name: 'durum.durum_ad' },
                    { data: 'unvan_ad', name: 'unvan.unvan_ad' },
                    { data: 'izinmazeret_yil', name: 'izin_mazeret.izinmazeret_yil' },
                    { data: 'izin_ad', name: 'izin_turleri.izin_ad' },
                    {
                        data: 'izinmazeret_baslayis',
                        name: 'izin_mazeret.izinmazeret_baslayis',
                        render: function (data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
                        }
                    },
                    {
                        data: 'izinmazeret_baslayissaat',
                        name: 'izin_mazeret.izinmazeret_baslayissaat',
                        render: function (data) {
                            return data ? data.slice(0, 5) : '';
                        }
                    },
                    {
                        data: 'izinmazeret_bitissaat',
                        name: 'izin_mazeret.izinmazeret_bitissaat',
                        render: function (data) {
                            return data ? data.slice(0, 5) : '';
                        }
                    },
                    {
                        data: 'izinmazeret_suresi',
                        name: 'izin_mazeret.izinmazeret_suresi',
                        render: function (data) {
                            return data ? data.slice(0, 5) : '';
                        }
                    },
                    { data: 'izinmazeret_aciklama', name: 'izin_mazeret.izinmazeret_aciklama' },
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
                    [6, 'desc']
                ]
            });

            // Date Range Temizleme (Boş bırakılırsa tüm veriler gelir)
            $('#date_range').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });
        });
        function addizinmazeret() {
            $('#IzinMazeretForm').trigger("reset");
            $('#IzinMazeretModal').modal('Add IzinMazeret');
            $('#izinmazeret-modal').modal('show');
            $('#izinmazeret_id').val('');
            //$('#companylogo-img').attr('src', '/upload/avatar.png'); // resim önizlemesini varsayılana döndür
            $('#company-logo-input').val(''); // file inputu sıfırla

        }
        function deleteFunc(izinmazeret_id) {
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
                        url: "{{ route('izinmazeret.delete') }}",
                        data: { izinmazeret_id: izinmazeret_id },
                        dataType: 'json',
                        success: function (res) {
                            // DataTable'ı güncelle
                            $('#ajax-dt-izinmazeret').DataTable().ajax.reload();

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
                    $('#ajax-dt-izinmazeret').DataTable().ajax.reload(null, false);

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