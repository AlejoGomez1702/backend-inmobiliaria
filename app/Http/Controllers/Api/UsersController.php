<?php

namespace App\Http\Controllers\Api;

use App\Providers\HttpRequestsProvider as ClientHttp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function getAllUsers(Request $request)
    {
        $client = new ClientHttp('');
        // $filters = $request->all();
        $data = $client->get('user/all-users');
        $status = $data['status'];
        // $total = $data['total'];
        unset($data['status']);

        return response()->json([
            'users' => $data,
            'status' => $status,
            // 'total' => $total,
            // 'pruebas' => $filters
        ], 200);
    }
}
