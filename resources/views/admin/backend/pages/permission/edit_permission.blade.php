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
                            <form action="{{ route('update.permission') }}" method="POST" class="row g-3"
                                multipart="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ $permissions->id }}">
                                <div class="live-preview">
                                    <div class="row gy-4">
                                        <div class="col-xxl-3 col-md-6">
                                            <div>
                                                <label for="basiInput" class="form-label">Persmission Name</label>
                                                <input type="text" class="form-control" name="name" value="{{ $permissions->name }}">
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-md-6">
                                             <label for="basiInput" class="form-label">Persmission Group</label>
                                            <select name="group_name" class="form-select mb-3" aria-label="Default select example">
                                                <option value="" selected>Seçim Yapınız </option>
                                                <option value="Personel" {{ $permissions->group_name == 'Personel' ? 'selected' : '' }}>Personel</option>
                                                <option value="PDKS" {{ $permissions->group_name == 'PDKS' ? 'selected' : '' }}>PDKS</option>
                                                <option value="Trafik" {{ $permissions->group_name == 'Trafik' ? 'selected' : '' }}>Trafik</option>
                                                <option value="Evrak" {{ $permissions->group_name == 'Evrak' ? 'selected' : '' }}>Evrak</option>
                                                <option value="Tanimlama" {{ $permissions->group_name == 'Tanimlama' ? 'selected' : '' }}>Tanımlama</option>
                                                <option value="Yetkilendirme" {{ $permissions->group_name == 'Yetkilendirme' ? 'selected' : '' }}>Yetkilendirme</option>
                                                <option value="Kullanici" {{ $permissions->group_name == 'Kullanici' ? 'selected' : '' }}>Kullanici</option>
                                                <option value="Izin" {{ $permissions->group_name == 'Izin' ? 'selected' : '' }}>İzin</option>
                                                <option value="Ayar" {{ $permissions->group_name == 'Ayar' ? 'selected' : '' }}>Ayar</option>
                                                <option value="Tunel" {{ $permissions->group_name == 'Tunel' ? 'selected' : '' }}>Tünel</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12"><button class="btn btn-primary" type="submit">Güncelle</button></div>
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
