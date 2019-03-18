<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Auth;
use App\Car;

class CarController extends Controller
{
    public function index(Request $request){
        
        $hash = $request->header('Authorization', null);

        $jwt = new JwtAuth();
        $checkToken = $jwt->checkToken($hash);

        if($checkToken){

            echo "identificado";
            die();

        }else{

            echo "Erro de identificacion";
            die();
        }
    }

    public function store(Request $request){
        
        $hash = $request->header('Authorization', null);

        $jwt = new JwtAuth();
        $checkToken = $jwt->checkToken($hash);

        if($checkToken){

            //recibir por post
            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            // get usuario
            $user = $jwt->checkToken($hash, true);
            
            // validacion de datos
            $request->merge($params_array);

            try{

                $validate = $this->validate($request, [

                    'title' => 'required|min:5',
                    'description' => 'required',
                    'price' => 'required',
                    'status' => 'required'
                ]);

            }catch(\Illuminate\Validation\ValidationException $e){

                return $e->getResponse();
            }

            // guardar el coche
            
            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->price = $params->price;
            $car->status = $params->status;

            $car->save();

            $data = array(

                'car' => $car,
                'status' => 'success',
                'code' => 200
            );

        }else{

            // devolver error
            $data = array(

                'message' => 'login incorrecto',
                'status' => 'error',
                'code' => 400
            );
        }

        return response()->json($data, 200);
    }
}

