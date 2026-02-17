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
                        <h4 class="mb-sm-0">Personeller</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Anasayfa</a></li>
                                <li class="breadcrumb-item"><a href="{{ route(name: 'personel.listesi') }}">Personeller</a>
                                </li>
                                <li class="breadcrumb-item active">Ayrılış Yapan Personeller</li>
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

                                </div>
                            </div>
                            <table id="ajax-crud-datatable"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Ad Soyad</th>
                                        <th>Sicil No</th>
                                        <th>Statü</th>
                                        <th>Ünvan</th>
                                        <th>Birimi</th>
                                        <th>Durum</th>
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
                                <div class="text-center">
                                    <div class="position-relative d-inline-block">
                                        <div class="position-absolute bottom-0 end-0">
                                            <label for="company-logo-input" class="mb-0" data-bs-toggle="tooltip"
                                                data-bs-placement="right" title="Resim Yükleyin">
                                                <div class="avatar-xs cursor-pointer">
                                                    <div class="avatar-title bg-light border rounded-circle text-muted">
                                                        <i class="ri-image-fill"></i>
                                                    </div>
                                                </div>
                                            </label>
                                            <input class="form-control d-none" name="personel_resim" value=""
                                                id="company-logo-input" type="file"
                                                accept="image/png, image/gif, image/jpeg">
                                        </div>
                                        <div class="avatar-lg p-1">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <img src="{{ asset('/upload/avatar.png') }}" id="companylogo-img"
                                                    class="avatar-md rounded-circle object-cover" />
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="fs-13 mt-3">Resim</h5>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="companyname-field" class="form-label">Ad Soyad</label>
                                <input type="text" id="personel_adsoyad" class="form-control" name="personel_adsoyad"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="tc-field" class="form-label">TC Kimlik</label>
                                    <input type="number" id="personel_tc" class="form-control" name="personel_tc"
                                        pattern="[0-9]{11}" autocomplete="off" />
                                    <div class="invalid-feedback">Geçerli bir TC girin.</div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="companyname-field" class="form-label">Sicil (TC de yazılabilir)</label>
                                    <input type="number" id="personel_sicilno" class="form-control"
                                        placeholder="Aralara Çizgi Koymayın!" name="personel_sicilno"
                                        autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="tel-field" class="form-label">Telefon</label>
                                    <input type="number" id="personel_telefon" class="form-control"
                                        name="personel_telefon" autocomplete="off" value="5" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="durum-field" class="form-label">Statü</label>
                                    <select id="personel_durumid" class="form-control" name="personel_durumid">
                                        <option value="">Seçiniz</option>
                                        @foreach ($durum as $value)
                                            <option value="{{ $value->durum_id }}">{{ $value->durum_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="gorev-field" class="form-label">Kadro</label>
                                    <select id="personel_gorev" class="form-control" name="personel_gorev">
                                        <option value="">Seçiniz</option>
                                        @foreach ($gorev as $value)
                                            <option value="{{ $value->gorev_id }}">{{ $value->gorev_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="unvan-field" class="form-label">Ünvan</label>
                                    <select id="personel_unvan" class="form-control" name="personel_unvan">
                                        <option value="">Seçiniz</option>
                                        @foreach ($unvan as $value)
                                            <option value="{{ $value->unvan_id }}">{{ $value->unvan_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="unvan-field" class="form-label">Birim</label>
                                    <select id="personel_birim" class="form-control" name="personel_birim">
                                        <option value="">Seçiniz</option>
                                        @foreach ($birim as $value)
                                            <option value="{{ $value->birim_id }}">{{ $value->birim_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="star_value-field" class="form-label">Doğum Tarihi</label>
                                    <input type="date" id="personel_dogumtarihi" class="form-control"
                                        name="personel_dogumtarihi" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="star_value-field" class="form-label">İşe Giriş Tarihi</label>
                                    <input type="date" id="personel_isegiristarih" class="form-control"
                                        name="personel_isegiristarih" />
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <label for="companyname-field" class="form-label">E-Posta</label>
                                <input type="email" id="personel_eposta" class="form-control" name="personel_eposta"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-2">
                                <div>
                                    <label for="industry_type-field" class="form-label">PDKS Aktif mi? </label>
                                    <select class="form-select" id="personel_kartkullanim" name="personel_kartkullanim">
                                        <option selected value="">Seçim Yapınız</option>
                                        <option value="1">Evet</option>
                                        <option value="0">Hayır</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <label for="companyname-field" class="form-label">Derece</label>
                                <input type="number" id="personel_derece" class="form-control"
                                    name="personel_derece" />
                            </div>
                            <div class="col-lg-1">
                                <label for="companyname-field" class="form-label">Kademe</label>
                                <input type="number" id="personel_kademe" class="form-control"
                                    name="personel_kademe" />
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="il-field" class="form-label">İl</label>
                                    <select id="personel_il" class="form-control" name="personel_il">
                                        <option value="">Seçiniz</option>
                                        @foreach ($il as $value)
                                            <option value="{{ $value->il_id }}">{{ $value->il_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="ilce-field" class="form-label">İlçe</label>
                                    <select id="personel_ilce" class="form-control" name="personel_ilce">
                                        <option value="">Seçiniz</option>
                                        @foreach ($ilce as $value)
                                            <option value="{{ $value->ilce_id }}">{{ $value->ilce_ad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div>
                                    <label for="industry_type-field" class="form-label">Sözleşmeli mi? </label>
                                    <select class="form-select" id="personel_sozlesmelimi" name="personel_sozlesmelimi">
                                        <option selected value="">Seçim Yapınız</option>
                                        <option value="1">Evet</option>
                                        <option value="0">Hayır</option>
                                        <option value="2">Taşeron</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div>
                                    <label for="industry_type-field" class="form-label">Engelli mi? </label>
                                    <select class="form-select" id="personel_engellimi" name="personel_engellimi">
                                        <option selected value="">Seçim Yapınız</option>
                                        <option value="1">Evet</option>
                                        <option value="0">Hayır</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div>
                                    <label for="mesai-field" class="form-label">Mesai Türü</label>
                                    <select id="personel_mesai" class="form-control" name="personel_mesai">
                                        <option value="">Seçiniz</option>
                                        @foreach ($mesai as $value)
                                            <option value="{{ $value->mesai_id }}">{{ $value->mesai_aciklama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div>
                                    <label for="kan-field" class="form-label">Kan Grubu </label>
                                    <select class="form-select" id="personel_kan" name="personel_kan">
                                        <option selected value="">Seçim Yapınız</option>
                                        <option>A Rh (+)</option>
                                        <option>B Rh (+)</option>
                                        <option>AB Rh (+)</option>
                                        <option>O Rh (+)</option>
                                        <option>A Rh (-)</option>
                                        <option>B Rh (-)</option>
                                        <option>AB Rh (-)</option>
                                        <option>O Rh (-)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div>
                                    <label for="ogrenim-field" class="form-label">Öğrenim</label>
                                    <select id="personel_ogrenim" class="form-control" name="personel_ogrenim">
                                        <option value="">Seçiniz</option>
                                        @foreach ($ogrenim as $value)
                                            <option value="{{ $value->ogrenim_id }}">{{ $value->ogrenim_tur }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="okul-field" class="form-label">Okul</label>
                                <input type="text" id="personel_okul" class="form-control" name="personel_okul" />
                            </div>
                            <div class="col-lg-2">
                                <div>
                                    <label for="ayrilis-field" class="form-label">Durum</label>
                                    <select id="personel_durum" class="form-control" name="personel_durum">
                                        <option value="">Seçiniz</option>
                                        @foreach ($ayrilis as $value)
                                            <option value="{{ $value->ayrilis_id }}">{{ $value->ayrilis_tur }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label for="adres-field" class="form-label">Adres</label>
                                <input type="text" id="personel_adres" class="form-control" name="personel_adres" />
                            </div>
                            <div class="col-lg-12">
                                <label for="personel_aciklama" class="form-label">Açıklama</label>
                                <textarea id="personel_aciklama" class="form-control" name="personel_aciklama" rows="2"></textarea>
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
            $('#ajax-crud-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('personel.ayrilan') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // Sıralama için eklendi
                    //{ data: 'personel_id', name: 'personel_id' },
                    {
                        data: 'personel_adsoyad',
                        name: 'personel_adsoyad'
                    },
                    {
                        data: 'personel_sicilno',
                        name: 'personel_sicilno'
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
                        data: 'birim_ad',
                        name: 'birim.birim_ad'
                    }, // Doğru isimlendirme
                    {
                        data: 'ayrilis_tur',
                        name: 'ayrilis.ayrilis_tur'
                    }, // Doğru isimlendirme
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
                    [3, 'desc']
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

        function editFunc(personel_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('personel.edit') }}",
                data: {
                    personel_id: personel_id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#PersonelModal').html("Personel Düzenle");
                        $('#personel-modal').modal('show');
                        $('#personel_id').val(res.data.personel_id);
                        $('#personel_adsoyad').val(res.data.personel_adsoyad);
                        $('#personel_tc').val(res.data.personel_tc);
                        $('#personel_sicilno').val(res.data.personel_sicilno);
                        $('#personel_telefon').val(res.data.personel_telefon);
                        $('#personel_durumid').val(res.data.personel_durumid);
                        $('#personel_gorev').val(res.data.personel_gorev);
                        $('#personel_unvan').val(res.data.personel_unvan);
                        $('#personel_birim').val(res.data.personel_birim);
                        $('#personel_dogumtarihi').val(res.data.personel_dogumtarihi);
                        $('#personel_isegiristarih').val(res.data.personel_isegiristarih);
                        $('#personel_eposta').val(res.data.personel_eposta);
                        $('#personel_derece').val(res.data.personel_derece);
                        $('#personel_kademe').val(res.data.personel_kademe);
                        $('#personel_il').val(res.data.personel_il);
                        $('#personel_ilce').val(res.data.personel_ilce);
                        $('#personel_sozlesmelimi').val(res.data.personel_sozlesmelimi);
                        $('#personel_engellimi').val(res.data.personel_engellimi);
                        $('#personel_mesai').val(res.data.personel_mesai);
                        $('#personel_kan').val(res.data.personel_kan);
                        $('#personel_ogrenim').val(res.data.personel_ogrenim);
                        $('#personel_okul').val(res.data.personel_okul);
                        $('#personel_durum').val(res.data.personel_durum);
                        $('#personel_adres').val(res.data.personel_adres);
                        $('#personel_aciklama').val(res.data.personel_aciklama);
                        // Sadece bu kısmı ekle:
                        if (res.data.personel_resim) {
                            $('#companylogo-img').attr('src', '/' + res.data.personel_resim + '?t=' + new Date()
                                .getTime());
                        } else {
                            $('#companylogo-img').attr('src', '/upload/avatar.png');
                        }

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
                    $('#ajax-crud-datatable').DataTable().ajax.reload(null, false);

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
