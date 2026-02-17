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
                                    @if (Auth::guard('web')->user()->can('ayar.ekle'))
                                        <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Ayar Ekle</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-crud-dt-ayar"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Kurum</th>
                                        <th>Şef</th>
                                        <th>Şef Ünvanı</th>
                                        <th>Başmühendis</th>
                                        <th>Başmühendis Ünvanı</th>
                                        <th>P. Müdürü</th>
                                        <th>P. Müdür Ünvanı</th>
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
    <div id="ayar-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="AyarForm" id="AyarForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="ayar_id" id="ayar_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <label for="ayar_kurum-field" class="form-label">Kurum</label>
                                <input type="text" id="ayar_kurum" class="form-control" name="ayar_kurum"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-6">
                                <label for="ayar_yonetici-field" class="form-label">Şef</label>
                                <input type="text" id="ayar_yonetici" class="form-control" name="ayar_yonetici"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-6">
                                <label for="ayar_yoneticiunvan-field" class="form-label">Şef Ünvanı</label>
                                <input type="text" id="ayar_yoneticiunvan" class="form-control" name="ayar_yoneticiunvan"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-6">
                                <label for="ayar_basmuhendis-field" class="form-label">Başmühendis</label>
                                <input type="text" id="ayar_basmuhendis" class="form-control" name="ayar_basmuhendis"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-6">
                                <label for="ayar_basmuhendisunvan-field" class="form-label">Başmühendis Ünvanı</label>
                                <input type="text" id="ayar_basmuhendisunvan" class="form-control"
                                    name="ayar_basmuhendisunvan" autocomplete="off" />
                            </div>
                            <div class="col-lg-6">
                                <div>
                                    <label for="ayar_mudur-field" class="form-label">P. Müdürü</label>
                                    <input type="text" id="ayar_mudur" class="form-control" name="ayar_mudur"
                                        autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div>
                                    <label for="ayar_mudurunvan-field" class="form-label">P. Müdürü Ünvanı</label>
                                    <input type="text" id="ayar_mudurunvan" class="form-control"
                                        name="ayar_mudurunvan" autocomplete="off" />
                                </div>
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
            $('#ajax-crud-dt-ayar').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ayar.listesi') }}",
                columns: [{
                        data: 'ayar_kurum',
                        name: 'ayar_kurum'
                    },
                    {
                        data: 'ayar_yonetici',
                        name: 'ayar_yonetici'
                    },
                    {
                        data: 'ayar_yoneticiunvan',
                        name: 'ayar_yoneticiunvan'
                    },
                    {
                        data: 'ayar_basmuhendis',
                        name: 'ayar_basmuhendis'
                    },
                    {
                        data: 'ayar_basmuhendisunvan',
                        name: 'ayar_basmuhendisunvan'
                    },
                    {
                        data: 'ayar_mudur',
                        name: 'ayar_mudur'
                    },
                    {
                        data: 'ayar_mudurunvan',
                        name: 'ayar_mudurunvan'
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
                    [0, 'asc']
                ],
                dom: 'lBfrtip',
                buttons: [
                    'excelHtml5',
                    'pdfHtml5',
                    'print'
                ],
            });
        });

        function add() {
            $('#AyarForm').trigger("reset");
            $('#AyarModal').modal('Add Ayar');
            $('#ayar-modal').modal('show');
            $('#ayar_id').val('');
        }

        function editFunc(ayar_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('ayar.edit') }}",
                data: {
                    ayar_id: ayar_id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#AyarModal').html("Ayar Düzenle");
                        $('#ayar-modal').modal('show');
                        $('#ayar_id').val(res.data.ayar_id);
                        $('#ayar_kurum').val(res.data.ayar_kurum);
                        $('#ayar_yonetici').val(res.data.ayar_yonetici);
                        $('#ayar_yoneticiunvan').val(res.data.ayar_yoneticiunvan);
                        $('#ayar_basmuhendis').val(res.data.ayar_basmuhendis);
                        $('#ayar_basmuhendisunvan').val(res.data.ayar_basmuhendisunvan);
                        $('#ayar_mudur').val(res.data.ayar_mudur);
                        $('#ayar_mudurunvan').val(res.data.ayar_mudurunvan);

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

        function deleteFunc(ayar_id) {
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
                        url: "{{ route('ayar.delete') }}",
                        data: {
                            ayar_id: ayar_id
                        },
                        dataType: 'json',
                        success: function(res) {
                            // DataTable'ı güncelle
                            $('#ajax-crud-dt-ayar').DataTable().ajax.reload();

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
        $('#AyarForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('ayar.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#ayar-modal").modal('hide');
                    $('#ajax-crud-dt-ayar').DataTable().ajax.reload(null, false);

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
