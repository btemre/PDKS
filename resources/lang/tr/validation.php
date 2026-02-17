<?php

return [
    /*
    |---------------------------------------------------------------
    | Doğrulama Mesajları
    |---------------------------------------------------------------
    |
    | Aşağıdaki dil satırları, doğrulama sırasında oluşan hata mesajlarını
    | içerir. Her bir doğrulama kuralı için özel bir hata mesajı tanımlayabilirsiniz.
    |
    */
    'accepted' => ':attribute kabul edilmelidir.',
    'active_url' => ':attribute geçerli bir URL değil.',
    'after' => ':attribute, :date tarihinden sonra bir tarih olmalıdır.',
    'after_or_equal' => ':attribute, :date tarihinden sonra veya aynı tarih olmalıdır.',
    'alpha' => ':attribute yalnızca harflerden oluşmalıdır.',
    'alpha_dash' => ':attribute yalnızca harfler, rakamlar, tire ve alt çizgi içermelidir.',
    'alpha_num' => ':attribute yalnızca harfler ve rakamlar içermelidir.',
    'array' => ':attribute bir dizi olmalıdır.',
    'before' => ':attribute, :date tarihinden önce bir tarih olmalıdır.',
    'before_or_equal' => ':attribute, :date tarihinden önce veya aynı tarih olmalıdır.',
    'between' => [
        'numeric' => ':attribute, :min ile :max arasında bir sayı olmalıdır.',
        'file' => ':attribute, :min ile :max kilobayt arasında olmalıdır.',
        'string' => ':attribute, :min ile :max karakter arasında olmalıdır.',
        'array' => ':attribute, :min ile :max öğe arasında olmalıdır.',
    ],
    'boolean' => ':attribute yalnızca doğru veya yanlış olabilir.',
    'confirmed' => ':attribute ile doğrulaması eşleşmiyor.',
    'date' => ':attribute geçerli bir tarih değil.',
    'date_equals' => ':attribute, :date tarihine eşit olmalıdır.',
    'date_format' => ':attribute, :format biçimine uymuyor.',
    'different' => ':attribute ve :other farklı olmalıdır.',
    'digits' => ':attribute, :digits basamaklı olmalıdır.',
    'digits_between' => ':attribute, :min ve :max basamak arasında olmalıdır.',
    'dimensions' => ':attribute geçersiz boyutlara sahip.',
    'distinct' => ':attribute alanında aynı değer birden fazla kez kullanılmış.',
    'email' => ':attribute hatalıdır, lütfen geçerli bir e-posta adresi girin.',
    'ends_with' => ':attribute şu değerlerden biriyle bitmelidir: :values',
    'exists' => 'Seçilen :attribute geçersiz.',
    'file' => ':attribute bir dosya olmalıdır.',
    'filled' => ':attribute alanı dolu olmalıdır.',
    'gt' => [
        'numeric' => ':attribute, :value değerinden büyük olmalıdır.',
        'file' => ':attribute, :value kilobayttan büyük olmalıdır.',
        'string' => ':attribute, :value karakterden uzun olmalıdır.',
        'array' => ':attribute, :value öğeden fazla olmalıdır.',
    ],
    'gte' => [
        'numeric' => ':attribute, :value değerine eşit veya büyük olmalıdır.',
        'file' => ':attribute, :value kilobayta eşit veya büyük olmalıdır.',
        'string' => ':attribute, :value karaktere eşit veya uzun olmalıdır.',
        'array' => ':attribute, :value öğeye eşit veya fazla olmalıdır.',
    ],
    'image' => ':attribute bir resim dosyası olmalıdır.',
    'in' => 'Seçilen :attribute geçersiz.',
    'in_array' => ':attribute, :other içinde bulunmalıdır.',
    'integer' => ':attribute bir tam sayı olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':attribute geçerli bir JSON dizisi olmalıdır.',
    'lt' => [
        'numeric' => ':attribute, :value değerinden küçük olmalıdır.',
        'file' => ':attribute, :value kilobayttan küçük olmalıdır.',
        'string' => ':attribute, :value karakterden kısa olmalıdır.',
        'array' => ':attribute, :value öğeden az olmalıdır.',
    ],
    'lte' => [
        'numeric' => ':attribute, :value değerine eşit veya küçük olmalıdır.',
        'file' => ':attribute, :value kilobayta eşit veya küçük olmalıdır.',
        'string' => ':attribute, :value karaktere eşit veya kısa olmalıdır.',
        'array' => ':attribute, :value öğeye eşit veya az olmalıdır.',
    ],
    'max' => [
        'numeric' => ':attribute, :max değerinden büyük olmamalıdır.',
        'file' => ':attribute, :max kilobayttan büyük olmamalıdır.',
        'string' => ':attribute, :max karakterden uzun olmamalıdır.',
        'array' => ':attribute, :max öğeden fazla olmamalıdır.',
    ],
    'mimes' => ':attribute, şu türde bir dosya olmalıdır: :values.',
    'mimetypes' => ':attribute, şu türlerden biri olmalıdır: :values.',
    'min' => [
        'numeric' => ':attribute en az :min olmalıdır.',
        'file' => ':attribute en az :min kilobayt olmalıdır.',
        'string' => ':attribute en az :min karakter olmalıdır.',
        'array' => ':attribute en az :min öğe içermelidir.',
    ],
    'not_in' => 'Seçilen :attribute geçersiz.',
    'not_regex' => ':attribute biçimi geçersiz.',
    'numeric' => ':attribute bir sayı olmalıdır.',
    'present' => ':attribute alanı mevcut olmalıdır.',
    'regex' => ':attribute biçimi geçersiz.',
    'required' => ':attribute alanı zorunludur.',
    'required_if' => ':other :value olduğunda :attribute alanı zorunludur.',
    'required_unless' => ':other :values içinde olmadıkça :attribute alanı zorunludur.',
    'required_with' => ':values mevcut olduğunda :attribute alanı zorunludur.',
    'required_with_all' => ':values mevcut olduğunda :attribute alanı zorunludur.',
    'required_without' => ':values mevcut olmadığında :attribute alanı zorunludur.',
    'required_without_all' => ':values hiçbiri mevcut olmadığında :attribute alanı zorunludur.',
    'same' => ':attribute ve :other eşleşmelidir.',
    'size' => [
        'numeric' => ':attribute, :size olmalıdır.',
        'file' => ':attribute, :size kilobayt olmalıdır.',
        'string' => ':attribute, :size karakter olmalıdır.',
        'array' => ':attribute, :size öğe içermelidir.',
    ],
    'starts_with' => ':attribute şu değerlerden biriyle başlamalıdır: :values',
    'string' => ':attribute bir metin olmalıdır.',
    'timezone' => ':attribute geçerli bir zaman dilimi olmalıdır.',
    'unique' => ':attribute zaten alınmış.',
    'uploaded' => ':attribute yüklenemedi.',
    'url' => ':attribute geçerli bir URL olmalıdır.',
    'uuid' => ':attribute geçerli bir UUID olmalıdır.',

    /*
    |---------------------------------------------------------------
    | Özel Doğrulama Mesajları
    |---------------------------------------------------------------
    |
    | Aşağıda, belirli alanlar için özel hata mesajları tanımlıyoruz.
    | Örneğin, "email" ve "password" için daha kullanıcı dostu mesajlar.
    |
    */
    'custom' => [
        'email' => [
            'required' => 'E-posta alanı boş bırakılamaz.',
            'email' => 'E-posta hatalıdır, lütfen geçerli bir e-posta adresi girin.',
            'failed' => 'Girdiğiniz bilgiler kayıtlarımızla eşleşmiyor.',
        ],
        'password' => [
            'required' => 'Şifre alanı boş bırakılamaz.',
            'min' => 'Şifre hatalıdır, en az :min karakter olmalıdır.',
            'confirmed' => 'Şifre doğrulaması eşleşmedi, lütfen tekrar kontrol edin.',
        ],
        // Kimlik doğrulama hatası için özel bir mesaj
        'credentials' => [
            'failed' => 'Girdiğiniz bilgiler kayıtlarımızla eşleşmiyor.',
        ],
    ],

    /*
    |---------------------------------------------------------------
    | Özel Alan İsimleri
    |---------------------------------------------------------------
    |
    | Aşağıdaki dil satırları, "email" gibi alan isimlerini daha anlaşılır
    | hale getirmek için kullanılır. Örneğin, "email" yerine "E-posta".
    |
    */
    'attributes' => [
        'name' => 'İsim',
        'username' => 'Kullanıcı Adı',
        'email' => 'E-posta',
        'password' => 'Şifre',
        'password_confirmation' => 'Şifre Doğrulama',
        'city' => 'Şehir',
        'country' => 'Ülke',
        'address' => 'Adres',
        'phone' => 'Telefon',
        'age' => 'Yaş',
        'gender' => 'Cinsiyet',
    ],
];