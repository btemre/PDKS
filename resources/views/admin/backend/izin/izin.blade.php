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
                                <li class="breadcrumb-item active">İzin İşlemleri</li>
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
                                    @if (Auth::guard('web')->user()->can('izin.ekle'))
                                        <a class="btn btn-success" onclick="add()" href="javascript:void(0)">İzin Ekle</a>
                                    @endif
                                    @if (Auth::guard('web')->user()->can('izin.saatlikekle'))
                                        <a class="btn btn-primary" onclick="addizinmazeret()"
                                            href="javascript:void(0)">Saatlik İzin Ekle</a>
                                    @endif
                                    <a class="btn btn-info" href="{{ route('personel.izinmazeret') }}">Saatlik İzin
                                        Listesi</a>
                                        <a class="btn btn-warning" href="{{ route('izin.zorunlu') }}">Zorunlu İzin
                                            Listesi</a>
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
                                        <th>Yazdır</th>
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
    <div id="izin-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="IzinForm" id="IzinForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="izin_id" id="izin_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <div class="text-center">
                                    <div class="position-relative d-inline-block">
                                        <h5 class="fs-13 mt-3">Kalan İzin</h5>
                                        <div class="avatar-lg p-1">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <input type="number" id="kalan_izin" name="kalan_izin" disabled
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div>
                                    <label for="izin_personel" class="form-label">Personel</label>
                                    <select id="izin_personel" class="form-control" name="izin_personel" required>
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
                                    <label for="izin_turid" class="form-label">İzin Türü</label>
                                    <select id="izin_turid" class="form-control" name="izin_turid" required>
                                        <option value="">Seçiniz</option>
                                        @foreach ($izintur as $value)
                                            <option value="{{ $value->izin_turid }}">{{ $value->izin_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="izin_yil" class="form-label">İzin Yılı</label>
                                    <input type="number" id="izin_yil" value="{{ date('Y') }}"
                                        class="form-control" min="{{ date('Y') - 3 }}" max="{{ date('Y') }}"
                                        name="izin_yil" required />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="izin_baslayis" class="form-label">İzin Başlama Tarihi</label>
                                    <input type="date" id="izin_baslayis" class="form-control" name="izin_baslayis"
                                        required />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="izin_suresi" class="form-label">İzin Süresi</label>
                                    <input type="number" id="izin_suresi" class="form-control" name="izin_suresi"
                                        min="1" max="100" required />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="izin_bitis" class="form-label">İzin Bitiş Tarihi</label>
                                    <input type="date" id="izin_bitis" class="form-control" name="izin_bitis"
                                        required />
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="izin_isebaslayis" class="form-label">İşe Başlama Tarihi</label>
                                    <input type="date" id="izin_isebaslayis" class="form-control"
                                        name="izin_isebaslayis" required />
                                </div>
                            </div>
                            {{-- Resmi tatilleri JS için gömülü veri olarak kullanıyoruz --}}
                            <script>
                                window.resmiTatiller = @json($resmiTatiller); // Controller’dan gönderilmiş veri
                            </script>
                            @vite(['resources/js/izin.js'])
                            <div class="col-lg-6">
                                <label for="companyname-field" class="form-label">Vefat Eden Yakınlık Bilgisi</label>
                                <input type="text" id="izin_vefat" class="form-control"
                                    placeholder="Vefat İzni Verildiyse Doldurulacak-> ÖRNEĞiN='Babamın'"
                                    name="izin_vefat" />
                            </div>
                            <div class="col-lg-6">
                                <label for="companyname-field" class="form-label">Rapor Aldığı Sağlık Kuruluşu</label>
                                <input type="text" id="izin_saglikkurumu" class="form-control"
                                    placeholder="Rapor Aldıysa Doldurulacak-> ÖRNEĞiN='Osmaniye Devlet Hastanesi'"
                                    name="izin_saglikkurumu" />
                            </div>



                            <div class="col-lg-6">
                                <label for="adres-field" class="form-label">Adres</label>
                                <input type="text" id="izin_adresi" class="form-control"
                                    placeholder="Adres Alanı Boş Bırakılırsa İkamet Adresi Yazılır" name="izin_adresi" />
                            </div>
                            <div class="col-lg-6">
                                <label for="aciklama-field" class="form-label">Açıklama</label>
                                <input type="text" id="izin_aciklama" class="form-control" name="izin_aciklama" />
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
                                    <select id="izinmazeret_turid" class="form-control" name="izinmazeret_turid"
                                        required>
                                        <option value="">Seçiniz</option>
                                        @foreach ($izinturmazeret as $value)
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
                    url: "{{ route('personel.izin') }}",
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
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tümü"]],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength',
                    'excelHtml5',
                    'print'
                ],
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
        function add() {
            $('#IzinForm').trigger("reset");
            $('#IzinModal').modal('Add Izin');
            $('#izin-modal').modal('show');
            $('#izin_id').val('');
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
        function editFunc(izin_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('izin.edit') }}",
                data: {
                    izin_id: izin_id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#IzinModal').html("Izin Düzenle");
                        $('#izin-modal').modal('show');
                        $('#izin_id').val(res.data.izin_id);
                        $('#izin_personel').val(res.data.izin_personel);
                        $('#izin_turid').val(res.data.izin_turid);
                        $('#izin_yil').val(res.data.izin_yil);
                        $('#izin_baslayis').val(res.data.izin_baslayis);
                        $('#izin_suresi').val(res.data.izin_suresi);
                        $('#izin_bitis').val(res.data.izin_bitis);
                        $('#izin_isebaslayis').val(res.data.izin_isebaslayis);
                        $('#izin_vefat').val(res.data.izin_vefat);
                        $('#izin_saglikkurumu').val(res.data.izin_saglikkurumu);
                        $('#izin_adres').val(res.data.izin_adres);
                        $('#izin_aciklama').val(res.data.izin_aciklama);
                    }
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

        function deleteFunc(izin_id) {
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
                        url: "{{ route('izin.delete') }}",
                        data: {
                            izin_id: izin_id
                        },
                        dataType: 'json',
                        success: function(res) {
                            // DataTable'ı güncelle
                            $('#ajax-dt-izin').DataTable().ajax.reload();

                            // Controller'dan gelen status'e göre mesaj göster
                            Swal.fire({
                                title: res.status === 'success' ? 'Başarılı!' : 'Hata!',
                                text: res.message, // Controller'dan gelen mesaj
                                icon: res.status === 'success' ? 'success' : 'error',
                                confirmButtonText: 'Tamam'
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
       /* $(document).ready(function () {
    function fetchKalanIzin() {
        let personelId = $("#izin_personel").val();
        let izinTurId  = $("#izin_turid").val();
        let izinYil    = $("#izin_yil").val();

        if (personelId && izinTurId) {
            $.ajax({
                url: "/izin/kalan",
                method: "GET",
                data: {
                    personel_id: personelId,
                    izin_turid: izinTurId,
                    izin_yil: izinYil
                },
                success: function (res) {
                    $("#kalan_izin").val(res.kalan_izin); 
                }
            });
        }
    }

    $("#izin_personel, #izin_turid, #izin_yil").on("change", fetchKalanIzin);
});*/
function fetchKalanIzin() {
    let personelId = $("#izin_personel").val();
    let izinTurId  = $("#izin_turid").val();
    let izinYil    = $("#izin_yil").val();

    if (personelId && izinTurId && izinYil) {
        $.ajax({
            url: "/izin/kalan",
            method: "GET",
            data: {
                personel_id: personelId,
                izin_turid: izinTurId,
                izin_yil: izinYil
            },
            success: function (res) {
                $("#kalan_izin").val(res.kalan_izin);
            }
        });
    }
}

// personel ve izin türü değişince
$("#izin_personel, #izin_turid").on("change", fetchKalanIzin);

// yıl değişince hem yazıldığında hem oklarla oynandığında çalışsın
$("#izin_yil").on("input change", fetchKalanIzin);


    </script>
@endsection
