<?php

namespace App\Http\Controllers\Api;

use App\Providers\HttpRequestsProvider as ClientHttp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function getAllClients(Request $request)
    {
        $client = new ClientHttp('');
        $filters = $request->all();
        $data = $client->get('client/search', $filters);
        $status = $data['status'];
        $total = $data['total'];
        unset($data['status']); unset($data['total']);

        return response()->json([
            'clients' => $data,
            'status' => $status,
            'total' => $total,
            // 'pruebas' => $filters
        ], 200);
    }
}
