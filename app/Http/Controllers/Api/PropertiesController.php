<?php

namespace App\Http\Controllers\Api;

use App\Providers\HttpRequestsProvider as ClientHttp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PropertiesController extends Controller
{
    public function getAllProperties(Request $request)
    {
        $client = new ClientHttp('');
        $filters = $request->all();
        $data = $client->get('property/search', $filters);
        $status = $data['status'];
        $total = $data['total'];
        unset($data['status']); unset($data['total']);

        return response()->json([
            'properties' => $data,
            'status' => $status,
            'total' => $total,
            // 'pruebas' => $filters
        ], 200);
    }


    public function getPropertyById($id)
    {
        $client = new ClientHttp('');
        $data = $client->get('property/get/' . $id);

        return response()->json($data, 200);
    }


}
