<?php

namespace App\Http\Controllers\Api;

use App\Providers\HttpRequestsProvider as ClientHttp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TasksController extends Controller
{
    public function getAllTasks(Request $request)
    {
        $client = new ClientHttp('');        
        $current_date = Carbon::now();
        $first_date = $current_date->firstOfMonth()->toDateString();
        $end_date = $current_date->lastOfMonth()->toDateString();        

        $response = $client->getStrict('management/tasks', [
            'id_user' => $request->user_id,
            'start' => $first_date,
            'end' => $end_date
        ]);

        // Obtener las tareas que estan: 1 => vencidas, 2 => en progreso, 3 => completadas
        $expired = [];
        $in_progress = [];
        $completed = [];

        if($response['status'] == "success")
        {
            unset($response['status']);
            foreach ($response as $task) 
            {
                switch ($task['management_status_id']) 
                {
                    case '1':
                        $task = $this->addCalendarData($task);
                        array_push($expired, $task);
                        break;

                    case '2':
                        $task = $this->addCalendarData($task);
                        array_push($in_progress, $task);
                        break;

                    case '3':
                        $task = $this->addCalendarData($task);
                        array_push($completed, $task);
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        }

        return response()->json([
            'expired' => $expired,            
            'in_progress' => $in_progress,
            'completed' => $completed
        ], 200);
    }


    public function createTask(Request $request)
    {
        $client = new ClientHttp('');
        
        // crear una nueva tarea en la base de datos.
        $task_information = $request->all();
        $task_information['id_user'] = $request->id_user;

        $response = $client->post('management/add', $task_information);


        $properties = $request->properties;
        $property = $client->get('property/get/' . $properties[0]);
        $for_sale = $property['for_sale']; //Esto creo que esta sacando un string pero deberia ser booleano


        $user = $client->get('user/get/' . $request->id_user);

        return response()->json([
            'code' => $for_sale,
            'status' => 'success'
            // 'pruebas' => $filters
        ], 200);

        if($response == 200)
        {
            //SEND EMAIL
            $details = [
                'title' => 'Probando la app de vista',
                'body' => 'jejejejejeje'
            ];
            
            \Mail::to('alejogomes2339-12@hotmail.com')->send(new \App\Mail\ClientMail($details));
        }

        return response()->json([
            'code' => $response,
            'status' => 'success'
            // 'pruebas' => $filters
        ], 200);
    }

    public function addCalendarData($task)
    {
        $start_date = Carbon::parse($task['date']);
        $end_date = $start_date->addHour();
        $title = $task['description'];
        $task['allDay'] = false;
        $task['endTime'] = $end_date;
        $task['startTime'] = $start_date;
        $task['title'] = $title;

        return $task;
    }
}
