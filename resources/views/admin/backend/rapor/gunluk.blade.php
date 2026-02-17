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
                                <li class="breadcrumb-item"><a href="{{ route('pdks.bugun') }}">PDKS İşlemleri</a></li>
                                <li class="breadcrumb-item active">{{ $pagetitle }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Önizleme kutusu --}}
            <div class="row mb-3">
                <div class="col-lg-8">
                    <div class="card border-primary bg-primary-subtle">
                        <div class="card-body py-3">
                            <h6 class="card-title mb-2"><i class="mdi mdi-information-outline me-1"></i> Önizleme</h6>
                            <p class="mb-0 small" id="rapor-onizleme-metin">
                                Rapor <strong id="rapor-onizleme-tarih">{{ now()->format('d.m.Y') }}</strong> tarihi için hazırlanacak.
                                <strong id="rapor-onizleme-sayi">0</strong> alıcıya gönderilecek.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rapor Gönderme --}}
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-email-send-outline me-1"></i> Rapor Gönder
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Tarih seçip alıcıları belirledikten sonra raporu gönderebilirsiniz. Tarih boş bırakılırsa bugünün raporu gönderilir.
                            </p>
                            <form action="{{ route('rapor.gonder') }}" method="POST">
                                @csrf
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label for="rapor_tarih" class="form-label">Rapor Tarihi</label>
                                        <input type="date" class="form-control" id="rapor_tarih" name="date"
                                               value="{{ old('date', now()->format('Y-m-d')) }}"
                                               max="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>

                                <h6 class="mb-3">Alıcı E-posta Adresleri</h6>

                                <div class="mb-3 border-bottom pb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll" checked>
                                        <label class="form-check-label fw-semibold" for="selectAll">
                                            Tümünü Seç / Kaldır
                                        </label>
                                    </div>
                                </div>

                                @if(isset($mailler) && $mailler->count() > 0)
                                    @php $currentBirim = null; @endphp
                                    @foreach($mailler as $mail)
                                        @if($currentBirim !== $mail->birim_ad)
                                            @php $currentBirim = $mail->birim_ad; @endphp
                                            @if(!$loop->first)
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <span class="badge bg-primary-subtle text-primary mb-2">{{ $currentBirim }}</span>
                                        @endif
                                        <div class="form-check ms-3">
                                            <input class="form-check-input mail-checkbox" type="checkbox"
                                                   name="emails[]" value="{{ $mail->email }}"
                                                   id="mail_{{ $mail->id }}" checked>
                                            <label class="form-check-label" for="mail_{{ $mail->id }}">
                                                {{ $mail->email }}
                                            </label>
                                        </div>
                                        @if($loop->last)
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="alert alert-warning mb-3">
                                        Aktif rapor mail adresi bulunamadı.
                                    </div>
                                @endif

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary" id="btnGonder">
                                        <i class="mdi mdi-email-send-outline me-1"></i> Raporu Gönder (Tümü)
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mail Listesi Yönetimi --}}
            <div class="row">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-email-edit-outline me-1"></i> Mail Listesi Yönetimi
                            </h5>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#mailEkleModal">
                                <i class="mdi mdi-plus me-1"></i> Yeni Mail Ekle
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>E-posta Adresi</th>
                                            <th>Birim</th>
                                            <th style="width: 160px;">İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mailler as $key => $mail)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $mail->email }}</td>
                                                <td><span class="badge bg-primary-subtle text-primary">{{ $mail->birim_ad }}</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info"
                                                            onclick="mailDuzenle({{ $mail->id }}, '{{ $mail->email }}', {{ $mail->birim }})">
                                                        <i class="mdi mdi-pencil"></i> Düzenle
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                            onclick="mailSil({{ $mail->id }}, '{{ $mail->email }}')">
                                                        <i class="mdi mdi-delete"></i> Sil
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Kayıt bulunamadı.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Yeni Mail Ekleme Modal --}}
    <div class="modal fade" id="mailEkleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('rapor.mail.ekle') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="mdi mdi-email-plus-outline me-1"></i> Yeni Mail Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">E-posta Adresi</label>
                            <input type="email" class="form-control" name="email" required placeholder="ornek@mail.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Birim</label>
                            <select name="birim" class="form-select" required>
                                <option value="">Seçiniz</option>
                                @foreach($birimler as $birim)
                                    <option value="{{ $birim->birim_id }}">{{ $birim->birim_ad }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn btn-success">Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Mail Düzenleme Modal --}}
    <div class="modal fade" id="mailDuzenleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('rapor.mail.guncelle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mail_id" id="duzenle_mail_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="mdi mdi-email-edit-outline me-1"></i> Mail Düzenle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">E-posta Adresi</label>
                            <input type="email" class="form-control" name="email" id="duzenle_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Birim</label>
                            <select name="birim" id="duzenle_birim" class="form-select" required>
                                <option value="">Seçiniz</option>
                                @foreach($birimler as $birim)
                                    <option value="{{ $birim->birim_id }}">{{ $birim->birim_ad }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Mail Silme Modal --}}
    <div class="modal fade" id="mailSilModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('rapor.mail.sil') }}" method="POST">
                    @csrf
                    <input type="hidden" name="mail_id" id="sil_mail_id">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="mdi mdi-delete-outline me-1"></i> Mail Sil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Aşağıdaki mail adresini silmek istediğinize emin misiniz?</p>
                        <p class="fw-semibold text-danger" id="sil_email_goster"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn btn-danger">Sil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.mail-checkbox');
            const btnGonder = document.getElementById('btnGonder');
            const raporTarih = document.getElementById('rapor_tarih');
            const onizlemeTarih = document.getElementById('rapor-onizleme-tarih');
            const onizlemeSayi = document.getElementById('rapor-onizleme-sayi');

            function formatTarih(str) {
                if (!str) return 'bugün';
                const d = new Date(str);
                return d.toLocaleDateString('tr-TR');
            }

            function updateOnizleme() {
                const tarih = raporTarih ? raporTarih.value : '';
                const count = [...checkboxes].filter(c => c.checked).length;
                if (onizlemeTarih) onizlemeTarih.textContent = tarih ? formatTarih(tarih) : 'bugün';
                if (onizlemeSayi) onizlemeSayi.textContent = count;
            }

            if (raporTarih) {
                raporTarih.addEventListener('change', updateOnizleme);
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateButton();
                updateOnizleme();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = [...checkboxes].every(c => c.checked);
                    const noneChecked = [...checkboxes].every(c => !c.checked);
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = !allChecked && !noneChecked;
                    updateButton();
                    updateOnizleme();
                });
            });

            function updateButton() {
                const count = [...checkboxes].filter(c => c.checked).length;
                btnGonder.disabled = count === 0;
                if (count === 0) {
                    btnGonder.innerHTML = '<i class="mdi mdi-email-send-outline me-1"></i> Mail seçiniz';
                } else if (count === checkboxes.length) {
                    btnGonder.innerHTML = '<i class="mdi mdi-email-send-outline me-1"></i> Raporu Gönder (Tümü)';
                } else {
                    btnGonder.innerHTML = '<i class="mdi mdi-email-send-outline me-1"></i> Raporu Gönder (' + count + ' mail)';
                }
            }

            updateOnizleme();
        });

        function mailDuzenle(id, email, birimId) {
            document.getElementById('duzenle_mail_id').value = id;
            document.getElementById('duzenle_email').value = email;
            document.getElementById('duzenle_birim').value = birimId;
            new bootstrap.Modal(document.getElementById('mailDuzenleModal')).show();
        }

        function mailSil(id, email) {
            document.getElementById('sil_mail_id').value = id;
            document.getElementById('sil_email_goster').textContent = email;
            new bootstrap.Modal(document.getElementById('mailSilModal')).show();
        }
    </script>
@endsection
