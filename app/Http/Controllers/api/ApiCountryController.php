<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ApiCountryController extends Controller
{
    /**
     * Fetches a list of countries from a local JSON file.
     *
     * @return \Illuminate\Http\Response
     */
    public function country()
    {
        $path = database_path('data/countries.json');
        if (File::exists($path)) {
            $json = File::get($path);
            $countries = collect(json_decode($json, true))->sortBy('name')->values();
            return response($countries, 200);
        }

        return response()->json(['error' => 'Country data not found.'], 404);
    }

    /**
     * Fetches states for a given country name from the local countries JSON file.
     *
     * @param  string  $country_name
     * @return \Illuminate\Http\Response
     */
    public function state($country_name)
    {
        $path = database_path('data/countries.json');
        if (File::exists($path)) {
            $json = File::get($path);
            $countries = collect(json_decode($json, true));

            // Find the country by name
            $country = $countries->firstWhere('name', $country_name);

            if ($country && !empty($country['states'])) {
                // Sort states by name and return them
                $states = collect($country['states'])->sortBy('name')->values();
                return response($states, 200);
            }
        }

        // Return an empty array if country not found or has no states
        return response()->json([]);
    }
}
