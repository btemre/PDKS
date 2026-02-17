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
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="row g-4 mb-3">

                            </div>
                            <table id="ajax-dt-izinkullanim"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Ad Soyad</th>
                                        <th>Statü</th>
                                        <th>Ünvan</th>
                                        <th>İlk işe Giriş</th>
                                        <th>Tecrübe</th>
                                        <th>İzin Türü</th>
                                        <th>İzin Yılı</th>
                                        <th>Kullanılan</th>
                                        <th>Hakedilen</th>
                                        <th>Kalan İzin</th>
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
            $('#ajax-dt-izinkullanims').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('personel.izinkullanim') }}",  // AJAX endpoint
                    dataSrc: function (json) {
                        console.log(json);  // Veriyi konsolda kontrol et
                        return json;  // Veriyi doğru şekilde döndür
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'personel_adsoyad', name: 'p.personel_adsoyad' },
                    { data: 'durum_ad', name: 'd.durum_ad' },
                    { data: 'unvan_ad', name: 'u.unvan_ad' },
                    { data: 'personel_isegiristarih', name: 'p.personel_isegiristarih' },
                    { data: 'tecrube', name: 'tecrube' },
                    { data: 'izin_ad', name: 'it.izin_ad' },
                    { data: 'izin_yil', name: 'i.izin_yil' },
                    { data: 'izin_suresi', name: 'izin_suresi' },
                    { data: 'izin_hakki', name: 'ich.izin_hakki' },
                    { data: 'Kalanizin', name: 'Kalanizin' },
                ],
                order: [[2, 'asc']]
            });

        });

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#ajax-dt-izinkullanim').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('personel.izinkullanim') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'personel_adsoyad', name: 'personel_adsoyad' },
                    { data: 'durum_ad', name: 'durum_ad' },
                    { data: 'unvan_ad', name: 'unvan_ad' },
                    {
                        data: 'personel_isegiristarih_formatted',
                        name: 'personel_isegiristarih',
                        render: function (data, type, row) {
                            // Görünüm için formatlı tarih, sıralama için orijinal tarih
                            if (type === 'display') {
                                return data;
                            }
                            // Sıralama için orijinal tarih değeri
                            return row.personel_isegiristarih;
                        }
                    },
                    {
                        data: 'tecrube',
                        name: 'tecrube_numeric',
                        render: function (data, type, row) {
                            if (type === 'sort') {
                                return row.tecrube_numeric;
                            }
                            return data;
                        }
                    },
                    { data: 'izin_ad', name: 'izin_ad' },
                    {
                        data: 'izin_yil',
                        name: 'izin_yil_numeric',
                        render: function (data, type, row) {
                            if (type === 'sort') {
                                return row.izin_yil_numeric;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'izin_suresi',
                        name: 'izin_suresi_numeric',
                        render: function (data, type, row) {
                            if (type === 'sort') {
                                return row.izin_suresi_numeric;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'izin_hakki',
                        name: 'izin_hakki_numeric',
                        render: function (data, type, row) {
                            if (type === 'sort') {
                                return row.izin_hakki_numeric;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'Kalanizin',
                        name: 'Kalanizin_numeric',
                        render: function (data, type, row) {
                            if (type === 'sort') {
                                return row.Kalanizin_numeric;
                            }
                            return data;
                        }
                    }
                ],
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
                    [1, 'desc']
                ]
            });
        });
    </script>

@endsection