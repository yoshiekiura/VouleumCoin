<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        if($exception instanceof QueryException)
        {
            $check_dt = \IcoHandler::checkDB();
            if(empty($check_dt)){
                $heading = 'Something is wrong in Database!';
                $message = 'Please re-check your database connection, tables and columns etc.';
                return response()->view('errors.custom', ['heading'=>$heading, 'message'=>$message]);
            }
            else{
                return response()->view('errors.db_error', compact('check_dt'));
            }
            
        }
        if($exception instanceof \PDOException)
        {
            $heading = 'Unable Connect Database!';
            $message = 'Please re-check your database name, username and password.';
            return response()->view('errors.custom', ['heading'=>$heading, 'message'=>$message]);

        }
        return parent::render($request, $exception);
    }
}
