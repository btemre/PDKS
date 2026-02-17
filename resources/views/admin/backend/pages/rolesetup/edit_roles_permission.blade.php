@extends('admin.admin_dashboard')
@section('title')
    {{ $title }}
@endsection
@section('page-title')
    {{ $pagetitle }}
@endsection
@section('admin')
    <style>
        .form-check-label {
            text-transform: capitalize
        }
    </style>
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
                            <form action="{{ route('admin.roles.update', $roles->id) }}" method="POST" class="row g-3"
                                multipart="multipart/form-data">
                                @csrf
                                <div class="live-preview">

                                    <div class="col-lg-4 col-md-6">
                                        <label for="basiInput" class="form-label">Role Name</label>
                                        <h4>{{ $roles->name }}</h4>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="formCheck1">
                                                <label class="form-check-label" for="formCheck1">
                                                    Permission All
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    @foreach ($permission_groups as $group)
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="col-lg-4 col-md-6">
                                                    <div>
                                                        @php
                                                            $permissions = App\Models\User::getpermissionByGroupName($group->group_name);
                                                        @endphp
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" value="" id="formCheck2"
                                                                {{ App\Models\User::roleHasPermissions($roles, $permissions) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="formCheck2">
                                                                {{ $group->group_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-9">


                                                @foreach ($permissions as $permission)
                                                    <div class="col-lg-4 col-md-6">
                                                        <div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" name="permission[]"
                                                                    value="{{ $permission->id }}" type="checkbox"
                                                                    id="formCheckDefault{{ $permission->id }}" {{ App\Models\User::roleHasPermissionSafe($roles, $permission->name) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="formCheckDefault{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <br>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-12"><button class="btn btn-primary" type="submit">Yetkiyi Güncelle</button></div>
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
    <script>
        $('#formCheck1').click(function() {
            if ($(this).is(':checked')) {
                $('input[type=checkbox]').prop('checked', true);
            } else {
                $('input[type=checkbox]').prop('checked', false);
            }

        })
    </script>
@endsection
