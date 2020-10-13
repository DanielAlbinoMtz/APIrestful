<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Stmt\If_;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['store','resend']);
        $this->middleware('auth:api')->except(['store','verify','resend']);      
        $this->middleware('transform.input:' . UserTransformer::class)->only(['store','update']);

        $this->middleware('scope:manage-account')->only(['show','update']);

        $this->middleware('can:view,user')->only('show');
        $this->middleware('can:update,user')->only('show');
        $this->middleware('can:delete,user')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->allowedAdminAction();

        $usuarios = User::all();

        /* return response()->json(['data' => $usuarios], 200); */
        /* con traits */
        return $this->showAll($usuarios);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];

        $this->validate($request, $rules);

        $campos = $request->all();
        $campos['password'] = bcrypt($request->password);
        $campos['verified'] = User::USUARIO_NO_VERIFICADO;
        $campos['verification_token'] = User::generarVerificationToken();
        $campos['admin'] = User::USUARIO_REGULAR;


        $usuario = User::create($campos);

        /* return response()->json(['data' => $usuario], 201); */
        /* con traits */
        return $this->showOne($usuario, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
          /* return response()->json(['data' => $user], 200); */

        return $this->showOne($user);
    }

    
    public function update(Request $request, User $user)
    {

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::USUARIO_ADMINISTRADOR . ',' . User::USUARIO_REGULAR,
        ];

        $this->validate($request, $rules);
        /* revisar si el campo de nombre ha sido cambiado */
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        /* verificar si el email ha sido cambiado */
        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::USUARIO_NO_VERIFICADO;
            $user->verification_token = User::generarVerificationToken();
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        /* error 409 un error en la peticion */

        if ($request->has('admin')) {
            $this->allowedAdminAction();

            if (!$user->esVerificado()) {
                return $this->errorResponse('Unicamente los usuarios verificados pueden cambiar su valor de administrador', 409);
            }

            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }

        $user->save();
        return $this->showOne($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
       $user->delete();

        return $this->showOne($user, 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        
        return $this->showOne($user);
    }

    public function verify($token){
        $user = User::where('verification_token', $token)->firstOrFail();
        $user->verified = User::USUARIO_VERIFICADO;
        $user->verification_token = null;

        $user->save();
        
        return $this->showMessage('La cuenta ha sido verificada');

    }

    public function resend(User $user)
    {
        if($user->esVerificado()){
            return $this->errorResponse('Este usuario ya ha sido verificado',409);
        }

        retry(5, function () use($user) {
            Mail::to($user)->send(new UserCreated($user));
        },100);

        return $this-> showMessage('El correo de verificación se ha reenviado');
    }
}
