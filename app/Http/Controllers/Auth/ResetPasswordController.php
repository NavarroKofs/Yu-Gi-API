    <?php

    namespace App\Http\Controllers\Auth;
    use Auth;
    use App\Http\Controllers\Controller;
    use Illuminate\Foundation\Auth\ResetsPasswords;
    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
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
        'token' => str_random(60),
        'created_at' => Carbon::now()
    ]);
        //Get the token just created above
        $tokenData = DB::table('password_resets')
        ->where('email', $email->email)->first();
          


    if ($this->sendResetEmail($email->email, $tokenData->token)) {

        return response()->json("Se ha enviado un correo para cambiar la contrasena a " . "$email->email",200);

    } else {
        return response()->json([

            "errors"=> ["code"=>"Network Error",
            "title"=>"Ha ocurrido un problema, por favor intenta de nuevo"]], 422);
    }
           }else{

            return response()->json([
            "errors"=> ["code"=>"Error-1",
            "title"=>"El usuario no se encuentra registrado"
        ]], 422);

           }
       
          

        }
    private function sendResetEmail($email, $token){

       //Obtener el usuario de la base de datos
    $user = DB::table('users')->where('email', $email)->select('email')->first();

    //Generar un link con el token incluido en el mismo
    $link = config('base_url') . 'password/reset/' . $token . '?email=' . urlencode($user->email);

        try {
       
            return true;
        } catch (\Exception $e) {
            return false;
        }
        }
    }
