<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PDKS route adına göre ilgili izni kontrol eder.
 * Route adı ile permission adı eşleşiyorsa (pdks.bugun -> pdks.bugun) tek noktadan yetki.
 */
class EnsurePdksPermission
{
    protected static array $routePermissionMap = [
        'pdks.bugun' => 'pdks.bugun',
        'pdks.giriscikis' => 'pdks.giriscikis',
        'pdks.giriscikis.export' => 'pdks.giriscikis',
        'pdks.gecgelen' => 'pdks.gecgelen',
        'pdks.erkencikan' => 'pdks.erkencikan',
        'pdks.gelmeyen' => 'pdks.gelmeyen',
        'pdks.gecislog' => 'pdks.gecislog',
        'pdks.hareket' => 'pdks.hareket',
        'pdksgecisekle.store' => 'pdks.gecisekle',
        'pdks.personel-kart-ara' => 'pdks.gecisekle',
        'pdks.gunluk-aciklama.get' => 'pdks.menu',
        'pdks.gunluk-aciklama.store' => 'pdks.menu',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $permission = self::$routePermissionMap[$routeName] ?? null;

        if ($permission && !$request->user()?->can($permission)) {
            abort(403, 'Bu sayfa için yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}
