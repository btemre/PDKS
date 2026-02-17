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
                                    @if (Auth::guard('web')->user()->can('kullanici.ekle'))
                                        <a class="btn btn-primary" href="{{ route('add.admin') }}">Yeni Kullanıcı Ekle</a>
                                    @endif
                                    @if (Auth::guard('web')->user()->can('kullanici.log'))
                                        <a class="btn btn-info" href="{{ route('all.adminlog') }}">Kullanıcı Logları</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-crud-datatable"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Birim</th>
                                        <th>İsim</th>
                                        <th>Kullanıcı Adı</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Yönetici</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alladmin as $key => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->birim->birim_ad }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->username }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>
                                                @foreach ($item->roles as $role)
                                                    <span class="badge bg-primary">{{ $role->name ?? 'N/A' }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $item->yonetici == 1 ? 'Yönetici' : 'Kullanıcı' }}</td>

                                            <td>
                                                @if (Auth::guard('web')->user()->can('kullanici.duzenle'))
                                                    <a href="{{ route('edit.admin', $item->id) }}"
                                                        class="btn btn-sm btn-info">Düzenle</a>
                                                @endif
                                                @if (Auth::guard('web')->user()->can('kullanici.sil'))
                                                    <a href="{{ route('delete.admin', $item->id) }}"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                                        Sil
                                                    </a>
                                                @endif
                                            </td>
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
    <!-- Modal -->
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#ajax-crud-datatable').DataTable({
                processing: true,
                serverSide: false,
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
                    [0, 'asc']
                ]
            });
        });
    </script>
@endsection
