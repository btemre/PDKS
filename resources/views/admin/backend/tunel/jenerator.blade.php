@extends('admin.admin_dashboard')
@section('title')
    {{ $title }}
@endsection
@section('page-title')
    {{ $pagetitle }}
@endsection
@section('admin')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0"> {{ $title }}</h4>
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
                <div class="card center">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0 d-none">{{ $pagetitle }}</h4>
                        <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
                        <div class="d-flex align-items-center px-2 py-1 rounded shadow-sm"
                            style="background-color:#064e03; color:#fff; font-weight:600; font-size:0.85rem;">
                            <i class="bi bi-fuel-pump-fill me-1"></i> Son Yakıt İkmali: {{ $toplamYakit }} Litre
                        </div>
                            <div class="d-flex align-items-center px-2 py-1 rounded shadow-sm"
                                style="background-color:#ef4444; color:#fff; font-weight:600; font-size:0.85rem;">
                                <i class="bi bi-x-circle-fill me-1"></i> Kırmızı: Geçmiş
                            </div>
                            <div class="d-flex align-items-center px-2 py-1 rounded shadow-sm"
                                style="background-color:#f97316; color:#fff; font-weight:600; font-size:0.85rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Turuncu: Yaklaşıyor
                            </div>
                            <div class="d-flex align-items-center px-2 py-1 rounded shadow-sm"
                                style="background-color:#22c55e; color:#fff; font-weight:600; font-size:0.85rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Yeşil: Geçerli
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
                                @if (Auth::guard('web')->user()->can('jenerator.ekle'))
                                    <a class="btn btn-info" onclick="add()" href="javascript:void(0)">Yeni Jeneratör Ekle</a>
                                @endif
                            </div>
                        </div>
                        <table id="ajax-crud-dt-jenerator"
                            class="table table-bordered dt-responsive nowrap table-striped align-middle"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bulunduğu Yer</th>
                                    <th>Marka</th>
                                    <th>TCK</th>
                                    <th>KVa</th>
                                    <th>Çalışma Saati</th>
                                    <th>Hacim(L)</th>
                                    <th>Yakıt Seviyesi(cm)</th>
                                    <th>Bakım Tarihi</th>
                                    <th>Akü Tarihi</th>
                                    <th>Kontrol Tarihi</th>
                                    <th>Durum</th>
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

<div id="jenerator-modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-info p-3">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="close-modal"></button>
            </div>
            <form method="POST" action="javascript:void(0)" name="JeneratorForm" id="JeneratorForm"
                enctype="multipart/form-data">
                <input type="hidden" name="jenerator_id" id="jenerator_id">
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Genel Jeneratör Bilgileri --}}
                        <div class="col-lg-3">
                            <label for="jenerator_bina" class="form-label">Konum (Bina)</label>
                            <select id="jenerator_bina" name="jenerator_bina" class="form-select">
                                <option value="">Seçim Yapınız</option>
                                @foreach ($bina as $value)
                                    <option value="{{ $value->bina_id }}">{{ $value->bina_adi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="jenerator_ad" class="form-label">Ad</label>
                            <input type="text" id="jenerator_ad" class="form-control" name="jenerator_ad" autocomplete="off" />
                        </div>
                        <div class="col-lg-3">
                            <label for="jenerator_marka" class="form-label">Marka</label>
                            <input type="text" id="jenerator_marka" class="form-control" name="jenerator_marka" autocomplete="off" />
                        </div>
                        <div class="col-lg-3">
                            <label for="jenerator_model" class="form-label">Model</label>
                            <input type="text" id="jenerator_model" class="form-control" name="jenerator_model" autocomplete="off" />
                        </div>
                        <div class="col-lg-2">
                            <label for="jenerator_yil" class="form-label">Yıl</label>
                            <input type="number" id="jenerator_yil" class="form-control" name="jenerator_yil" autocomplete="off" />
                        </div>
                        <div class="col-lg-2">
                            <label for="jenerator_kva" class="form-label">KVa</label>
                            <input type="text" id="jenerator_kva" class="form-control" name="jenerator_kva" autocomplete="off" />
                        </div>
                        <div class="col-lg-2">
                            <label for="jenerator_tck" class="form-label">TCK</label>
                            <input type="text" id="jenerator_tck" class="form-control" name="jenerator_tck" autocomplete="off" />
                        </div>
                        <div class="col-lg-3">
                            <label for="jenerator_akutarihi" class="form-label">Akü Tarihi</label>
                            <input type="date" id="jenerator_akutarihi" class="form-control" name="jenerator_akutarihi" autocomplete="off" />
                        </div>
                        <div class="col-lg-3">
                            <label for="jenerator_bakimtarihi" class="form-label">Bakım Tarihi</label>
                            <input type="date" id="jenerator_bakimtarihi" class="form-control" name="jenerator_bakimtarihi" autocomplete="off" />
                        </div>

                        {{-- Yakıt ve Tank Bilgileri --}}
                        <hr class="my-3">
                        <div class="col-lg-3">
                            <label for="jenerator_tur" class="form-label">Tank Tipi</label>
                            <select class="form-select" id="jenerator_tur" name="jenerator_tur">
                                <option selected value="">Seçim Yapınız</option>
                                <option value="1">Yatay Silindir</option>
                                <option value="2">Dikey Silindir</option>
                                <option value="3">Dikdörtgen</option>
                            </select>
                        </div>
                         <div class="col-lg-3">
                            <label for="jenerator_yakitseviyesi" class="form-label">Yakıt Seviyesi (Cm)</label>
                            <input type="number" step="any" min="0" id="jenerator_yakitseviyesi" class="form-control" name="jenerator_yakitseviyesi" autocomplete="off" />
                        </div>
                        <div class="col-12 row g-3 silindir-alanlar" style="display:none;">
                            <div class="col-lg-4">
                                <label for="jenerator_cap" class="form-label">Tank Çapı (Cm)</label>
                                <input type="number" id="jenerator_cap" class="form-control" name="jenerator_cap" 
                                       step="any" min="0" placeholder="Örn: 140" autocomplete="off" />
                            </div>
                            <div class="col-lg-4">
                                <label for="jenerator_uzunluk" class="form-label" id="label-uzunluk-yukseklik">Tank Uzunluk (Cm)</label>
                                <input type="number" id="jenerator_uzunluk" class="form-control" name="jenerator_uzunluk" 
                                       step="any" min="0" placeholder="Örn: 334" autocomplete="off" />
                            </div>
                        </div>
                        
                        <div class="col-12 row g-3 dikdortgen-alanlar" style="display:none;">
                            <div class="col-lg-3">
                                <label for="jenerator_en" class="form-label">Tank En (Cm)</label>
                                <input type="number" id="jenerator_en" class="form-control" name="jenerator_en" 
                                       step="any" min="0" placeholder="Santimetre" autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <label for="jenerator_boy" class="form-label">Tank Boy (Cm)</label>
                                <input type="number" id="jenerator_boy" class="form-control" name="jenerator_boy" 
                                       step="any" min="0" placeholder="Santimetre" autocomplete="off" />
                            </div>
                            <div class="col-lg-3">
                                <label for="jenerator_yukseklik" class="form-label">Tank Yükseklik (Cm)</label>
                                <input type="number" id="jenerator_yukseklik" class="form-control" name="jenerator_yukseklik" 
                                       step="any" min="0" placeholder="Santimetre" autocomplete="off" />
                            </div>
                        </div>
                         <hr class="my-3">

                        {{-- Diğer Bilgiler --}}
                        <div class="col-lg-3">
                            <label for="jenerator_durum" class="form-label">Durum</label>
                            <select class="form-select" id="jenerator_durum" name="jenerator_durum">
                                <option selected value="">Seçim Yapınız</option>
                                <option value="1">Aktif</option>
                                <option value="0">Pasif</option>
                            </select>
                        </div>
                         <div class="col-lg-9">
                            <label for="jenerator_aciklama" class="form-label">Açıklama</label>
                            <input type="text" id="jenerator_aciklama" class="form-control" name="jenerator_aciklama" autocomplete="off" />
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
    // DOM elementlerini global scope'a yakın bir yerde tanımlayalım
    let tankTipiSelect, silindirAlanlar, dikdortgenAlanlar, uzunlukYukseklikLabel;

    /**
     * Tank tipine göre ilgili form alanlarını gösterir/gizler.
     */
    function toggleTankAlanlari() {
        if (!tankTipiSelect) return; // Element henüz yüklenmediyse işlemi durdur

        const secilenDeger = tankTipiSelect.value;

        // Önce tüm özel alan gruplarını gizle
        silindirAlanlar.style.display = 'none';
        dikdortgenAlanlar.style.display = 'none';
        
        if (secilenDeger === '1') { // Yatay Silindir
            silindirAlanlar.style.display = 'flex';
            uzunlukYukseklikLabel.textContent = 'Tank Uzunluk (Cm)';
        } else if (secilenDeger === '2') { // Dikey Silindir
            silindirAlanlar.style.display = 'flex';
            uzunlukYukseklikLabel.textContent = 'Tank Yükseklik (Cm)';
        } else if (secilenDeger === '3') { // Dikdörtgen
            dikdortgenAlanlar.style.display = 'flex';
        }
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#ajax-crud-dt-jenerator').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('jenerator.listesi') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenerator_bina', name: 'jenerator_bina' },
                { data: 'jenerator_marka', name: 'jenerator_marka' },
                { data: 'jenerator_tck', name: 'jenerator_tck' },
                { data: 'jenerator_kva', name: 'jenerator_kva' },
                { data: 'calisma_saati', name: 'calisma_saati' },
                { data: 'jenerator_hacim', name: 'jenerator_hacim' },
                { data: 'jenerator_yakitseviyesi', name: 'jenerator_yakitseviyesi' },
                { data: 'jenerator_bakimtarihi', name: 'jenerator_bakimtarihi', searchable: false, render: data => renderTarih(data, null, { type: 'bakim' }) },
                { data: 'jenerator_akutarihi', name: 'jenerator_akutarihi', searchable: false, render: data => renderTarih(data, null, { type: 'aku' }) },
                { data: 'son_kontrol_tarihi', name: 'son_kontrol_tarihi', searchable: false, render: data => renderTarih(data, null, { type: 'kontrol' }) },
                { data: 'jenerator_durum', name: 'jenerator_durum', render: data => data == 1 ? '<span class="text-success">Aktif</span>' : '<span class="text-danger">Pasif</span>' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            language: { url: '{{ url('build/json/datatabletr.json') }}' },
            order: [[1, 'desc']],
            pageLength: -1,
            lengthMenu: [[-1, 10, 25], ["Tümü", 10, 25]],
            dom: 'Bfrtip',
                buttons: ['pageLength','excelHtml5','print'],
        });

        // DOM elementlerini `ready` fonksiyonu içinde, yüklendiklerinden emin olarak ata
        tankTipiSelect = document.getElementById('jenerator_tur');
        silindirAlanlar = document.querySelector('.silindir-alanlar');
        dikdortgenAlanlar = document.querySelector('.dikdortgen-alanlar');
        uzunlukYukseklikLabel = document.getElementById('label-uzunluk-yukseklik');
        
        // Event Listener'ı ata
        if(tankTipiSelect) {
            tankTipiSelect.addEventListener('change', toggleTankAlanlari);
        }
    });

    function add() {
        $('#JeneratorForm').trigger("reset");
        $('#exampleModalLabel').html("Yeni Jeneratör Ekle");
        $('#jenerator-modal').modal('show');
        $('#jenerator_id').val('');
        toggleTankAlanlari(); // Formu sıfırladıktan sonra alanların görünümünü de sıfırla
    }

    function editFunc(jenerator_id) {
        $.ajax({
            type: "POST",
            url: "{{ route('jenerator.edit') }}",
            data: { jenerator_id: jenerator_id },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#exampleModalLabel').html("Jeneratör Düzenle");
                    $('#jenerator-modal').modal('show');
                    
                    // Form alanlarını doldur
                    $('#jenerator_id').val(res.data.jenerator_id);
                    $('#jenerator_bina').val(res.data.jenerator_bina);
                    $('#jenerator_ad').val(res.data.jenerator_ad);
                    $('#jenerator_marka').val(res.data.jenerator_marka);
                    $('#jenerator_model').val(res.data.jenerator_model);
                    $('#jenerator_yil').val(res.data.jenerator_yil);
                    $('#jenerator_kva').val(res.data.jenerator_kva);
                    $('#jenerator_tck').val(res.data.jenerator_tck);
                    $('#jenerator_akutarihi').val(res.data.jenerator_akutarihi);
                    $('#jenerator_bakimtarihi').val(res.data.jenerator_bakimtarihi);
                    $('#jenerator_yakitseviyesi').val(res.data.jenerator_yakitseviyesi);
                    
                    // Tank Bilgileri
                    $('#jenerator_tur').val(res.data.jenerator_tur);
                    $('#jenerator_cap').val(res.data.jenerator_cap);
                    $('#jenerator_uzunluk').val(res.data.jenerator_uzunluk);
                    $('#jenerator_en').val(res.data.jenerator_en);
                    $('#jenerator_boy').val(res.data.jenerator_boy);
                    $('#jenerator_yukseklik').val(res.data.jenerator_yukseklik);

                    $('#jenerator_durum').val(res.data.jenerator_durum);
                    $('#jenerator_aciklama').val(res.data.jenerator_aciklama);

                    // *** ANA DÜZELTME: Form doldurulduktan sonra alanların görünürlüğünü ayarla ***
                    toggleTankAlanlari();
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

    function deleteFunc(jenerator_id) {
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
                    url: "{{ route('jenerator.delete') }}",
                    data: { jenerator_id: jenerator_id },
                    dataType: 'json',
                    success: function(res) {
                        $('#ajax-crud-dt-jenerator').DataTable().ajax.reload();
                        Swal.fire({
                            title: res.status === 'success' ? 'Başarılı!' : 'Hata!',
                            text: res.message,
                            icon: res.status,
                            confirmButtonText: 'Tamam'
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Hata!", xhr.responseJSON?.message || "Bir hata oluştu.", "error");
                    }
                });
            }
        });
    }
    
    $('#JeneratorForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('jenerator.store') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: () => $("#btn-save").html('Gönderiliyor...').prop("disabled", true),
            success: function(response) {
                $("#jenerator-modal").modal('hide');
                $('#ajax-crud-dt-jenerator').DataTable().ajax.reload(null, false);
                Swal.fire("Başarılı!", response.message, "success");
            },
            error: function(xhr) {
                Swal.fire("Hata!", xhr.responseJSON?.message || "Bir hata oluştu.", "error");
            },
            complete: () => $("#btn-save").html('Kaydet').prop("disabled", false)
        });
    });

    function renderTarih(data, type, options = {}) {
        if (!data) return '';
        let tarih = new Date(data);
        if (isNaN(tarih)) return ''; // Geçersiz tarih kontrolü
        
        let formatted = tarih.toLocaleDateString('tr-TR');
        let today = new Date();
        today.setHours(0,0,0,0); // Sadece gün bazlı karşılaştırma için
        let expiry = new Date(tarih);
        
        switch (options.type) {
            case 'bakim': expiry.setFullYear(expiry.getFullYear() + 1); break;
            case 'aku': expiry.setFullYear(expiry.getFullYear() + 2); break;
            case 'kontrol': expiry.setDate(expiry.getDate() + 7); break;
        }
        
        let diffDays = Math.floor((expiry - today) / (1000 * 60 * 60 * 24));
        let color = '#22c55e'; // Yeşil
        
        if (expiry < today) {
            color = '#ef4444'; // Kırmızı
        } else if ((options.type === 'kontrol' && diffDays <= 2) || ((options.type === 'bakim' || options.type === 'aku') && diffDays <= 30)) {
            color = '#ffa752'; // Turuncu
        }
        
        return `<span style="background-color:${color}; color:white; padding: 2px 6px; border-radius: 4px; font-weight:bold;">${formatted}</span>`;
    }
</script>
@endsection