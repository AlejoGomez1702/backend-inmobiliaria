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

        if ($response['status'] == "success") {
            unset($response['status']);
            foreach ($response as $task) {
                switch ($task['management_status_id']) {
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
        $sale_value = ($for_sale === 'true');

        $property_client = $client->get('client/get/' . $request->clients[0]);


        $user = $client->get('user/get/' . $request->id_user);
        $name = $user['first_name'];
        $lastname = $user['last_name'];
        $nameClient = $property_client['first_name'];
        $lastnameClient = $property_client['last_name'];
        $fecha = $request->date;
        $fechaCarbon = new Carbon($fecha);
        $fechaVisita = $fechaCarbon->format('d-m-Y');
        $horaVisita = $fechaCarbon->format('H:i:s');

        return response()->json([
            'code' => $for_sale,
            'status' => 'success',
            'fecha' => $fechaVisita,
            'hora' => $horaVisita
        ], 200);

        if ($response == 200) {
            //SEND EMAIL
            $details = [
                'title' => 'Se??or(a) ' . $nameClient . ' ' . $lastnameClient,
                'body' => 'De acuerdo a nuestra conversaci??n, le informamos que el d??a ' . $fechaVisita . ' a las ' . $horaVisita . ' visitaremos el inmueble en menci??n {ficha} ' . ' con el cliente ' . $nameClient . ' ' . $lastnameClient . '\n' . '{ficha} el inmueble tiene adjunto una ficha, se debe enviar en ese correo como adjunto en pdf. \n Por favor tener en cuenta que cualquier acercamiento y negociaci??n deber?? hacerse con nuestro conocimiento y participaci??n. En caso de cerrarse el negocio, la remuneraci??n de la inmobiliaria por la administraci??n del inmueble es el 10% m??s IVA del mismo porcentaje. \n Cordial saludo, \n
                Nombre del agente y de la empresa \n' . $name . ' ' . $lastname,
            ];

            $details2 = [
                'title' => 'Se??or(a) ' . $nameClient . ' ' . $lastnameClient,
                'body' => 'De acuerdo a nuestra conversaci??n, le informamos que el d??a ' . $fechaVisita . ' a las ' . $horaVisita . ' visitaremos el inmueble en menci??n {ficha}' . ' con el cliente ' . $nameClient . ' ' . $lastnameClient . '\n' . '{ficha} el inmueble tiene adjunto una ficha, se debe enviar en ese correo como adjunto en pdf. \n Por favor tener en cuenta que cualquier acercamiento y negociaci??n deber?? hacerse con nuestro conocimiento y participaci??n. En caso de cerrarse el negocio, la remuneraci??n de la inmobiliaria por la administraci??n del inmueble es el 3% m??s IVA del mismo porcentaje. \n Cordial saludo, \n
                Nombre del agente y de la empresa \n' . $name . ' ' . $lastname,
            ];

            if ($sale_value) {
                \Mail::to('danipipe1998@hotmail.com')->send(new \App\Mail\ClientMail($details));
            } else {
                \Mail::to('danipipe1998@hotmail.com')->send(new \App\Mail\ClientMail($details2));
            }
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
