<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Sidebar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $user = Auth::user(); // Usuário autenticado

        $host = $request->getHost();

        $parts = explode('.', $host);
        $subdomain = $parts[0] ?? null;

        $sidebarItems = [];

        if ($user) {
            if ($subdomain == 'dash') {
                $sidebarItems = [
                    [
                        'title' => "Assinaturas",
                        "items" => [
                            [
                                "icon" => "pi-money-bill",
                                "name" => "Assinaturas",
                                "path" => route("backoffice.admin.subscriptions.show"),
                            ],
                            [
                                "icon" => "pi-align-justify",
                                "name" => "Planos",
                                "path" => route("backoffice.admin.plans.show"),
                            ],
                        ]
                    ],
                    // [
                    //     'title' => "Segurança",
                    //     'items' => [
                    //         [
                    //             "icon" => "mdiCog",
                    //             "name" => "Configurações",
                    //             "path" => route("dash.app.sessoes.show"),
                    //         ],
    
                    //     ]
                    // ]
                ];
            } 
        }

        Inertia::share("sidebarItems", $sidebarItems);

        return $next($request);
    }
}
