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
                                <li class="breadcrumb-item active">{{ $title }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- tarih Filtreleme: --}}

                            <div class="row">
                                <div class="col col-sm-10">Tarihe Göre Filtreleme Yapabilirsiniz</div>
                                <div class="col col-sm-2">
                                    <input type="text" id="date_range" class="form-control" readonly />
                                </div>
                            </div>


                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                </div>
                            </div>
                            <table id="ajax-crud-gelmeyen"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Birim</th>
                                        <th>Statu</th>
                                        <th>Personel</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let start_date = moment().startOf('day');
            let end_date = moment().endOf('day');

            $('#date_range').daterangepicker({
                locale: {
                    applyLabel: 'Aralığı Seç',
                    cancelLabel: 'Vazgeç',
                    format: 'YYYY-MM-DD',
                    customRangeLabel: 'Kendim Seçeceğim',
                    //separator: ' & ',
                    fromLabel: 'From',
                    //toLabel: '&',
                    weekLabel: 'W',
                    daysOfWeek: ['Pzr', 'Pts', 'Sal', 'Çar', 'Per', 'Cum', 'Cts'],
                    monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos',
                        'Eylül', 'Ekim', 'Kasım', 'Aralık'
                    ],
                    firstDay: 1
                },
                autoUpdateInput: true,
                startDate: start_date,
                endDate: end_date,
                ranges: {
                    'Bugün': [moment(), moment()],
                    'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
                    'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
                    'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'Bu Yıl': [moment().startOf('year'), moment().endOf('year')],
                    'Geçen Yıl': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year')
                        .endOf('year')
                    ]
                }
            }, function(start, end) {
                $('#date_range').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                table.ajax.reload();
            });

            let table = $('#ajax-crud-gelmeyen').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pdks.gelmeyen') }}",
                    data: function(d) {
                        d.date_range = $('#date_range').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'birim_ad',
                        name: 'birim_ad'
                    },
                    {
                        data: 'durum_ad',
                        name: 'durum_ad',
                        render: function(data) {
                            let badgeClass = 'badge bg-secondary';
                            if (data.includes('GELMEDİ')) badgeClass = 'badge bg-danger';
                            else if (data.includes('GELDİ')) badgeClass = 'badge bg-success';
                            else if (data.includes('İZİNLİ')) badgeClass = 'badge bg-warning';
                            else if (data.includes('RESMİ TATİL')) badgeClass = 'badge bg-info';
                            else if (data.includes('HAFTA SONU')) badgeClass = 'badge bg-primary';
                            else if (data.includes('TANIMSIZ')) badgeClass = 'badge bg-dark';
                            return '<span class="' + badgeClass + '">' + data + '</span>';
                        }
                    },

                    {
                        data: 'personel_adsoyad',
                        name: 'personel_adsoyad'
                    },
                    {
                        data: 'tarih',
                        name: 'tarih',
                        searchable: false,
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g,
                                '-') : '';
                        }
                    }
                ]
            });
        });
    </script>
@endsection
