<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function createTask(Request $request)
    {
        $client = new ClientHttp('');
        $filters = ['email' => $request->id_user];

        $data = $client->get('user/all-users', $filters);
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
