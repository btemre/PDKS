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
                        <div class="card-body">
                             <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                    {{-- Manuel ekleme için yetki kontrolü --}}
                                    @if (Auth::guard('web')->user()->can('ayar.ekle'))
                                        <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Yeni Kayıt Ekle</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-crud-dt-envanter"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Bilgisayar Adı</th>
                                        <th>Kullanıcı Adı</th>
                                        <th>IP Adresi</th>
                                        <th>İşlemci</th>
                                        <th>İşletim Sistemi</th>
                                        <th>RAM</th>
                                        <th>Disk</th>
                                        <th>Seri No</th>
                                        <th>Son Güncelleme</th>
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
    <!-- Modal (Ekleme/Düzenleme) -->
    <div id="envanter-modal" class="modal fade" tabindex="-1" aria-labelledby="ModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="ModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="EnvanterForm" id="EnvanterForm">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Temel Bilgiler -->
                            <div class="col-lg-4">
                                <label class="form-label">Bilgisayar Adı</label>
                                <input type="text" id="bilgisayar_adi" class="form-control" name="bilgisayar_adi" />
                            </div>
                             <div class="col-lg-4">
                                <label class="form-label">Kullanıcı Adı</label>
                                <input type="text" id="kullanici_adi" class="form-control" name="kullanici_adi" />
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">Domain</label>
                                <input type="text" id="domain" class="form-control" name="domain" />
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">İşletim Sistemi</label>
                                <input type="text" id="isletim_sistemi" class="form-control" name="isletim_sistemi" />
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">İ.S. Sürümü</label>
                                <input type="text" id="isletim_sistemi_surumu" class="form-control" name="isletim_sistemi_surumu" />
                            </div>

                            <!-- Donanım Bilgileri -->
                             <div class="col-lg-12">
                                <label class="form-label">İşlemci Modeli</label>
                                <input type="text" id="islemci_modeli" class="form-control" name="islemci_modeli" />
                            </div>
                             <div class="col-lg-4">
                                <label class="form-label">Çekirdek Sayısı</label>
                                <input type="text" id="islemci_cekirdek_sayisi" class="form-control" name="islemci_cekirdek_sayisi" />
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">RAM Boyutu</label>
                                <input type="text" id="ram_boyutu" class="form-control" name="ram_boyutu" />
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">RAM Türü</label>
                                <input type="text" id="ram_turu" class="form-control" name="ram_turu" />
                            </div>
                             <div class="col-lg-4">
                                <label class="form-label">Disk Boyutu</label>
                                <input type="text" id="disk_boyutu" class="form-control" name="disk_boyutu" />
                            </div>
                             <div class="col-lg-4">
                                <label class="form-label">Disk Türü</label>
                                <input type="text" id="disk_turu" class="form-control" name="disk_turu" />
                            </div>
                             <div class="col-lg-4">
                                <label class="form-label">Anakart Modeli</label>
                                <input type="text" id="anakart_modeli" class="form-control" name="anakart_modeli" />
                            </div>
                             <div class="col-lg-12">
                                <label class="form-label">Ekran Kartı</label>
                                <input type="text" id="ekran_karti" class="form-control" name="ekran_karti" />
                            </div>

                             <!-- Ağ Bilgileri -->
                            <div class="col-lg-6">
                                <label class="form-label">IP Adresi</label>
                                <input type="text" id="ip_adresi" class="form-control" name="ip_adresi" />
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">MAC Adresi</label>
                                <input type="text" id="mac_adresi" class="form-control" name="mac_adresi" />
                            </div>

                            <!-- Ek Bilgiler -->
                            <div class="col-lg-6">
                                <label class="form-label">Antivirüs</label>
                                <input type="text" id="antivirus" class="form-control" name="antivirus" />
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Ofis Versiyonu</label>
                                <input type="text" id="ofis_versiyonu" class="form-control" name="ofis_versiyonu" />
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Seri Numarası</label>
                                <input type="text" id="seri_numarasi" class="form-control" name="seri_numarasi" />
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">BIOS Sürümü</label>
                                <input type="text" id="bios_surumu" class="form-control" name="bios_surumu" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                             <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
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
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            $('#ajax-crud-dt-envanter').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('bilgisayar.listesi') }}",
                columns: [
                    { data: 'bilgisayar_adi', name: 'bilgisayar_adi' },
                    { data: 'kullanici_adi', name: 'kullanici_adi' },
                    { data: 'ip_adresi', name: 'ip_adresi' },
                    { data: 'islemci_modeli', name: 'islemci_modeli' },
                    { data: 'isletim_sistemi', name: 'isletim_sistemi' },
                    { data: 'ram_boyutu', name: 'ram_boyutu' },
                    { data: 'disk_boyutu', name: 'disk_boyutu' },
                    { data: 'seri_numarasi', name: 'seri_numarasi' },
                    { data: 'updated_at', name: 'updated_at', 
                        render: function(data, type, row) {
                            return new Date(data).toLocaleString('tr-TR');
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                language: {
                    url: '{{ url('build/json/datatabletr.json') }}'
                },
                lengthMenu: [[-1, 25, 50, 100], ["Tümü", 25, 50, 100 ]],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength',
                    'excelHtml5',
                    'print'
                ],
                order: [
                    [8, 'desc']
                ]
            });
        });
        function add() {
            $('#EnvanterForm').trigger("reset");
            $('#ModalLabel').html("Yeni Envanter Kaydı Ekle");
            $('#envanter-modal').modal('show');
            $('#id').val('');
        } 
        $('#EnvanterForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('bilgisayar.guncelle') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#envanter-modal").modal('hide');
                    $('#ajax-crud-dt-envanter').DataTable().ajax.reload(null, false);
                    Swal.fire({
                        title: "Başarılı!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "Tamam"
                    });
                    $("#btn-save").html('Kaydet').attr("disabled", false);
                },
                error: function(xhr) {
                    // Hata durumunda validasyon mesajlarını göstermek için
                    var errorMsg = "Bir hata oluştu.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = '';
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errorMsg += value + '<br>';
                        });
                    }
                    Swal.fire({
                        title: "Hata!",
                        html: xhr.responseJSON?.message || errorMsg,
                        icon: "error",
                        confirmButtonText: "Tamam"
                    });
                    $("#btn-save").html('Kaydet').attr("disabled", false);
                }
            });
        });
    </script>
@endsection

