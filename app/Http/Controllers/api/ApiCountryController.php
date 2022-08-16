<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiCountryController extends Controller
{
    //
    public function country(){

        $response = Http::withHeaders([
            "Accept" => "application/json",
            "api-token" => 
            "tiEBZQn7tQcwSuDIYI-OnMrdFx9N2tajptZ0k5OAWk3uQPhA0-jS0PtVk23m8A5-xWU",
            "user-email" => "massafacundo11@gmail.com"
        ])->get('https://www.universal-tutorial.com/api/getaccesstoken');

        $token = $response->json("auth_token");

        $country = Http::withHeaders([
            "Authorization" => "Bearer " . $token,
            "Accept" => "application/json",
        ])->get('https://www.universal-tutorial.com/api/countries/');
        
        return response($country->json(), 200);
    }
    
    public function state($country){

        $response = Http::withHeaders([
            "Accept" => "application/json",
            "api-token" => 
            "tiEBZQn7tQcwSuDIYI-OnMrdFx9N2tajptZ0k5OAWk3uQPhA0-jS0PtVk23m8A5-xWU",
            "user-email" => "massafacundo11@gmail.com"
        ])->get('https://www.universal-tutorial.com/api/getaccesstoken');

        $token = $response->json("auth_token");

        $country = Http::withHeaders([
            "Authorization" => "Bearer " . $token,
            "Accept" => "application/json",
        ])->get('https://www.universal-tutorial.com/api/states/' . $country);
        
        return response($country->json(), 200);
    }
}
