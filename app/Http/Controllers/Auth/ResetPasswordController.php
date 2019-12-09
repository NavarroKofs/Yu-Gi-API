<?php

namespace App\Http\Controllers\Auth;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

    class ResetPasswordController extends Controller
    {
        /*
        |--------------------------------------------------------------------------
        | Password Reset Controller
        |--------------------------------------------------------------------------
        |
        | This controller is responsible for handling password reset requests
        | and uses a simple trait to include this behavior. You're free to
        | explore this trait and override any methods you wish to tweak.
        |
        */

        use ResetsPasswords;

        /**
         * Where to redirect users after resetting their password.
         *
         * @var string
         */
        protected $redirectTo = '/home';

        public function resetPassword( Request $email){

            //Validacion de input
            $validacion = $this->validate($email, [
                'email' => 'email|required|string'
            ]
    );
          
           $emailRequired = $email->email;
          
         

    //Buscar el email en la base de datos
           $validacionCorreo = DB::table('users')->where('email', '=', $emailRequired)->first();
           //validación de la existencia del correo
        if($validacionCorreo){
        DB::table('password_resets')->insert([
        'email' => $email->email,
        'token' => str_random(4),
        'created_at' => Carbon::now()
    ]);
      
        $tokenData = DB::table('password_resets')
        ->where('email', $email->email)->first();
           //  return response()->json($tokenData,201);


    if ($this->sendResetEmail($email->email, $tokenData->token)) {
        return response()->json($tokenData->token,200);
    } else {
        return response()->json([

            "errors"=> ["code"=>"503",
            "title"=>"Service Unavailable",
            "description" => "Server has failed"

        ]], 503);
    }
           }else{
            return response()->json([
                
            "errors"=> ["code"=>"401",
            "title"=>"Unauthorized",
            "description"=> "users mail doesnt exists"
        ]], 401);
           }
          
        
          

        }
        private function sendResetEmail($email, $token){
       
    $userEmail = DB::table('users')->where('email', $email)->select('email')->first();
    $username = DB::table('users')->where('email', $email)->select('name')->first();
    
     if($userEmail){
        $datos =[];
            $datos['emai']=$userEmail;
            Mail::send('emails', ['token' => $token, 'email'=> $email], function($msg)use($email){
                $msg->from('poiupioroleaowo@gmail.com', 'PoioTeam');
                $msg->to($email)->subject('datos enviados');
            });
            return true;

     }else{
        return false;
     }
             }

    public function resetPasswordComplete(Request $request)
{
   
    $validator = Validator::make($request->all(), [
        'email' => 'required',
        'password' => 'required'
    ]);

   
    if ($validator->fails()) {
            return response()->json([
                    "errors"=> ["code"=> "422",
                   "title"=>  "Unprocessable Entity"
                   
                   ]]  , 422);

    }

    $password = $request->password;

    $tokenData = DB::table('password_resets')
    ->where('token', $request->token)->first();

    if (!$tokenData){

    return response()->json([
        "errors"=> [
            "code"=>"401",

            "title"=>"Unauthorized",
            "description" => "Invalid token"
    ]], 401);
    } 

    $user = User::where('email', $tokenData->email)->first();

    if (!$user){
            return response()->json([
        "errors"=> [
            "code"=>"401",

            "title"=>"Unauthorized",
            "description" => "Invalid user"
    ]], 401);
    }

    $user->password = \Hash::make($password);
    $user->update();


    Auth::login($user);


    DB::table('password_resets')->where('email', $user->email)
    ->delete();

        return  response()->json(200);
   
}
    }
