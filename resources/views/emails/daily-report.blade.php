<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>{{ $title }}</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style type="text/css">
        /* Reset & base */
        body, table, td, p, a, li { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; margin: 0; padding: 0; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }
        .ExternalClass, .ReadMsgBody { width: 100%; }
        .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td { line-height: 100%; }

        /* Mobile-first: default = small screen */
        .wrapper { width: 100%; max-width: 600px; margin: 0 auto; background: #f7f8fa; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; }
        .section { padding: 20px 16px; }
        .section-mobile { padding: 16px 12px; }
        .card { background: #fff; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
        .stat-card { display: block; width: 100%; margin-bottom: 10px; border-radius: 10px; padding: 16px; box-sizing: border-box; }
        .stat-num { font-size: 28px; font-weight: 700; line-height: 1.2; }
        .stat-label { font-size: 13px; font-weight: 600; line-height: 1.3; margin-top: 4px; }
        .row-card { display: block; padding: 14px 16px; border-bottom: 1px solid #eef0f2; }
        .row-card:last-child { border-bottom: 0; }
        .row-card:nth-child(even) { background: #fafbfc; }
        .row-name { font-size: 15px; font-weight: 600; color: #1a1d21; margin-bottom: 4px; }
        .row-meta { font-size: 13px; color: #5c6370; }
        .row-time { font-size: 13px; font-weight: 600; color: #374151; margin-top: 4px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
        .h1 { font-size: 22px; font-weight: 700; line-height: 1.3; margin: 0; }
        .h2 { font-size: 18px; font-weight: 700; line-height: 1.3; margin: 0 0 12px 0; }
        .empty-state { padding: 32px 20px; text-align: center; color: #6b7280; font-size: 15px; }

        @media only screen and (min-width: 480px) {
            .section { padding: 24px 20px; }
            .h1 { font-size: 26px; }
            .h2 { font-size: 20px; }
            .stat-num { font-size: 32px; }
            .stat-label { font-size: 14px; }
        }

        @media only screen and (min-width: 600px) {
            .stat-row { display: table !important; width: 100%; table-layout: fixed; }
            .stat-cell { display: table-cell !important; width: 50% !important; padding: 8px !important; vertical-align: top !important; }
            .detail-table { display: table !important; width: 100% !important; }
            .detail-table tr { display: table-row !important; }
            .detail-table td { display: table-cell !important; padding: 12px 14px !important; }
        }

        /* Desktop fallback for clients that don't support media */
        @media only screen and (max-width: 599px) {
            .mobile-stack { display: block !important; width: 100% !important; }
            .mobile-full { width: 100% !important; max-width: 100% !important; }
            .stat-row { display: block !important; }
            .stat-cell { display: block !important; width: 100% !important; padding: 6px 0 !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#f7f8fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 15px; line-height: 1.5; color: #374151;">
    <div class="wrapper">
        <table class="container" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto;">
            <!-- Header: premium, mobile-friendly -->
            <tr>
                <td style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; padding: 28px 20px; text-align: center;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td>
                                <h1 class="h1" style="color: #fff; margin-bottom: 6px;">PDKS Günlük Rapor</h1>
                                <p style="margin: 0; font-size: 14px; opacity: 0.95; color: #cbd5e1;">{{ $title }}</p>
                                <p style="margin: 10px 0 0 0; font-size: 12px; opacity: 0.8; color: #94a3b8;">Rapor oluşturulma: {{ now()->format('d.m.Y H:i') }} · Mobil uyumlu</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Özet kartları: mobilde tek sütun, masaüstünde 2 sütun -->
            @php
                $sectionKeys = !empty($sectionOrder) ? $sectionOrder : array_keys($records);
                $statsConfig = [
                    'Giriş-Çıkış Kayıtları'   => ['color' => '#059669', 'bg' => '#ecfdf5', 'icon' => '✓'],
                    'Gelmeyen Personeller'     => ['color' => '#dc2626', 'bg' => '#fef2f2', 'icon' => '○'],
                    'Geç Gelen Personeller'   => ['color' => '#d97706', 'bg' => '#fffbeb', 'icon' => '⏱'],
                    'Erken Çıkan Personeller' => ['color' => '#0284c7', 'bg' => '#f0f9ff', 'icon' => '→'],
                    'Çıkış İşlemi Yapmayan Personeller' => ['color' => '#c2410c', 'bg' => '#fff7ed', 'icon' => '⊘'],
                    'Kart Tanımsız Personeller' => ['color' => '#64748b', 'bg' => '#f8fafc', 'icon' => '?'],
                ];
                /* Durum bazlı tasarım: GEÇ GELDİ, ERKEN ÇIKTI, İZİNLİ vb. farklı renk/stil */
                $durumStyles = [
                    'NORMAL'                    => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '1px solid #10b981', 'icon' => '✓'],
                    'GEÇ GELDİ'                 => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '1px solid #f59e0b', 'icon' => '⏱'],
                    'GEÇ GELDİ & ERKEN ÇIKTI'   => ['bg' => '#fef2f2', 'color' => '#b91c1c', 'border' => '1px solid #ef4444', 'icon' => '⚠'],
                    'GEÇ GELDİ & ÇIKIŞ YAPMADI' => ['bg' => '#fff7ed', 'color' => '#c2410c', 'border' => '1px solid #ea580c', 'icon' => '⚠'],
                    'ERKEN ÇIKTI'               => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '1px solid #3b82f6', 'icon' => '→'],
                    'ÇIKIŞ YAPMADI'             => ['bg' => '#fff7ed', 'color' => '#c2410c', 'border' => '1px solid #ea580c', 'icon' => '⊘'],
                    'İZİNLİ'                    => ['bg' => '#f5f3ff', 'color' => '#5b21b6', 'border' => '1px solid #8b5cf6', 'icon' => '📋'],
                    'GELMEDİ'                    => ['bg' => '#fef2f2', 'color' => '#b91c1c', 'border' => '1px solid #ef4444', 'icon' => '○'],
                    'KART TANIMSIZ'              => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '1px solid #94a3b8', 'icon' => '?'],
                ];
                $durumDefault = ['bg' => '#f3f4f6', 'color' => '#4b5563', 'border' => '1px solid #d1d5db', 'icon' => ''];
            @endphp
            <tr>
                <td class="section section-mobile" style="padding: 20px 16px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="stat-row" style="border-spacing: 0 10px;">
                        @foreach (array_chunk($sectionKeys, 2) as $chunk)
                        <tr>
                            @foreach ($chunk as $key)
                            @php $cfg = $statsConfig[$key] ?? ['color' => '#475569', 'bg' => '#f1f5f9', 'icon' => '·']; $rows = $records[$key] ?? []; $cnt = is_countable($rows) ? count($rows) : 0; @endphp
                            <td class="stat-cell mobile-stack" style="padding: 6px; vertical-align: top;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: {{ $cfg['bg'] }}; border-radius: 12px; border: 1px solid {{ $cfg['color'] }}20;">
                                    <tr>
                                        <td style="padding: 18px 16px; text-align: center;">
                                            <div style="font-size: 28px; font-weight: 700; color: {{ $cfg['color'] }};">{{ $cnt }}</div>
                                            <div style="font-size: 13px; font-weight: 600; color: {{ $cfg['color'] }}; margin-top: 4px;">{{ $cfg['icon'] }} {{ $key }}</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            @endforeach
                            @if (count($chunk) === 1)
                            <td class="stat-cell mobile-stack" style="padding: 6px; width: 50%;"></td>
                            @endif
                        </tr>
                        @endforeach
                    </table>
                </td>
            </tr>

            <!-- Bölümler: $sectionOrder ile sıralı -->
            @foreach ($sectionKeys as $raporAdi)
            @php
                $rows = $records[$raporAdi] ?? [];
                $cfg = $statsConfig[$raporAdi] ?? ['color' => '#475569'];
                $sectionColor = $cfg['color'];
            @endphp
            <tr>
                <td class="section section-mobile" style="padding: 16px 16px 8px 16px;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                        <tr>
                            <td style="background: {{ $sectionColor }}; color: #fff; padding: 14px 18px;">
                                <h2 class="h2" style="color: #fff; font-size: 17px; margin: 0;">{{ $raporAdi }}</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0;">
                                @if (count($rows) > 0)
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="detail-table" style="border-collapse: collapse;">
                                        <tr style="background: {{ $sectionColor }}15;">
                                            <td style="padding: 10px 10px 10px 14px; font-size: 11px; font-weight: 700; color: #374151; border-bottom: 1px solid #e5e7eb; width: 28px; text-align: center;">#</td>
                                            <td style="padding: 10px 14px; font-size: 11px; font-weight: 700; color: #374151; border-bottom: 1px solid #e5e7eb;">Personel</td>
                                            <td style="padding: 10px 14px; font-size: 11px; font-weight: 700; color: #374151; border-bottom: 1px solid #e5e7eb;">Birim</td>
                                            <td style="padding: 10px 14px; font-size: 11px; font-weight: 700; color: #374151; border-bottom: 1px solid #e5e7eb;">Ünvan</td>
                                            <td style="padding: 10px 14px; font-size: 11px; font-weight: 700; color: #374151; border-bottom: 1px solid #e5e7eb;">Durum</td>
                                            <td style="padding: 10px 14px 10px 10px; font-size: 11px; font-weight: 700; color: #374151; border-bottom: 1px solid #e5e7eb; white-space: nowrap;">Giriş – Çıkış</td>
                                        </tr>
                                        @foreach ($rows as $index => $row)
                                        <tr style="background: {{ $index % 2 === 0 ? '#fff' : '#fafbfc' }};">
                                            <td style="padding: 12px 10px 12px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; color: #6b7280; text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
                                            <td style="padding: 12px 14px; border-bottom: 1px solid #f3f4f6; font-size: 14px; font-weight: 600; color: #111827; vertical-align: middle;">{{ trim($row->personel_adsoyad ?? '') ?: '—' }}</td>
                                            <td style="padding: 12px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; color: #4b5563; vertical-align: middle;">{{ trim($row->birim_ad ?? '') ?: '—' }}</td>
                                            <td style="padding: 12px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; color: #4b5563; vertical-align: middle;">{{ trim($row->unvan_ad ?? '') ?: '—' }}</td>
                                            <td style="padding: 12px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; vertical-align: middle;">
                                                @php
                                                    $d = $row->durum ?? '';
                                                    $isIzinli = $d !== '' && str_starts_with($d, 'İZİNLİ');
                                                    $dStyle = $isIzinli ? ($durumStyles['İZİNLİ'] ?? $durumDefault) : ($durumStyles[$d] ?? $durumDefault);
                                                @endphp
                                                <span style="display: inline-block; padding: 5px 11px; border-radius: 6px; font-weight: 600; font-size: 12px; background: {{ $dStyle['bg'] }}; color: {{ $dStyle['color'] }}; border: {{ $dStyle['border'] }};">@if($dStyle['icon'] !== ''){{ $dStyle['icon'] }} @endif{{ $row->durum ?? '—' }}</span>
                                            </td>
                                            <td style="padding: 12px 14px 12px 10px; border-bottom: 1px solid #f3f4f6; font-size: 13px; font-weight: 500; color: #374151; vertical-align: middle; white-space: nowrap;">
                                                @if(!empty($row->giris) || !empty($row->cikis))
                                                    @php
                                                        $g = !empty($row->giris) ? \Carbon\Carbon::parse($row->giris)->format('H:i') : '—';
                                                        $c = !empty($row->cikis) ? \Carbon\Carbon::parse($row->cikis)->format('H:i') : '—';
                                                    @endphp
                                                    {{ $g }} – {{ $c }}
                                                @elseif(!empty($row->gecikme_suresi))
                                                    {{ $row->gecikme_suresi }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <div class="empty-state">Bu bölümde kayıt bulunmuyor.</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            @endforeach

            <!-- Footer -->
            <tr>
                <td style="background: #0f172a; color: #94a3b8; text-align: center; padding: 20px 16px;">
                    <p style="margin: 0 0 6px 0; font-size: 13px;">Bu rapor PDKS sistemi tarafından otomatik oluşturulmuştur.</p>
                    <p style="margin: 0; font-size: 11px; opacity: 0.8;">Gizlilik içinde saklayınız. © {{ date('Y') }} PDKS</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
