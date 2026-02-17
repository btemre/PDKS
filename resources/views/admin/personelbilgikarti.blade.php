
<div class="row">
    @foreach ($Personel as $value)
        <div class="col-xxl-3">
            <div class="card position-relative" id="contact-view-detail">
                {{-- İzinli Personel için --}}
                @if(isset($izinli_personel[$value->personel_adsoyad]))
                <div class="ribbon-wrapper">
                    <div class="ribbon" title="{{ $izinli_personel[$value->personel_adsoyad] }}">
                        İzinli
                    </div>
                </div>
            @endif
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset(
                            !empty($value->personel_resim) && file_exists(public_path($value->personel_resim))
                                ? $value->personel_resim
                                : 'backend/assets/images/users/kgm.jpg',
                        ) }}"
                            alt="Personel Resmi" class="avatar-lg rounded-circle img-thumbnail shadow">

                        <span class="contact-active position-absolute rounded-circle bg-success">
                            <span class="visually-hidden"></span>
                        </span>
                    </div>

                    <h5 class="mt-4 mb-1">
                        <a href="{{ route('personel.bilgidetay', $value->personel_id) }}">
                            {{ $value->personel_adsoyad }}
                        </a>
                    </h5>
                    
                    <p class="text-muted">{{ $value->personel_kan }} - {{ $value->personel_derece }}/{{ $value->personel_kademe }}</p>

                    <ul class="list-inline mb-0">
                        <li class="list-inline-item avatar-xs">
                            <a href="javascript:void(0);" class="avatar-title bg-success-subtle text-success fs-15 rounded" title="0{{ $value->personel_telefon }}">
                                <i class="ri-phone-line"></i>
                            </a>
                        </li>
                        <li class="list-inline-item avatar-xs">
                            <a href="javascript:void(0);" class="avatar-title bg-danger-subtle text-danger fs-15 rounded" title="{{ $value->personel_eposta }}">
                                <i class="ri-mail-line"></i>
                            </a>
                        </li>
                        <li class="list-inline-item avatar-xs">
                            <a href="javascript:void(0);" class="avatar-title bg-warning-subtle text-warning fs-15 rounded" title="{{ $value->personel_adres }}">
                                <i class="ri-question-answer-line"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-semibold mb-3 text-center">{{ $value->unvan_ad }}</h6>
                    <div class="table-responsive table-card">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-medium" scope="row">Sicil No:</td>
                                    <td>{{ $value->personel_sicilno }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">Statü:</td>
                                    <td>{{ $value->durum_ad }} / {{ $value->gorev_ad }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">İşe Giriş Tarihi:</td>
                                    <td>{{ tarih($value->personel_isegiristarih) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">Eğitim:</td>
                                    <td>{{ $value->ogrenim_tur }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">Çalışma Süresi:</td>
                                    <td>{{ $value->calisma_suresi }}<small class="text-muted"></small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
.ribbon-wrapper {
    width: 75px;
    height: 75px;
    overflow: hidden;
    position: absolute;
    top: -5px;
    right: -5px;
}

.ribbon {
    font-size: 0.7rem;
    color: white;
    text-align: center;
    transform: rotate(45deg);
    width: 100px;
    background-color: #ff4d4f;
    position: absolute;
    top: 10px;
    right: -25px;
    padding: 5px 0;
    cursor: pointer;
}

.ribbon:hover {
    background-color: #ff7875;
}
</style>
