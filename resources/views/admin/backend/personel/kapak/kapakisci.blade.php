
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .logo img {
            width: 100%;
            height: 100%;
            /*border-radius: 10%; /*Profil resmini yuvarlak yap */
        }

        .icon img {
            width: 100%;
            height: 100%;
        }
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .page {
            width: 22cm;
            height: 29.7cm;
            background-color: #fff; /* Sayfa arkaplan rengi */
            display: flex; /* Container'ları yatayda sıralamak için flex kullanıyoruz */
            justify-content: space-between; /* Container'ları sayfa genişliği içinde ortalar */
        }
        .container {
            width: 6cm;
            height: 20cm;
            margin: 0cm; /* Container'lar arasındaki boşluğu ayarlar */
            border: 3px solid #000;
        }
        .logo {
            height: 4cm;
            background-color: #fff; /* Beyaz arkaplan */
            text-align: center;
            line-height: 4cm;

        }
        .icon {
            background: url('{{ asset('backend/assets/images/users/kgm.jpg') }}') center center no-repeat;
            background-size: contain; /* ya da cover kullanabilirsiniz, ihtiyaca bağlı olarak değiştirilebilir */
            height: 3cm;
            text-align: center;
        }
        .company-name{
            font-weight: bold; /* Yazıların pontosunu kalın yap */
            font-size: 14pt; /* Yazıların puntosunu büyüt */
        }
        .department-name,
        .person-name{
            font-weight: bold; /* Yazıların pontosunu kalın yap */
            font-size: 18pt; /* Yazıların puntosunu büyüt */
        }
        .employee-id {
            font-weight: bold; /* Yazıların pontosunu kalın yap */
            font-size: 20pt; /* Yazıların puntosunu büyüt */
        }
        .amount {
            font-weight: bold; /* Yazıların pontosunu kalın yap */
            font-size: 24pt; /* Yazıların puntosunu büyüt */
        }
        .company-name {
            height: 2cm;
            background-color: #E85C0CFF; /* Kırmızı arkaplan */
            color: #ffffff; /* Beyaz yazı rengi */
            text-align: center;
            display: flex; /* Flex konteynerı olarak kullan */
            align-items: center; /* Yatayda ve dikeyde ortala */
            white-space: normal; /* Metni otomatik olarak satıra al */
            word-wrap: break-word; /* Kelime sınırlarında otomatik olarak alt satıra geçir */
        }
        .department-name {
            height: 2cm;
            text-align: center;
            line-height: 2cm;
        }
        .person-name,
        .employee-id {
            height: 5cm;
            background-color: #E85C0CFF; /* Kırmızı arkaplanı yeniden ekleyin */
            color: #ffffff; /* Beyaz yazı rengini ekleyin */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center; /* İçeriğin ortalanması için text-align ekleyin */
        }
        .amount {
            height: 1cm;
            text-align: center;
            line-height: 1cm;
        }
    </style>
</head>
<body>
    <div class="page">
        @php $sira = 1; $counter = 0; @endphp
        @foreach ($personel as $value)
            @if ($counter % 4 === 0 && $counter > 0)
                </div><div class="page">
            @endif
    
            <div class="container">
                <div class="logo">
                    <img src="{{ asset(
                            !empty($value->personel_resim) && file_exists(public_path($value->personel_resim))
                                ? $value->personel_resim
                                : 'backend/assets/images/users/kgm.jpg',
                        ) }}">
                </div>
    
                <div class="company-name">
                    {{ class_exists('Transliterator') ? Transliterator::create('tr-upper')->transliterate($ayar->ayar_author) : mb_convert_case($ayar->ayar_author, MB_CASE_UPPER, 'UTF-8') }}
                </div>
    
                <div class="icon"></div>
    
                <div class="person-name">
                    @php
                        $ad_soyad = class_exists('Transliterator') ? Transliterator::create('tr-upper')->transliterate($value->personel_adsoyad) : mb_convert_case($value->personel_adsoyad, MB_CASE_UPPER, 'UTF-8');
                        $ad_soyad_array = preg_split('/\s+/', $ad_soyad);
                    @endphp
    
                    @foreach ($ad_soyad_array as $isim)
                        {{ $isim }}<br>
                    @endforeach
                </div>
    
                <div class="employee-id">{{ $value->personel_sicilno }}</div>
                <div class="amount">{{ $sira++ }}</div>
            </div>
    
            @php $counter++; @endphp
        @endforeach
    </div>
    
</body>
</html>
