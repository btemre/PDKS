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
                                    @if (Auth::guard('web')->user()->can('cihaz.ekle'))
                                    <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Cihaz Ekle</a>
                                    @endif
                                </div>

                            </div>
                            <table id="ajax-crud-cihaz"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sıra</th>
                                        <th>Kurum</th>
                                        <th>Cihaz</th>
                                        <th>Geçiş</th>
                                        <th>IP</th>
                                        <th>Port</th>
                                        <th>Model</th>
                                        <th>Seri No</th>
                                        <th>MAC Adresi</th>
                                        <th>Cihaz Kart Sayısı</th>
                                        <th>Son Bağlanti Zamani</th>
                                        <th>Baglanti</th>
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
    <div id="cihaz-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="CihazForm" id="CihazForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="cihaz_id" id="cihaz_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">
                            <div class="col-lg-3">
                                <div>
                                    <label for="cihaz_kurumid" class="form-label">Kurum</label>
                                    <select id="cihaz_kurumid" class="form-control" name="cihaz_kurumid" required>
                                        <option value="">Seçiniz</option>
                                        @foreach ($kurum as $value)
                                            <option value="{{ $value->ayar_kurumid }}">{{ $value->ayar_kurum }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="cihaz_adi-field" class="form-label">Cihaz Adı</label>
                                <input type="text" id="cihaz_adi" class="form-control" name="cihaz_adi" />
                            </div>
                            <div class="col-lg-3">
                                <label for="cihaz_ip-field" class="form-label">Cihaz İp</label>
                                <input type="text" id="cihaz_ip" class="form-control" name="cihaz_ip" />
                            </div>
                            <div class="col-lg-3">
                                <label for="companyname-field" class="form-label">Cihaz Port</label>
                                <input type="number" id="cihaz_port" class="form-control" name="cihaz_port" />
                            </div>
                            <div class="col-lg-4">
                                <label for="cihaz_model-field" class="form-label">Cihaz Model</label>
                                <input type="text" id="cihaz_model" class="form-control" name="cihaz_model" />
                            </div>
                            <div class="col-lg-4">
                                <div>
                                    <label for="cihaz_gecistipi-field" class="form-label">Geçiş Tipi </label>
                                    <select class="form-select" id="cihaz_gecistipi" name="cihaz_gecistipi">
                                        <option value="3">Giriş/Çıkış</option>
                                        <option value="1">Giriş</option>
                                        <option value="2">Çıkış</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="okul-field" class="form-label">Açıklama</label>
                                <input type="text" id="cihaz_aciklama" placeholder="Zorunlu Değil" class="form-control"
                                    name="cihaz_aciklama" />
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
            $('#ajax-crud-cihaz').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('cihaz.listesi') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // Sıralama için eklendi
                    {
                        data: 'ayar_kurum',
                        name: 'ayar_kurum'
                    },
                    {
                        data: 'cihaz_adi',
                        name: 'cihaz_adi'
                    },
                    {
                        data: 'cihaz_gecistipi',
                        name: 'cihaz_gecistipi',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span style="color:green;font-weight:bold;">Giriş</span>';
                            } else if (data == 2) {
                                return '<span style="color:red;font-weight:bold;">Çıkış</span>';
                            } else {
                                return '<span style="color:blue;font-weight:bold;">Giriş/Çıkış</span>';
                            }
                        }
                    },
                    {
                        data: 'cihaz_ip',
                        name: 'cihaz_ip'
                    },
                    {
                        data: 'cihaz_port',
                        name: 'cihaz_port'
                    },
                    {
                        data: 'cihaz_model',
                        name: 'cihaz_model'
                    },
                    {
                        data: 'seri_no',
                        name: 'seri_no'
                    },
                    {
                        data: 'mac_adresi',
                        name: 'mac_adresi'
                    },
                    {
                        data: 'kart_sayisi',
                        name: 'kart_sayisi'
                    },
                    {
                        data: 'son_baglanti_zamani',
                        name: 'son_baglanti_zamani',
                        searchable: false,
                        render: function(data) {
                            if (!data) return '';
                            const date = new Date(data);
                            return date.toLocaleString('tr-TR', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit'
                            }).replace(/\./g, '-');
                        }
                    },
                    {
                        data: 'baglanti_durumu',
                        name: 'baglanti_durumu',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span style="color:green;font-weight:bold;">Bağlı</span>';
                            } else {
                                return '<span style="color:red;font-weight:bold;">Bağlı Değil</span>';
                            }
                        }
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
                    [5, 'asc']
                ]
            });
        });
        function add() {
            $('#CihazForm').trigger("reset");
            $('#CihazModal').modal('Add Cihaz');
            $('#cihaz-modal').modal('show');
            $('#cihaz_id').val('');
        }
        $('#CihazForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('cihaz.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#cihaz-modal").modal('hide');
                    $('#ajax-crud-cihaz').DataTable().ajax.reload(null, false);

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
        function deleteFunc(cihaz_id) {
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
                        url: "{{ route('cihaz.delete') }}",
                        data: {
                            cihaz_id: cihaz_id
                        },
                        dataType: 'json',
                        success: function(res) {
                            // DataTable'ı güncelle
                            $('#ajax-crud-cihaz').DataTable().ajax.reload();

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
    </script>
@endsection
