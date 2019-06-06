<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    //
    public function index()
    {
        $data = DB::table('configs')->find(1);
        $data->date = date('Y-m-d H:i:s');
        return response()->json(['Code'=>200,'Data' => $data]);
    }
}
