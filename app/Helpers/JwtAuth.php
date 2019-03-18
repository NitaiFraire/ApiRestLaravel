<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

    public $key;

    public function __construct(){

        $this->key = '4321432432432143216545-&';
    }

    public function signUp($email, $password, $getToken = null){
        
        $user = User::where(

            array(

                'email' => $email,
                'password' => $password
            
                ))->first();

        $signUp = false;

        if(is_object($user)){

            $signUp = true;
        }

        if($signUp){

            // generar token y devolverlo
            $token = array(

                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 10)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));


            if(is_null($getToken)){

                return $jwt;

            }else{

                return $decoded;
            }

        }else{

            // devolver error
            return array('status' => false, 'message' => 'Login ha fallado');
        }
    }

    public function checkToken($jwt, $getIdentity = false){
        
        $auth = false;

        try{

            $decoded = JWT::decode($jwt, $this->key, array('HS256'));

        }catch(\UnexpectedValueException $e){

            $auth = false;

        }catch(\DomainException $e){

            $auth = false;

        }

        if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){

            $auth = true;

        }else{

            $auth = false;
        }

        if($getIdentity){

            return $decoded;
        }

        return $auth;
    }
}