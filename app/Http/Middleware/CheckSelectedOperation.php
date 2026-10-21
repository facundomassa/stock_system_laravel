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
        // Si la operación de la sesión ya no está entre las permitidas (se le quitó el permiso
        // al usuario, o el valor no es válido), se descarta y se vuelve a pedir
        if (Session::has('selected_operation') && !get_allowed_operations_list()->contains(Session::get('selected_operation'))) {
            Session::forget('selected_operation');
        }

        if (!Session::has('selected_operation') && $request->path() !== 'operation-select') {
            if (auth()->check() && auth()->user()->hasRole('tecnico')) {
                return $next($request);
            }
            // Redirige a 'operation-select' solo si no es la página actual
            return redirect('operation-select')->with('error', 'Debes seleccionar una operación antes de acceder a esta página.');
        }

        return $next($request);
    }
}
