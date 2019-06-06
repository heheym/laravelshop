<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Resource not found.'
            ],404);
        }
        elseif ($exception instanceof ValidationException)
        {
           if(is_array($exception->errors())){
                foreach($exception->errors() as $k=>$v){
                    if(is_array($v)){
                        foreach($v as $kk=>$vv){
                            $msg = $vv;
                        }
                    }else{
                        $msg = $v;
                    }
                }
           }
            $ret=new \StdClass();
            $ret->Code="500";
            $ret->Msg=$msg;
            $ret->Data= null;
            return response()->json($ret);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['Code' => 500,'Msg'=>'无效登录,请重新登录','Data'=>null]);
        }

        return redirect()->guest(route('login'));
    }
}
