<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Meta;
use Flash;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    // public function render($request, Exception $e)
    // {
    //     if($e instanceof \PDOException)
    //     {
    //         return response()->view('errors.sql');
    //     }
    //     if($this->isHttpException($e))
    //     {
    //         switch ($e->getStatusCode()) {
                
    //             case '404':
    //                 return response()->view('errors.404',[],404);
    //             break;
    //             case '500':
    //                 return response()->view('errors.500',[],500);    
    //             break;
    //             default:
    //                 return $this->renderHttpException($e);
    //             break;
    //             }
    //     }        // dd($request);
    //     if(env('APP_ENV','local')!='local')
    //     {
    //       if($e instanceof \Cartalyst\Sentinel\Checkpoints\ThrottlingException)
    //       {
    //         Flash::error($e->getMessage());
    //         // dd($e->getMessage());
    //         return redirect()->back();

    //       }

    //       if($e instanceof Exception)
    //       {
    //         // dd($e->getMessage().'123');
    //         parent::report($e);
    //         Meta::setTitle('404 Page Not Found ');

    //         return response()->view('errors.404',[],404);
    //       }
    //     }

    //     return parent::render($request, $e);
    // }
    public function render($request, Exception $e)
    {
        if($e instanceof \Cartalyst\Sentinel\Checkpoints\ThrottlingException)
        {
            if ($request->ajax() || $request->is('api/*')) {
                return response()->json(['status' => 'error','msg'=>$e->getMessage()], 404);
            }

            \Flash::error($e->getMessage());
            return redirect()->back();
        }

        if ($e instanceof TokenMismatchException){

            if ($request->ajax() || $request->is('api/*')) {
                return response()->json(['status' => 'error','msg'=>$e->getMessage()], 404);
            }

            \Flash::error("You page session expired.Please try again.");
            return redirect(url()->previous());
        }
        if($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException)
        {
            $error_msg = 'Sorry, the page you are looking for could not be found.';

            if ($request->ajax() || $request->is('api/*')) {
                return response()->json(['status' => 'error','msg'=>$error_msg], 404);
            }

            return response()->view('errors.404',['error_msg'=>$error_msg],404);

        }
        // if($e instanceof \Exception)
        // {
        //     $error_msg = $e->getMessage();

        //     if ($request->ajax() || $request->is('api/*')) {
        //         return response()->json(['status' => 'error','msg'=>$error_msg], 404);
        //     }
        //     return response()->view('errors.404',['error_msg'=>$error_msg],404);
        // }

        return parent::render($request, $e);
    }
}
