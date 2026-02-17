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
                        <h4 class="mb-sm-0">{{ $title }}</h4>
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
                                        <th>Sıra</th>
                                        <th>Birimi</th>
                                        <th>Ad Soyad</th>
                                        <th>Sicil No</th>
                                        <th>Statü</th>
                                        <th>Ünvan</th>
                                        <th>Giriş Tarihi</th>
                                        <th>Kan Grubu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($personeller as $key => $p)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $p->birim_ad }}</td>
                                            <td>{{ $p->personel_adsoyad }}</td>
                                            <td>{{ $p->personel_sicilno }}</td>
                                            <td>{{ $p->durum_ad }}</td>
                                            <td>{{ $p->unvan_ad }}</td>
                                            <td>{{ tarih($p->personel_isegiristarih) }}</td>
                                            <td>{{ $p->personel_kan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#ajax-crud-datatable').DataTable({

                responsive: true,
                processing: true,
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
    </script>
@endsection
