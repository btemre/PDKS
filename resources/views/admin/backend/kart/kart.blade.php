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
                            <h4 class="card-title mb-0">{{ $pagetitle }}</h4>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                    <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Kart Ekle</a>
                                    @if (Auth::guard('web')->user()->can('pdks.izinekle'))
                                    <a class="btn btn-info" href="{{ route('personel.kartgecmisi') }}">Personel Kart Kullanım Alanı
                                        İzin Ekle</a>
                                @endif
                                </div>

                            </div>
                            <table id="ajax-crud-kart"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sıra</th>
                                        <th>Birim</th>
                                        <th>Ad Soyad</th>
                                        <th>Sicil No</th>
                                        <th>Statü</th>
                                        <th>Cihaz</th>
                                        <th>Kart ID</th>
                                        <th>Kart No</th>
                                        <th>İdari Yetki</th>
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
    <div id="personel-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="PersonelForm" id="PersonelForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="personel_id" id="personel_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <div>
                                    <label for="personel-field" class="form-label">Personel</label>
                                    <select name="personel_id" id="personel_id" data-choices class="form-control">
                                        <option value="">Seçiniz</option>
                                        @foreach ($personel as $value)
                                            <option value="{{ $value->personel_id }}">
                                                {{ $value->personel_adsoyad }} ({{ $value->durum_ad }} - {{ $value->birim_ad }})
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div>
                                    <label for="cihaz-field" class="form-label">Cihaz (Sistemde tanımlı tüm cihazlar
                                        otomatik olarak gelmektedir)</label>
                                    <select class="form-control" id="choices-multiple-remove-button" data-choices data-choices-removeItem
                                        name="cihaz_id[]" multiple>
                                        @foreach ($cihaz2 as $value)
                                            <option value="{{ $value->cihaz_id }}" selected>{{ $value->cihaz_adi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <label for="companyname-field" class="form-label">Kart Numarası</label>
                                <input type="number" id="kart_numarasi" class="form-control" name="kart_numarasi" />
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="industry_type-field" class="form-label">İdare Yetkisi mi? </label>
                                    <select class="form-select" id="yetkili" name="yetkili">
                                        <option value="0">Hayır</option>
                                        <option value="1">Evet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="okul-field" class="form-label">Kart İsimlendirme</label>
                                <input type="text" id="kart_adi" placeholder="Zorunlu Değil" class="form-control"
                                    name="kart_adi" />
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
            $('#ajax-crud-kart').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('personel.kartlistesi') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Sıralama için eklendi
                    { data: 'birim_ad', name: 'birim_ad' },
                    { data: 'personel_adsoyad', name: 'personel_adsoyad' },
                    { data: 'personel_sicilno', name: 'personel_sicilno' },
                    { data: 'durum_ad', name: 'durum_ad' },
                    {
                        data: 'cihaz_adi',
                        name: 'pdks_cihazlar.cihaz_adi',
                        render: function (data, type, row) {
                            if (!data) return '';
                            let cihazlar = data.split(',');
                            let options = '';
                            cihazlar.forEach(function (cihaz) {
                                options += '<option>' + cihaz.trim() + '</option>';
                            });
                            return '<select class="form-select rounded-pill">' + options + '</select>';
                        }
                    },
                    { data: 'kart_id', name: 'kart_id' },
                    { data: 'kart_numarasi', name: 'kart_numarasi' },
                    {
                        data: 'yetkili',
                        name: 'yetkili',
                        render: function (data, type, row) {
                            if (data == 1) {
                                return '<span style="color:green;font-weight:bold;">Yetkili</span>';
                            } else {
                                return '<span style="color:red;font-weight:bold;">Yetkisiz</span>';
                            }
                        }
                    },

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
                    [5, 'desc']
                ]
            });
        });
        function deleteFunc(kart_id) {
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
                        url: "{{ route('kart.delete') }}",
                        data: {
                            kart_id: kart_id
                        },
                        dataType: 'json',
                        success: function(res) {
                            // DataTable'ı güncelle
                            $('#ajax-crud-kart').DataTable().ajax.reload();

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
        function editFunc(kart_id, kart_numarasi) {
    Swal.fire({
        title: "Kart Numarasını Güncelle",
        input: "number",
        inputLabel: "Yeni Kart Numarası",
        inputValue: kart_numarasi,
        showCancelButton: true,
        confirmButtonText: "Güncelle",
        cancelButtonText: "İptal",
        inputValidator: (value) => {
            if (!value) {
                return "Lütfen yeni kart numarasını giriniz!";
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "{{ route('kart.update') }}",
                data: {
                    kart_id: kart_id,
                    kart_numarasi: result.value
                },
                dataType: 'json',
                success: function (res) {
                    $('#ajax-crud-kart').DataTable().ajax.reload(null, false);
                    Swal.fire({
                        title: res.status === 'success' ? 'Başarılı!' : 'Hata!',
                        text: res.message,
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
        function add() {
            $('#PersonelForm').trigger("reset");
            $('#PersonelModal').modal('Add Personel');
            $('#personel-modal').modal('show');
            $('#personel_id').val('');
        }
        $('#PersonelForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('kart.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function (response) {
                    $("#personel-modal").modal('hide');
                    $('#ajax-crud-kart').DataTable().ajax.reload(null, false);

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
<script src="/build/choices/choices.min.js"></script>
@endsection