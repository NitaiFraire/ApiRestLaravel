<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Auth;
use App\Car;

class CarController extends Controller
{
    public function index(){
        
        // registros de la tabla car
        $cars = Car::all()->load('user');

        return response()->json(array(

            'cars' => $cars,
            'status' => 'success' 
        ), 200);
    }

    public function show($id){

        $car = Car::find($id)->load('user');

        return response()->json(array(
            
            'car' => $car,
            'status' => 'success'
        
        ), 200);
    }

    public function update($id, Request $request){

        $hash = $request->header('Authorization', null);

        $jwt = new JwtAuth();
        $checkToken = $jwt->checkToken($hash);   

        if($checkToken){

            // recoger parametros post
            $json = $request->input('json', null);
            $params = json_decode($json);

            $params_array = json_decode($json, true);

            // validar datos
            $validate = \Validator::make($params_array, [

                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            if($validate->fails()){

                return response()->json($validate->errors(), 400);
            }

            // actualizar registro
            $car = Car::where('id', $id)->update($params_array);

            $data = array(

                'car' => $params,
                'status' => 'success',
                'code' => 200
            );

        }else{

            $data = array(

                'message' => 'login incorrecto',
                'status' => 'error',
                'code' => 400
            );

            return response()->json($data, 400);
        }

        return response()->json($data, 200);

    }

    public function destroy($id, Request $request){
        
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
    
        if($checkToken){

            // comprobar que existe el registro
            $car = Car::find($id);
            
            // borrarlo
            $car->delete();

            // devolverlo
            $data = array(

                'car' => $car,
                'status' => 'success',
                'code' => 200
            );

        }else{

            $data = array(

                'status' => 'error',
                'code' => 400,
                'message' => 'login incorrecto'

            );

            return response()->json($data, 400);
        }

        return response()->json($data, 200);
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

                $validate = \Validator::make($params_array, [

                    'title' => 'required|min:5',
                    'description' => 'required',
                    'price' => 'required',
                    'status' => 'required'
                ]);

                if($validate->fails()){

                    return response()->json($validate->errors(), 400);
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

