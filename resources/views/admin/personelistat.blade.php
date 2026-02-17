<div class="row">
    {{-- Toplam Personel --}}
    <div class="col-md-2">
        <a href="{{ route('personel.detay') }}" class="text-decoration-none">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="fw-bold text-uppercase text-muted mb-0">Toplam Personel</p>
                    <h2 class="mt-4 ff-secondary fw-semibold">
                        <span class="counter-value" data-target="{{ $toplamPersonel }}"></span>
                    </h2>
                    <p class="mb-0 text-muted">
                        <span class="badge bg-light text-success mb-0">Güncel sayı</span>
                    </p>
                </div>
            </div>
        </a>
    </div>

    {{-- Ünvanlara göre --}}
    @foreach($unvanlar as $unvan)
        <div class="col-md-2">
            <a href="{{ route('personel.detay', ['unvan_id' => $unvan->gorev_id]) }}" class="text-decoration-none">
                <div class="card card-animate">
                    <div class="card-body">
                        <p class="fw-bold text-uppercase text-muted mb-0">{{ $unvan->gorev_ad }}</p>
                        <h2 class="mt-4 ff-secondary fw-semibold">
                            <span class="counter-value" data-target="{{ $unvan->count }}"></span>
                        </h2>
                        <p class="mb-0 text-muted">
                            <span class="badge bg-light text-success mb-0">Güncel sayı</span>
                        </p>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
