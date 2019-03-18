<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use App\User;


class UserController extends Controller
{
    public function register(Request $request){
        
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'USER_ROLE';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if(!is_null($email) && !is_null($password) && !is_null($name)){

            // crear usuario
            $user = new User;

            $user->email = $email;
            $user->role = $role;
            $user->name = $name;
            $user->surname = $surname;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            // Comprobar usuario duplicado
            $issetUser = User::where('email', '=', $email)->count();

            if($issetUser == 0){

                // guardar
                $user->save();

                $data = array(

                    'status' => true,
                    'code' => 400,
                    'message' => 'Usuario registrado correctamente'
                );
                        
            }else{

                // no guardar
                $data = array(

                    'status' => false,
                    'code' => 400,
                    'message' => 'Usuario duplicado, no puede registrarse'
                );
            }


        }else{

            $data = array(

                'status' => false,
                'code' => 400,
                'message' => 'Usuario no creado'
            );
        }

        return response()->json($data, 200);
    }

    public function login(Request $request){
        
        $jwt = new JwtAuth();

        // recibir datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->getToken)) ? $params->getToken : null;

        // cifrar password
        $pwd = hash('sha256', $password);

        if($email && $password && ($getToken == null || $getToken == 'false')){

            $signUp = $jwt->signUp($email, $pwd);

        }elseif($getToken != null){

            $signUp = $jwt->signUp($email, $pwd, $getToken);

        }else{

            $signUp = array(

                'status' => 'error',
                'message' => 'envia datos por post'
            );

            return response()->json($signUp, 400);
        }

        return response()->json($signUp, 200);
    }
}
