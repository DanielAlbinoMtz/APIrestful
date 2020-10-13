<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Asm89\Stack\CorsService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException as ExceptionMethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {   
        if($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception,$request);
        }

        if ($exception instanceof ModelNotFoundException){
            $modelo = strtolower(class_basename($exception -> getModel()));
            return $this->errorResponse("No existe ninguna instancia de {$modelo} con el id especificadoo",404);
        }
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if($exception instanceof AuthorizationException){
            return $this->errorResponse('No posee permiso para realizar esta acción',403);
        }
        if($exception instanceof NotFoundHttpException){
            return $this->errorResponse('No se encontro la url especificada',404);
        }
        if($exception instanceof ExceptionMethodNotAllowedHttpException){
            return $this->errorResponse('El método especificado en la petición no es valido',405);
        }
        if($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
        if($exception instanceof QueryException){
            $codigo = $exception->errorInfo[1];
            if($codigo==1451){
                return $this->errorResponse('No se puede eliminar de forma permanente el recurso porque está relacionado con algún otro.',409);
            }            
        }
        if ($exception instanceof TokenMismatchException){ 
            return redirect()->back()->withInput($request->input());
        }
        if(config('app.debug')){
           return parent::render($request, $exception);  
        }

        return $this->errorResponse('Falla inesperada. Intente luego',500);

       /*  app(CorsService::class)->addActualRequestHeaders($response,$request); */

       
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($this->isFronted($request)){
            return redirect()->guest('login');
        }
            return $this->errorResponse('No autenticado.',401);
        
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        if ($this->isFronted($request)) {
            return $request->ajax() ? response()->json($errors, 422) : redirect()
            ->back()
            ->withInput($request->input())
            ->withErrors($errors);
        }

        return $this->errorResponse($errors,422);
       /*  return response()->json($errors,422); */
    }

    private function isFronted($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
