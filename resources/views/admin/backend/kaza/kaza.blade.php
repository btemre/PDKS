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
                                <div class="col-sm-auto">
                                    @if (Auth::guard('web')->user()->can('trafik.ekle'))
                                    <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Trafik Kazası
                                        Ekle</a>
                                    @endif
                                    @if (Auth::guard('web')->user()->can('trafik.istatistik'))
                                    <a class="btn btn-primary" href="{{route('kaza.istatistik')}}">Trafik Kaza
                                        İstatistiği</a>
                                    @endif
                                    @if (Auth::guard('web')->user()->can('trafik.kazasayi'))
                                    <a class="btn btn-info d-none" href="{{route('personel.izinmazeret')}}">Trafik Kaza
                                        Sayıları</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-crud-dt-kaza"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Yıl</th>
                                        <th>Ay</th>
                                        <th>Kaza</th>
                                        <th>Vefat</th>
                                        <th>Yaralı</th>
                                        <th>Çarpışma</th>
                                        <th>Devrilme</th>
                                        <th>Cisme Çarpma</th>
                                        <th>Durana Çarpma</th>
                                        <th>Yayaya Çarpma</th>
                                        <th>Araçtan Düşme</th>
                                        <th>Diğer</th>
                                        <th>Detay</th>
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
    <div id="kaza-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="KazaForm" id="KazaForm" enctype="multipart/form-data">
                    <input type="hidden" name="kaza_id" id="kaza_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="kaza_resimleri" class="form-label">Kaza Resimleri (En fazla 10 adet)</label>
                                <input type="file" id="kaza_resimleri" class="form-control" name="kaza_resimleri[]" multiple accept="image/*">
                                <small class="form-text text-muted">Desteklenen formatlar: jpeg, png, jpg, gif, svg. Maksimum dosya boyutu: 2MB.</small>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="star_value-field" class="form-label">Kaza Tarihi</label>
                                    <input type="date" id="kaza_tarih" class="form-control" name="kaza_tarih" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="star_value-field" class="form-label">Kaza Saati</label>
                                    <input type="time" id="kaza_saat" class="form-control" name="kaza_saat" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="plaka-field" class="form-label">Plaka</label>
                                <input type="text" id="kaza_plaka" class="form-control" name="kaza_plaka" />
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="arac-field" class="form-label">Araç Cinsi</label>
                                    <select id="kaza_arac" class="form-control" name="kaza_arac[]" data-choices
                                        data-choices-multiple-groups="true" multiple>
                                        <option value="">Seçiniz</option>
                                        @foreach ($araccins as $value)
                                            @for ($i = 0; $i < 5; $i++)
                                                <option value="{{ $value->araccins_ad }}">{{ $value->araccins_id }}-)
                                                    {{ $value->araccins_ad }}</option>
                                            @endfor
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div>
                                    <label for="unvan-field" class="form-label">KKNO</label>
                                    <select id="kaza_kkno" class="form-control" name="kaza_kkno">
                                        <option value="">Seçiniz</option>
                                        @foreach ($kkno as $value)
                                            <option value="{{ $value->kkno_ad }}">{{ $value->kkno_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="km-field" class="form-label">Kilometre</label>
                                <input type="text" id="kaza_km" class="form-control" name="kaza_km" />
                            </div>
                            <div class="col-lg-3">
                                <label for="kaza-field" class="form-label">Kasa Sayısı</label>
                                <input type="number" id="kaza_sayisi" class="form-control" name="kaza_sayisi" min="0"
                                    value="1" />
                            </div>
                            <div class="col-lg-3">
                                <label for="vefat-field" class="form-label">Vefat Sayısı</label>
                                <input type="number" id="kaza_vefat" class="form-control" name="kaza_vefat" min="0"
                                    value="0" />
                            </div>
                            <div class="col-lg-3">
                                <label for="yarali-field" class="form-label">Yaralı Sayısı</label>
                                <input type="number" id="kaza_yarali" class="form-control" name="kaza_yarali" min="0"
                                    value="0" />
                            </div>
                            <div class="col-lg-3">
                                <label for="carpisma-field" class="form-label">Çarpışma</label>
                                <input type="number" id="kaza_carpisma" class="form-control" name="kaza_carpisma" min="0"
                                    value="0" />
                            </div>
                            <div class="col-lg-3">
                                <label for="kaza_devrilme-field" class="form-label">Devrilme</label>
                                <input type="number" id="kaza_devrilme" class="form-control" name="kaza_devrilme" min="0"
                                    value="0" />
                            </div>
                            <div class="col-lg-3">
                                <label for="kaza_cismecarpma-field" class="form-label">Cisme Çarpma</label>
                                <input type="number" id="kaza_cismecarpma" class="form-control" name="kaza_cismecarpma"
                                    min="0" value="0" />
                            </div>
                            <div class="col-lg-3">
                                <label for="kaza_duranaracacarpma-field" class="form-label">Durana Çarpma</label>
                                <input type="number" id="kaza_duranaracacarpma" class="form-control"
                                    name="kaza_duranaracacarpma" min="0" value="0" />
                            </div>
                            <div class="col-lg-3">
                                <label for="kaza_yayacarpma-field" class="form-label">Yayaya Çarpma</label>
                                <input type="number" id="kaza_yayacarpma" class="form-control" name="kaza_yayacarpma"
                                    min="0" value="0" />
                            </div>
                            <div class="col-lg-3">
                                <label for="kaza_aractandusme-field" class="form-label">Araçtan Düşme</label>
                                <input type="number" id="kaza_aractandusme" class="form-control" name="kaza_aractandusme"
                                    min="0" value="0" />
                            </div>
                            <div class="col-lg-3">
                                <label for="kaza_diger-field" class="form-label">Diğer</label>
                                <input type="number" id="kaza_diger" class="form-control" name="kaza_diger" min="0"
                                    value="0" />
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="industry_type-field" class="form-label">Maddi Hasarlı mı? </label>
                                    <select class="form-select" id="kaza_maddihasar" name="kaza_maddihasar">
                                        <option selected value="">Seçim Yapınız</option>
                                        <option value="1">Evet</option>
                                        <option value="0">Hayır</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="kaza_yeri-field" class="form-label">Kaza Yeri</label>
                                <input type="text" id="kaza_yeri" class="form-control" name="kaza_yeri" />
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="industry_type-field" class="form-label">İstikamet </label>
                                    <select class="form-select" id="kaza_istikamet" name="kaza_istikamet">
                                        <option selected value="">Seçim Yapınız</option>
                                        <option value="Adana">Adana</option>
                                        <option value="Akkent Kavşağı">Akkent Kavşağı</option> <!-- Yeni -->
                                        <option value="Batı Kavşağı">Batı Kavşağı</option> <!-- Yeni -->
                                        <option value="Doğu Gişeleri">Doğu Gişeleri</option> <!-- Yeni -->
                                        <option value="Doğu Kavşağı">Doğu Kavşağı</option> <!-- Yeni -->
                                        <option value="Düllük Kavşağı">Düllük Kavşağı</option> <!-- Yeni -->
                                        <option value="Gaziantep">Gaziantep</option>
                                        <option value="Havalimanı Kavşağı">Havalimanı Kavşağı</option> <!-- Yeni -->
                                        <option value="İbrahimli Kavşağı">İbrahimli Kavşağı</option> <!-- Yeni -->
                                        <option value="Iskenderun">İskenderun</option> <!-- Düzeltildi -->
                                        <option value="Kilis Kavşağı">Kilis Kavşağı</option> <!-- Yeni -->
                                        <option value="Küsget Kavşağı">Küsget Kavşağı</option> <!-- Yeni -->
                                        <option value="Narlı">Narlı</option> <!-- (Tekrar düzeltildi) -->
                                        <option value="Nizip">Nizip</option> <!-- Yeni -->
                                        <option value="Nurdağı">Nurdağı</option> <!-- (Zaten var) -->
                                        <option value="Kuzey Kavşağı">Kuzey Kavşağı</option> <!-- Yeni -->
                                        <option value="Osmaniye">Osmaniye</option> <!-- Yeni -->
                                        <option value="Şanlıurfa">Şanlıurfa</option> <!-- (Zaten var) -->
                                        <option value="Şehir Hastanesi Kavşağı">Şehir Hastanesi Kavşağı</option> <!-- Yeni -->
                                        <option value="Yamaçtepe Kavşağı">Yamaçtepe Kavşağı</option> <!-- Yeni -->
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label for="kaza_aciklama" class="form-label">Açıklama</label>
                                <textarea id="kaza_aciklama" class="form-control" name="kaza_aciklama" rows="1"></textarea>
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
            $('#ajax-crud-dt-kaza').DataTable({
                processing: true,
                serverSide: true,

                ajax: "{{ route('kaza.listesi') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'yil', name: 'yil' },
                    { data: 'ay_ad', name: 'ay_ad' },
                    { data: 'kaza', name: 'kaza' },
                    { data: 'vefat', name: 'vefat' },
                    { data: 'yarali', name: 'yarali' },
                    { data: 'carp', name: 'carp' },
                    { data: 'devril', name: 'devril' },
                    { data: 'cism', name: 'cism' },
                    { data: 'duran', name: 'duran' },
                    { data: 'yaya', name: 'yaya' },
                    { data: 'aracdus', name: 'aracdus' },
                    { data: 'diger', name: 'diger' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
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

        });
        function add() {
            $('#KazaForm').trigger("reset");
            $('#KazaModal').modal('Add Kaza');
            $('#kaza-modal').modal('show');
            $('#kaza_id').val('');
            $('#companylogo-img').attr('src', '/upload/avatar.png'); // resim önizlemesini varsayılana döndür
            $('#company-logo-input').val(''); // file inputu sıfırla
            $('#kaza_resimleri').val('');

        }
        function editFunc(kaza_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('kaza.edit') }}",
                data: { kaza_id: kaza_id },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        $('#KazaModal').html("Kaza Düzenle");
                        $('#kaza-modal').modal('show');
                        $('#kaza_id').val(res.data.kaza_id);
                        $('#kaza_tarih').val(res.data.kaza_tarih);
                        $('#kaza_saat').val(res.data.kaza_saat);
                        $('#kaza_plaka').val(res.data.kaza_plaka);
                        $('#kaza_arac').val(res.data.kaza_arac);
                        $('#kaza_kkno').val(res.data.kaza_kkno);
                        $('#kaza_km').val(res.data.kaza_km);
                        $('#kaza_sayisi').val(res.data.kaza_sayisi);
                        $('#kaza_vefat').val(res.data.kaza_vefat);
                        $('#kaza_yarali').val(res.data.kaza_yarali);
                        $('#kaza_carpisma').val(res.data.kaza_carpisma);
                        $('#kaza_devrilme').val(res.data.kaza_devrilme);
                        $('#kaza_cismecarpma').val(res.data.kaza_cismecarpma);
                        $('#kaza_duranaracacarpma').val(res.data.kaza_duranaracacarpma);
                        $('#kaza_yayacarpma').val(res.data.kaza_yayacarpma);
                        $('#kaza_aractandusme').val(res.data.kaza_aractandusme);
                        $('#kaza_diger').val(res.data.kaza_diger);
                        $('#kaza_maddihasar').val(res.data.kaza_maddihasar);
                        $('#kaza_yeri').val(res.data.kaza_yeri);
                        $('#kaza_istikamet').val(res.data.kaza_istikamet);
                        $('#kaza_aciklama').val(res.data.kaza_aciklama);
                    }
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
        $('#KazaForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('kaza.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function (response) {
                    $("#kaza-modal").modal('hide');
                    $('#ajax-crud-dt-kaza').DataTable().ajax.reload(null, false);

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