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
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Anasayfa</a></li>
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
                        <small class="text-muted">Son 3 yıldaki zorunlu izin (İzin Türü:Yıllık İzin) kalan gün sayıları</small>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="row g-4 mb-3">
                            <div class="col-sm-12">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information-outline"></i>
                                    <strong>Bilgi:</strong> Bu rapor personellerin zorunlu izin türündeki (ID: 1) kalan gün sayılarını göstermektedir. 
                                    "-" işareti o yıl için izin hakkı bulunmadığını belirtir.
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="ajax-dt-izinzorunlu"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Ad Soyad</th>
                                        <th>Statü</th>
                                        <th>Ünvan</th>
                                        @foreach($displayYears as $year)
                                            <th class="text-center">{{ $year }}<br><small class="text-muted">Kalan Gün</small></th>
                                        @endforeach
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
</div>

<script type="text/javascript">
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $('#ajax-dt-izinzorunlu').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('izin.zorunlu') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'personel_adsoyad', name: 'personel_adsoyad' },
            { data: 'durum_ad', name: 'durum_ad' },
            { data: 'unvan_ad', name: 'unvan_ad' },
            @foreach($displayYears as $year)
            {
                data: 'izin_kalan_{{ $year }}',
                name: 'izin_kalan_{{ $year }}',
                className: 'text-center',
                render: function(data, type, row) {
                    if (data === null || data === '-') {
                        return '<span class="text-muted">-</span>';
                    }
                    // Renk kodlama: 0 kırmızı, 1-5 turuncu, 6+ yeşil
                    if (data == 0) {
                        return '<span class="badge bg-danger">' + data + '</span>';
                    } else if (data > 0 && data <= 5) {
                        return '<span class="badge bg-warning text-dark">' + data + '</span>';
                    } else {
                        return '<span class="badge bg-success">' + data + '</span>';
                    }
                }
            },
            @endforeach
        ],
        order: [[2, 'desc']],
        language: {
            url: '{{ url('build/json/datatabletr.json') }}'
        },
        scrollX: true,
        fixedColumns: {
            leftColumns: 4
        },
        pageLength: -1,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tümü"]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="mdi mdi-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="mdi mdi-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="mdi mdi-printer"></i> Yazdır',
                className: 'btn btn-info btn-sm'
            }
        ]
    });
});
</script>

<style>
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.875em;
    padding: 0.5em 0.75em;
}
.dataTables_scrollX {
    overflow-x: auto;
}
</style>

@endsection