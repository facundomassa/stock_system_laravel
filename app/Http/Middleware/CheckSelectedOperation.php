<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; 

class CheckSelectedOperation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (!Session::has('selected_operation') && $request->path() !== 'operation-select') {
            // Redirige a 'operation-select' solo si no es la página actual
            return redirect('operation-select')->with('error', 'Debes seleccionar una operación antes de acceder a esta página.');
        }

        return $next($request);
    }
}
