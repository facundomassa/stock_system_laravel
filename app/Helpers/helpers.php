<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('get_selected_operation')) {
    /**
     * Obtiene la operación seleccionada desde la sesión.
     *
     * @return mixed|null
     */
    function get_selected_operation()
    {
        return Session::get('selected_operation');
    }
}

if (!function_exists('x_fecha_español')) {
    /**
     * Convierte una fecha al formato en español.
     *
     * @param string $fecha
     * @return string
     */
    function x_fecha_español($fecha)
    {
        setlocale(LC_TIME, 'spanish');
        return strftime('%d de %B', strtotime($fecha));
    }
}

if (!function_exists('get_allowed_operations')) {
    /**
     * Obtiene las operaciones permitidas para el usuario autenticado.
     *
     * @return \Illuminate\Support\Collection
     */
    function get_allowed_operations()
    {
        if (Auth::check()) {
            return Session::get('allowed_operations') ?? Auth::user()->allowedOperations();
        }

        return collect();
    }
}

if (!function_exists('get_allowed_operations_list')) {
    /**
     * Obtiene un listado de los nombres de las operaciones permitidas para el usuario autenticado.
     *
     * @return \Illuminate\Support\Collection
     */
    function get_allowed_operations_list()
    {
        if (Auth::check()) {
            // Obtiene las operaciones permitidas desde la sesión o la relación del usuario
            $allowedOperations = Session::get('allowed_operations') ?? Auth::user()->allowedOperations();

            // Retorna solo los nombres de las operaciones como una colección
            return $allowedOperations->pluck('name');
        }

        // Retorna una colección vacía si el usuario no está autenticado
        return collect();
    }
}