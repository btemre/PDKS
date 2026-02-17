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
                            <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                    @if (Auth::guard('web')->user()->can('arac.ekle'))
                                        <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Araç Ekle</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-crud-dt-arac"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Cins</th>
                                        <th>Model Yılı</th>
                                        <th>Marka</th>
                                        <th>Sürücü</th>
                                        <th>TCK</th>
                                        <th>Plaka</th>
                                        <th>Muayene</th>
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
    <div id="arac-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="AracForm" id="AracForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="arac_id" id="arac_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">
                            <div class="col-lg-3">
                                <label for="arac_cins-field" class="form-label">Cins</label>
                                <input type="text" id="arac_cins" class="form-control" name="arac_cins"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <label for="arac_marka-field" class="form-label">Marka</label>
                                <input type="text" id="arac_marka" class="form-control" name="arac_marka"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <label for="arac_surucusu-field" class="form-label">Sürücü</label>
                                <input type="text" id="arac_surucusu" class="form-control" name="arac_surucusu"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <label for="arac_tck-field" class="form-label">TCK</label>
                                <input type="text" id="arac_tck" class="form-control" name="arac_tck"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <label for="arac_plaka-field" class="form-label">Plaka</label>
                                <input type="text" id="arac_plaka" class="form-control" name="arac_plaka"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="arac_ilkmuayene-field" class="form-label">Muayene Tarihi</label>
                                    <input type="date" id="arac_ilkmuayene" class="form-control"
                                        name="arac_ilkmuayene" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="arac_sigorta-field" class="form-label">Sigorta Tarihi</label>
                                    <input type="date" id="arac_ilksigorta" class="form-control"
                                        name="arac_ilksigorta" />
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="arac_model-field" class="form-label">Model Yıl</label>
                                <input type="number" id="arac_model" class="form-control" name="arac_model" />
                            </div>
                            <div class="col-lg-3">
                                <label for="arac_kodu-field" class="form-label">Araç Kodu</label>
                                <input type="text" id="arac_kod" class="form-control" name="arac_kod" />
                            </div>
                            <div class="col-lg-9">
                                <label for="arac_sase-field" class="form-label">Şase</label>
                                <input type="text" id="arac_sase" class="form-control" name="arac_sase" />
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
            $('#ajax-crud-dt-arac').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('arac.listesi') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // Sıralama için eklendi
                    {
                        data: 'arac_cins',
                        name: 'arac_cins'
                    },
                    {
                        data: 'arac_model',
                        name: 'arac_model'
                    },
                    {
                        data: 'arac_marka',
                        name: 'arac_marka'
                    },
                    {
                        data: 'arac_surucusu',
                        name: 'arac_surucusu'
                    },
                    {
                        data: 'arac_tck',
                        name: 'arac_tck'
                    },
                    {
                        data: 'arac_plaka',
                        name: 'arac_plaka'
                    },
                    {
                        data: 'arac_ilkmuayene',
                        name: 'arac_ilkmuayene',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g,
                                '-') : '';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                createdRow: function(row, data, dataIndex) {
                    if (data.arac_ilkmuayene) {
                        let muayeneTarihi = new Date(data.arac_ilkmuayene);
                        let bugun = new Date();
                        if (muayeneTarihi < bugun) {
                            $('td', row).css('background-color', 'orange').css('color', 'black');
                        }
                    }
                },
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                lengthMenu: [[-1, 10, 25, 50, ], [ "Tümü", 10, 25, 50]],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength',
                    'excelHtml5',
                    'print'
                ],
                order: [
                    [0, 'desc']
                ]
            });
        });

        function add() {
            $('#AracForm').trigger("reset");
            $('#AracModal').modal('Add Arac');
            $('#arac-modal').modal('show');
            $('#arac_id').val('');
            $('#companylogo-img').attr('src', '/upload/avatar.png'); // resim önizlemesini varsayılana döndür
            $('#company-logo-input').val(''); // file inputu sıfırla

        }

        function editFunc(arac_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('arac.edit') }}",
                data: {
                    arac_id: arac_id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#AracModal').html("Arac Düzenle");
                        $('#arac-modal').modal('show');
                        $('#arac_id').val(res.data.arac_id);
                        $('#arac_cins').val(res.data.arac_cins);
                        $('#arac_marka').val(res.data.arac_marka);
                        $('#arac_surucusu').val(res.data.arac_surucusu);
                        $('#arac_tck').val(res.data.arac_tck);
                        $('#arac_plaka').val(res.data.arac_plaka);
                        $('#arac_ilkmuayene').val(res.data.arac_ilkmuayene);
                        $('#arac_ilksigorta').val(res.data.arac_ilksigorta);
                        $('#arac_model').val(res.data.arac_model);
                        $('#arac_kod').val(res.data.arac_kod);
                        $('#arac_sase').val(res.data.arac_sase)

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

        function deleteFunc(arac_id) {
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
                        url: "{{ route('arac.delete') }}",
                        data: {
                            arac_id: arac_id
                        },
                        dataType: 'json',
                        success: function(res) {
                            // DataTable'ı güncelle
                            $('#ajax-crud-dt-arac').DataTable().ajax.reload();

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
        $('#AracForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('arac.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#arac-modal").modal('hide');
                    $('#ajax-crud-dt-arac').DataTable().ajax.reload(null, false);

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
