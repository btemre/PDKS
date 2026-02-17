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
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">{{ $title }}</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('update.admin', $admin->id) }}" method="POST" class="row g-3"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="live-preview">
                                    <div class="row gy-4">
                                        <div class="col-md-3">
                                            <label for="basiInput" class="form-label">Ad Soyad</label>
                                            <input type="text" class="form-control" autocomplete="off" name="name" value="{{ $admin->name }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="basiInput" class="form-label">Kullanıcı Adı</label>
                                            <input type="text" class="form-control" autocomplete="off" name="username" value="{{ $admin->username }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="basiInput" class="form-label">Email</label>
                                            <input type="email" class="form-control" autocomplete="off" name="email" value="{{ $admin->email }}">
                                        </div>

                                        <div class="col-md-2">
                                            <label for="basiInput" class="form-label">Kullanıcı Role</label>
                                            <select name="roles" class="form-select mb-3"
                                                aria-label="Default select example">
                                                <option value="" selected>Seçim Yapınız</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" {{$admin->hasRole($role->name) ? 'selected' : ''}}  >{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="yonetici" class="form-label">Kullanıcı Türü</label>
                                            <select class="form-control" name="yonetici">
                                                <option value="1" {{ $admin->yonetici == 1 ? 'selected' : '' }}>Yönetici</option>
                                                <option value="0" {{ $admin->yonetici == 0 ? 'selected' : '' }}>Kullanıcı</option>
                                            </select>
                                        </div>
                                        
                                        
                                    </div>

                                    <div class="col-12 mt-3">
                                        <button class="btn btn-primary" type="submit">Güncelle</button>
                                    </div>
                                </div>
                            </form>
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
                order: [
                    [2, 'asc']
                ]
            });
        });
    </script>
@endsection
