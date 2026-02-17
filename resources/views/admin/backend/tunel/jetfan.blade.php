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
                <div class="row mb-3">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header py-2">
                                <h6 class="card-title mb-0">Fiziksel Durum</h6>
                            </div>
                            <div class="card-body p-2">
                                <div style="height:500px;"><canvas id="fizikselChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header py-2">
                                <h6 class="card-title mb-0">Scada Durumu</h6>
                            </div>
                            <div class="card-body p-2">
                                <div style="height:500px;"><canvas id="scadaChart"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tünel</th>
                                            <th>Jet Fan Durumu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $toplamAktif = $durumlar->sum('aktif');
                                            $toplamPasif = $durumlar->sum('pasif');
                                            $toplamToplam = $durumlar->sum('toplam');
                                            $toplamYuzde =
                                                $toplamToplam > 0 ? round(($toplamAktif / $toplamToplam) * 100, 2) : 0;
                                        @endphp

                                        @foreach ($durumlar as $row)
                                            <tr>
                                                <td>{{ $row->tunel_kod }} - {{ $row->tunel_ad }}</td>
                                                <td>
                                                    <div class="d-flex flex-column" style="gap:4px;">
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: {{ $row->aktif > 0 ? ($row->aktif / $row->toplam) * 100 : 0 }}%;">
                                                                {{ $row->aktif }}
                                                            </div>
                                                            <div class="progress-bar bg-danger" role="progressbar"
                                                                style="width: {{ $row->pasif > 0 ? ($row->pasif / $row->toplam) * 100 : 0 }}%;">
                                                                {{ $row->pasif }}
                                                            </div>
                                                        </div>
                                                        <small class="text-center w-100">Toplam: {{ $row->toplam }} |
                                                            Yüzde: {{ $row->yuzde }}%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- Alt toplam satırı -->
                                        <tr class="fw-bold bg-light">
                                            <td>Toplam</td>
                                            <td>
                                                <div class="d-flex flex-column" style="gap:4px;">
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: {{ $toplamToplam > 0 ? ($toplamAktif / $toplamToplam) * 100 : 0 }}%;">
                                                            {{ $toplamAktif }}
                                                        </div>
                                                        <div class="progress-bar bg-danger" role="progressbar"
                                                            style="width: {{ $toplamToplam > 0 ? ($toplamPasif / $toplamToplam) * 100 : 0 }}%;">
                                                            {{ $toplamPasif }}
                                                        </div>
                                                    </div>
                                                    <small class="text-center w-100">Toplam: {{ $toplamToplam }} | Yüzde:
                                                        {{ $toplamYuzde }}%</small>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>


                                </table>
                            </div>
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
                                    @if (Auth::guard('web')->user()->can('jetfan.ekle'))
                                        <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Jetfan Ekle</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-crud-dt-jetfan"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>

                                        <th>Bulunduğu Yer</th>
                                        <th>Jefan</th>
                                        <th>Fiziksel</th>
                                        <th>Scada</th>
                                        <th>Açıklama</th>
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
    <div id="jetfan-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-soft-info p-3">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form method="POST" action="javascript:void(0)" name="JetfanForm" id="JetfanForm"
                    enctype="multipart/form-data">
                    <input type="hidden" name="jetfan_id" id="jetfan_id">
                    <div class="modal-body">
                        <input type="hidden" id="id-field" />
                        <div class="row g-3">

                            <div class="col-lg-6">
                                <label for="jetfan_tunel-field" class="form-label">Konum (Tünel)</label>
                                <select id="jetfan_tunel" name="jetfan_tunel" class="form-select">
                                    <option value="">Seçim Yapınız</option>
                                    @foreach ($tuneller as $tunel)
                                        <option value="{{ $tunel->tunel_id }}">{{ $tunel->tunel_ad }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <label for="jetfan_ad-field" class="form-label">Jet Fan</label>
                                <input type="text" id="jetfan_ad" class="form-control" name="jetfan_ad"
                                    autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <div>
                                    <label for="jetfan_durum-field" class="form-label">Durum</label>
                                    <select class="form-select" id="jetfan_durum" name="jetfan_durum">
                                        <option selected value="">Seçim Yapınız</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Pasif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="jetfan_fizikseltest-field" class="form-label">Fiziksel Test</label>
                                <select id="jetfan_fizikseltest" name="jetfan_fizikseltest" class="form-select">
                                    <option value="">Seçim Yapınız</option>
                                    @foreach ($testTurleri as $test)
                                        <option value="{{ $test->test_id }}">{{ $test->test_tur }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="jetfan_scadatest-field" class="form-label">Scada Test</label>
                                <select id="jetfan_scadatest" name="jetfan_scadatest" class="form-select">
                                    <option value="">Seçim Yapınız</option>
                                    @foreach ($testTurleri as $test)
                                        <option value="{{ $test->test_id }}">{{ $test->test_tur }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-12">
                                <div>
                                    <label for="jetfan_aciklama-field" class="form-label">Açıklama</label>
                                    <input type="text" id="jetfan_aciklama" class="form-control"
                                        name="jetfan_aciklama" autocomplete="off" />
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
            $('#ajax-crud-dt-jetfan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('jetfan.listesi') }}",
                columns: [{
                        data: 'jetfan_tunel',
                        name: 'jetfan_tunel'
                    },
                    {
                        data: 'jetfan_ad',
                        name: 'jetfan_ad'
                    },
                    {
                        data: 'jetfan_fizikseltest',
                        name: 'jetfan_fizikseltest'
                    },
                    {
                        data: 'jetfan_scadatest',
                        name: 'jetfan_scadatest'
                    },
                    {
                        data: 'jetfan_aciklama',
                        name: 'jetfan_aciklama'
                    },
                    {
                        data: 'jetfan_durum',
                        name: 'jetfan_durum',
                        render: function(data) {
                            return data == 1 ?
                                '<span class="badge bg-success">Aktif</span>' :
                                '<span class="badge bg-danger">Pasif</span>';
                        },
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
                lengthMenu: [[10, 25, 50, -1 ], [ 10, 25, 50, "Tümü"]],
                dom: 'Bfrtip',
                buttons: [
                    'pageLength',
                    'excelHtml5',
                    'print'
                ],
                order: [
                    [0, 'asc']
                ]
            });
        });

        function add() {
            $('#JetfanForm').trigger("reset");
            $('#JetfanModal').modal('Add Jetfan');
            $('#jetfan-modal').modal('show');
            $('#jetfan_id').val('');
        }

        function editFunc(jetfan_id) {
            $.ajax({
                type: "POST",
                url: "{{ route('jetfan.edit') }}",
                data: {
                    jetfan_id: jetfan_id
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#JetfanModal').html("Jetfan Düzenle");
                        $('#jetfan-modal').modal('show');
                        $('#jetfan_id').val(res.data.jetfan_id);
                        $('#jetfan_tunel').val(res.data.jetfan_tunel);
                        $('#jetfan_ad').val(res.data.jetfan_ad);
                        $('#jetfan_scadatest').val(res.data.jetfan_scadatest);
                        $('#jetfan_fizikseltest').val(res.data.jetfan_fizikseltest);
                        $('#jetfan_aciklama').val(res.data.jetfan_aciklama);
                        $('#jetfan_durum').val(res.data.jetfan_durum);


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

        function deleteFunc(jetfan_id) {
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
                        url: "{{ route('jetfan.delete') }}",
                        data: {
                            jetfan_id: jetfan_id
                        },
                        dataType: 'json',
                        success: function(res) {
                            // DataTable'ı güncelle
                            $('#ajax-crud-dt-jetfan').DataTable().ajax.reload();

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
        $('#JetfanForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: "{{ route('jetfan.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
                },
                success: function(response) {
                    $("#jetfan-modal").modal('hide');
                    $('#ajax-crud-dt-jetfan').DataTable().ajax.reload(null, false);

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fiziksel = @json($fizikselDurumlar);
            const scada = @json($scadaDurumlar);

            // Ortak chart options (küçük ve responsive)
            const baseOptions = {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            };

            new Chart(document.getElementById('fizikselChart'), {
                type: 'pie',
                data: {
                    labels: fiziksel.map(x => x.label),
                    datasets: [{
                        data: fiziksel.map(x => x.value)
                    }]
                },
                options: baseOptions
            });

            new Chart(document.getElementById('scadaChart'), {
                type: 'pie',
                data: {
                    labels: scada.map(x => x.label),
                    datasets: [{
                        data: scada.map(x => x.value)
                    }]
                },
                options: baseOptions
            });
        });
    </script>
@endsection
