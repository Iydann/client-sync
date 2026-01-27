<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetGlobalProjectYear
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sedang mengganti filter lewat dropdown (query param)
        if ($request->has('year')) {
            session(['project_year' => $request->integer('year')]);
        }

        // 2. Jika tidak ada di session (login pertama), set default ke tahun ini
        if (!session()->has('project_year')) {
            session(['project_year' => now()->year]);
        }

        // Opsional: Share ke semua view agar bisa dipanggil $projectYear di blade manapun
        // view()->share('projectYear', session('project_year'));

        return $next($request);
    }
}