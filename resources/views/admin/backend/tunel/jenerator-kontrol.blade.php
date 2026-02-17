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
                                <li class="breadcrumb-item"><a href="{{route('jenerator.listesi')}}">Jeneratörler</a></li>
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
                            <h4>{{ $jenerator->jenerator_marka }} / {{ $jenerator->jenerator_ad }} / {{$jenerator->jenerator_tck}} Haftalık Kontrol Formu</h4>
                        </div>
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="row g-4 mb-3">
                                <div class="col-sm-auto">
                                    @if (Auth::guard('web')->user()->can('jeneratorkontrol.ekle'))
                                        <a class="btn btn-success" onclick="addKontrol()" href="javascript:void(0)">Yeni Kontrol Ekle</a>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="ajax-crud-dt-kontrol" class="table table-bordered table-striped align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tarih</th>
                                            <th>Yakıt Seviyesi (Bakla)</th>
                                            <th>Eklenen Yakıt(Lt)</th>
                                            <th>Çalışma Saati</th>
                                            <th>Frekans (Hz)</th>
                                            <th>Durum</th>
                                            <th>Açıklama</th>
                                            <th>İşlem</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- MODAL: Haftalık Kontrol Ekle --}}
<div class="modal fade" id="kontrolModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <form id="kontrolForm">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Yeni Haftalık Kontrol</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
          </div>
  
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Kontrol Tarihi<span class="text-danger">*</span></label>
                <input type="date" name="kontrol_tarihi" id="kontrol_tarihi" class="form-control" required>
              </div>
  
              <div class="col-md-3">
                <label class="form-label">Yakıt Seviyesi (Bakla)</label>
                <input type="number" name="yakit_seviyesi" class="form-control" >
              </div>
  
              <div class="col-md-3">
                <label class="form-label">Eklenen Yakıt Miktarı (L)</label>
                <input type="number" name="yakit_miktari" class="form-control" min="0">
              </div>
  
              <div class="col-md-3">
                <label class="form-label">Çalışma Saati</label>
                <input type="number" name="calisma_saati" class="form-control" min="0">
              </div>
  
              <div class="col-md-3">
                <label class="form-label">Yağ Seviyesi (%)</label>
                <input type="number" name="yag_seviyesi" class="form-control" min="0" >
              </div>
  
              <div class="col-md-3">
                <label class="form-label">Yağ Miktarı (L)</label>
                <input type="number" name="yag_miktari" class="form-control" min="0">
              </div>
  
              <div class="col-md-3">
                <label class="form-label">Frekans (Hz)</label>
                <input type="number" step="0.01" name="frekans" value="50" class="form-control" min="0" max="200">
              </div>
  
              <div class="col-md-3">
                <label class="form-label">Genel Durum</label>
                <select name="durum" class="form-select">
                  <option value="1" selected>Uygun</option>
                  <option value="0">Sorunlu</option>
                </select>
              </div>
            </div>
  
            <hr>
  
            <div class="row g-3">
              @php
                $checks = [
                  'sarj_redresoru' => 'Şarj Redresörü',
                  'aku_durumu'     => 'Akü Durumu',
                  'su_durumu'      => 'Su Durumu',
                  'temizlik'       => 'Genel Temizlik',
                  'pano_temizlik'  => 'Pano Temizliği',
                  'sizinti_kacak'  => 'Sızıntı/Kaçak',
                  'radyator'       => 'Radyatör',
                  'isitici'        => 'Isıtıcı',
                  'lamba'          => 'Lamba',
                  'egzoz'          => 'Egzoz',
                  'hava_filtresi'  => 'Hava Filtresi',
                  'scada_kontrolu' => 'SCADA Kontrolü',
                ];
              @endphp
  
              @foreach ($checks as $name => $label)
                <div class="col-md-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="{{ $name }}" name="{{ $name }}" checked>
                    <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                  </div>
                </div>
              @endforeach
            </div>
  
            <hr>
  
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea name="aciklama" class="form-control" rows="3" placeholder="Varsa notunuzu yazın..."></textarea>
              </div>
            </div>
          </div>
  
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
            <button type="submit" id="btnKaydet" class="btn btn-success">Kaydet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
    <script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#ajax-crud-dt-kontrol').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('jenerator.haftalik.kontrol', $jenerator->jenerator_id) }}",
        columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
        {
          data: 'kontrol_tarihi',
          name: 'kontrol_tarihi',
          searchable: false,
          render: function (data) {
              return data ? new Date(data).toLocaleDateString('tr-TR').replace(/\./g, '-') : '';
          }
        },
        { data: 'yakit_seviyesi', name: 'yakit_seviyesi' },
        { data: 'yakit_miktari', name: 'yakit_miktari' },
        { data: 'calisma_saati', name: 'calisma_saati' },
        { data: 'frekans', name: 'frekans' },
        { 
          data: 'durum', name: 'durum',
          render: function(data) {
            return data == 1
              ? '<span class="badge bg-success">Uygun</span>'
              : '<span class="badge bg-danger">Sorunlu</span>';
          }
        },
        { data: 'aciklama', name: 'aciklama' },
        { data: 'action', name: 'action', orderable:false, searchable:false, defaultContent: '-' },
        ],
        language: { url: '{{ url('build/json/datatabletr.json') }}' },
        dom: 'Bfrtip',
                buttons: ['pageLength','excelHtml5','print'],
        order: [[1, 'desc']]
        });

        });
// Form submit
    $('#kontrolForm').on('submit', function(e){
        e.preventDefault();

        var $btn = $('#btnKaydet');
        $btn.prop('disabled', true).text('Gönderiliyor...');

        $.post("{{ route('jenerator.kontrol.store', $jenerator->jenerator_id) }}", $(this).serialize())
            .done(function(resp){
                Swal.fire('Başarılı', resp.success ?? 'Kayıt eklendi', 'success');
                $('#kontrolModal').modal('hide');
                $('#ajax-crud-dt-kontrol').DataTable().ajax.reload(null, false);
                $('#kontrolForm')[0].reset();
                // Varsayılan olarak switchleri tekrar checked yap
                $('#kontrolForm input[type=checkbox]').prop('checked', true);
                setToday();
            })
            .fail(function(xhr){
                let msg = 'Bir hata oluştu.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errs = xhr.responseJSON.errors;
                    msg = Object.values(errs).map(arr => arr.join('<br>')).join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Hata', msg, 'error');
            })
            .always(function(){
                $btn.prop('disabled', false).text('Kaydet');
            });
    });

// Bugünün tarihini inputa yaz
    function setToday(){
        const today = new Date().toISOString().slice(0,10);
        document.getElementById('kontrol_tarihi').value = today;
    }
    function addKontrol(){
        // Formu temizle + defaultlar
        $('#kontrolForm')[0].reset();
        $('#kontrolForm input[type=checkbox]').prop('checked', true);
        setToday();
        const modalEl = document.getElementById('kontrolModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

 </script>
    
@endsection
