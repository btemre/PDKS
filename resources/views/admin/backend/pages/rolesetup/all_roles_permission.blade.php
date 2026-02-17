@extends('admin.admin_dashboard')
@section('title')
    {{ $title }}
@endsection
@section('page-title')
    {{ $pagetitle }}
@endsection
<style>
    td .badge {
        display: inline-block;
        margin: 2px;
        white-space: normal;
    }

    .yetkiler-cell {
        max-height: 100px;
        /* En fazla 100px yükseklik */
        overflow-y: auto;
        /* Fazlasını scroll ile göster */
    }
</style>
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
                                    @if (Auth::guard('web')->user()->can('yetkilendirme.rolizin.ekle'))
                                        <a class="btn btn-primary" href="{{ route('add.roles.permission') }}">Yeni Rol
                                            Ekle</a>
                                    @endif
                                </div>
                            </div>
                            <table id="ajax-crud-datatable"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:1%;">S</th>
                                        <th>Rol Adı</th>
                                        <th>Yetkiler</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                @php
                                                    $yetkilerListesi = $item->permissions->pluck('name')->toArray();
                                                    $yetkilerImplode = implode(', ', $yetkilerListesi);

                                                    // Yetkileri prefix'e göre grupla
$gruplar = [];
foreach ($yetkilerListesi as $yetki) {
    $parcalar = explode('.', $yetki, 2); // ilk noktadan ayır
                                                        $grupAdi = $parcalar[0];
                                                        $gruplar[$grupAdi][] = $yetki;
                                                    }
                                                @endphp

                                                <!-- Modal Açma Butonu -->
                                                <span class="badge bg-primary" style="cursor: pointer;"
                                                    title="{{ $yetkilerImplode }}" data-bs-toggle="modal"
                                                    data-bs-target="#yetkilerModal{{ $item->id }}">
                                                    {{ count($yetkilerListesi) }} Yetki
                                                </span>

                                                <!-- Modal -->
                                                <div class="modal fade" id="yetkilerModal{{ $item->id }}"
                                                    tabindex="-1" aria-labelledby="yetkilerModalLabel{{ $item->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="yetkilerModalLabel{{ $item->id }}">
                                                                    Yetkiler - {{ $item->name }}
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Kapat"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul class="list-group">
                                                                    @foreach ($gruplar as $grup => $yetkiler)
                                                                        @foreach ($yetkiler as $yetki)
                                                                            <li class="list-group-item">{{ $yetki }}
                                                                            </li>
                                                                        @endforeach
                                                                        @if (!$loop->last)
                                                                            <li class="list-group-item p-0 border-0">
                                                                                <hr class="my-1">
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Kapat</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if (Auth::guard('web')->user()->can('yetkilendirme.rolizin.duzenle'))
                                                    <a href="{{ route('admin.edit.roles', $item->id) }}"
                                                        class="btn btn-sm btn-info">Düzenle</a>
                                                @endif
                                                @if (Auth::guard('web')->user()->can('yetkilendirme.rolizin.sil'))
                                                    <a href="{{ route('admin.delete.roles', $item->id) }}"
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
                order: [
                    [0, 'asc']
                ]
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
