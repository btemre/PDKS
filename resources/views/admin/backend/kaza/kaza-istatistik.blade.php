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
                                <li class="breadcrumb-item"><a href="{{route('kaza.listesi')}}">Trafik Kazaları</a></li>
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

                            </div>
                            <table id="ajax-crud-dt-kazaistatistik"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th>Plaka</th>
                                        <th>Araç Cinsi</th>
                                        <th>KKNO</th>
                                        <th>KM</th>
                                        <th>Kaza Yeri</th>
                                        <th>Kaza</th>
                                        <th>Vefat</th>
                                        <th>Çarpışma</th>
                                        <th>Devrilme</th>
                                        <th>Cisme Çarpma</th>
                                        <th>Durana Çarpma</th>
                                        <th>Yayaya Çarpma</th>
                                        <th>Araçtan Düşme</th>
                                        <th>Diğer</th>
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



    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#ajax-crud-dt-kazaistatistik').DataTable({
                processing: true,
                serverSide: true,
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
                ],
                ajax: "{{ route('kaza.istatistik') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {
                        data: 'kaza_tarih',
                        name: 'kaza_tarih',
                        render: function (data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
                        }
                    },
                    {
                        data: 'kaza_saat',
                        name: 'kaza_saat',
                        render: function (data) {
                            return data ? data.slice(0, 5) : '';
                        }
                    },
                    { data: 'kaza_plaka', name: 'kaza_plaka' },
                    { data: 'kaza_arac', name: 'kaza_arac' },
                    { data: 'kaza_kkno', name: 'kaza_kkno' },
                    { data: 'kaza_km', name: 'kaza_km' },
                    { data: 'kaza_yeri', name: 'kaza_yeri' },
                    { data: 'kaza_sayisi', name: 'kaza_sayisi' },
                    { data: 'kaza_vefat', name: 'kaza_vefat' },
                    { data: 'kaza_carpisma', name: 'kaza_carpisma' },
                    { data: 'kaza_devrilme', name: 'kaza_devrilme' },
                    { data: 'kaza_cismecarpma', name: 'kaza_cismecarpma' },
                    { data: 'kaza_duranaracacarpma', name: 'kaza_duranaracacarpma' },
                    { data: 'kaza_yayacarpma', name: 'kaza_yayacarpma' },
                    { data: 'kaza_aractandusme', name: 'kaza_aractandusme' },
                    { data: 'kaza_diger', name: 'kaza_diger' },
                    // { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'desc']]
            });

        });
    </script>
@endsection