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
                                <li class="breadcrumb-item"><a href="{{ route('personel.kartlistesi') }}">Kart İşlemleri</a></li>
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
                                    @if (Auth::guard('web')->user()->can('personel.ekle'))
                                    <a class="btn btn-success" onclick="add()" href="javascript:void(0)">Kart Kullanım Ekle</a>
                                    @endif

                                </div>
                            </div>
                            <table id="ajax-crud-datatable"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:5px;">Sıra</th>
                                        <th>Ad Soyad</th>
                                        <th>Başlangıç</th>
                                        <th>Bitiş</th>
                                        <th>İşlem</th>
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
    <div id="personel-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <form method="POST" action="javascript:void(0)" name="PersonelForm" id="PersonelForm"
                enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <input type="hidden" id="id-field" />
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div>
                                <label for="durum-field" class="form-label">Personel</label>
                                <select id="personel_id" class="form-control" name="personel_id">
                                    <option value="">Seçiniz</option>
                                    @foreach ($personel as $value)
                                        <option value="{{ $value->personel_id }}">{{ $value->personel_adsoyad }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div>
                                <label for="star_value-field" class="form-label">Başlangıç Tarihi</label>
                                <input type="date" id="baslangic_tarihi" class="form-control"
                                    name="baslangic_tarihi" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div>
                                <label for="star_value-field" class="form-label">Bitiş Tarihi</label>
                                <input type="date" id="bitis_tarihi" class="form-control"
                                    name="bitis_tarihi" />
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="submit" class="btn btn-success" id="btn-save">Kaydet</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
<script type="text/javascript">
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Bu ayın başı – sonu
    var start_date = moment().startOf("month");
    var end_date   = moment().endOf("month");

    // Date Range Picker (tek format: YYYY-MM-DD)
    $("#date_range").daterangepicker(
        {
            autoUpdateInput: true,
            startDate: start_date,
            endDate: end_date,
            locale: {
                applyLabel: "Aralığı Seç",
                cancelLabel: "Vazgeç",
                format: "YYYY-MM-DD",
                customRangeLabel: "Kendim Seçeceğim",
                separator: " - ",
                fromLabel: "From",
                toLabel: "To",
                weekLabel: "W",
                daysOfWeek: ["Pzr", "Pts", "Sal", "Çar", "Per", "Cum", "Cts"],
                monthNames: [
                    "Ocak","Şubat","Mart","Nisan","Mayıs","Haziran",
                    "Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"
                ],
                firstDay: 1,
            },
            ranges: {
                "Bu Ay": [moment().startOf("month"), moment().endOf("month")],
                "Geçen Ay": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
                "Önceki Ay": [
                    moment().subtract(2, "month").startOf("month"),
                    moment().subtract(2, "month").endOf("month"),
                ],
                "Bu Yıl": [moment().startOf("year"), moment().endOf("year")],
                "Geçen Yıl": [
                    moment().subtract(1, "year").startOf("year"),
                    moment().subtract(1, "year").endOf("year"),
                ],
                "Önceki Yıl": [
                    moment().subtract(2, "year").startOf("year"),
                    moment().subtract(2, "year").endOf("year"),
                ],
            },
        },
        function (start, end) {
            $("#date_range").val(
                start.format("YYYY-MM-DD") + " - " + end.format("YYYY-MM-DD")
            );
            table.ajax.reload(); // tarih değişince tabloyu yenile
        }
    );

    // İlk değer (bu ay)
    $("#date_range").val(
        start_date.format("YYYY-MM-DD") + " - " + end_date.format("YYYY-MM-DD")
    );

    // DataTable
    let table = $("#ajax-crud-datatable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('personel.kartgecmisi') }}",
            data: function (d) {
                // → her istekte tarih aralığını gönder!
                d.date_range = $("#date_range").val();
            },
        },
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
            { data: "personel_adsoyad", name: "personel_adsoyad" },
            {
                data: 'baslangic_tarihi',
                name: 'baslangic_tarihi',
                searchable: false,
                render: function (data) {
                    return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
                }
            },
            {
                data: 'bitis_tarihi',
                name: 'bitis_tarihi',
                searchable: false,
                render: function (data) {
                    return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
                }
            },
            { data: "action", name: "action", orderable: false, searchable: false },
        ],
        language: {
            url: "{{ url('build/json/datatabletr.json') }}",
        },
        order: [[1, "asc"]],
        // İsteğe bağlı: sayfa değişince de güncel tarih aralığı kalsın
        drawCallback: function () {},
    });

    // İptal edilirse filtreyi temizle
    $("#date_range").on("cancel.daterangepicker", function (ev, picker) {
        $(this).val("");
        table.ajax.reload();
    });
});

    function add() {
        $('#PersonelForm').trigger("reset");
        $('#exampleModalLabel').text('Personel Ekle');
        
        // Ekleme modunda dropdown aktif
        $('#personel_id').prop('disabled', false);
        
        $('#personel-modal').modal('show');
        $('#id').val('');
    }

    function editFunc(id) {
        $.ajax({
            type: "POST",
            url: "{{ route('kartgecmis.edit') }}",
            data: {
                id: id
            },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#exampleModalLabel').text("Personel Düzenle");
                    
                    // Düzenleme modunda dropdown deaktif yap ve ilgili personeli seç
                    $('#personel_id').prop('disabled', true).val(res.data.personel_id);
                    
                    $('#baslangic_tarihi').val(res.data.baslangic_tarihi);
                    $('#bitis_tarihi').val(res.data.bitis_tarihi);
                    $('#id').val(res.data.id || id);
                    
                    $('#personel-modal').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: "Hata!",
                    text: xhr.responseJSON?.message || "Bir hata oluştu.",
                    icon: "error",
                    confirmButtonText: "Tamam"
                });
            }
        });
    }

    function deleteFunc(id) {
        Swal.fire({
            title: 'Silmek istediğinize emin misiniz?',
            text: "Bu işlem geri alınamaz!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'Hayır, iptal et'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('kartgecmis.delete') }}",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        $('#ajax-crud-datatable').DataTable().ajax.reload();

                        Swal.fire({
                            title: res.status === 'success' ? 'Başarılı!' : 'Hata!',
                            text: res.message,
                            icon: res.status === 'success' ? 'success' : 'error',
                            confirmButtonText: 'Tamam'
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Hata!",
                            text: xhr.responseJSON?.message || "Bir hata oluştu.",
                            icon: "error",
                            confirmButtonText: "Tamam"
                        });
                    }
                });
            }
        });
    }

    $('#PersonelForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        // Eğer dropdown disabled ise, değeri manuel olarak ekle
        if ($('#personel_id').prop('disabled')) {
            formData.set('personel_id', $('#personel_id').val());
        }
        
        $.ajax({
            type: 'POST',
            url: "{{ route('kartgecmis.store') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $("#btn-save").html('Gönderiliyor...').attr("disabled", true);
            },
            success: function(response) {
                $("#personel-modal").modal('hide');
                $('#ajax-crud-datatable').DataTable().ajax.reload(null, false);

                Swal.fire({
                    title: "Başarılı!",
                    text: response.message,
                    icon: "success",
                    confirmButtonText: "Tamam"
                });

                $("#btn-save").html('Kaydet').attr("disabled", false);
            },
            error: function(xhr) {
                Swal.fire({
                    title: "Hata!",
                    text: xhr.responseJSON?.message || "Bir hata oluştu.",
                    icon: "error",
                    confirmButtonText: "Tamam"
                });

                $("#btn-save").html('Kaydet').attr("disabled", false);
            }
        });
    });
</script>
@endsection

