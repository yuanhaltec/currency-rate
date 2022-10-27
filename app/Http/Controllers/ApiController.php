<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{

    protected function response($data, $message = null, $code = Response::HTTP_OK)
    {
        $message ??= 'Success retrieve data';
        $response['message'] = $message;

        if (is_array($data)) {
            $response['data'] = $data;
        } else {
            $response['message'] = $data;   
        }     
           
        return response($response, $code);
    }

    protected function errorResponse($message, $code = Response::HTTP_BAD_REQUEST)
    {
        $response = [
            'message' => $message
        ];
        return response($response, $code);
    }

    protected function exceptionResponse($e, $code = 500) {

        if ($e instanceof ValidationException) {
            $code = Response::HTTP_BAD_REQUEST;
            $response = [
                'message' => 'Invalid input',
                'validation' => $e->errors()
            ];
        } elseif ($e instanceof ModelNotFoundException) {
            $code = Response::HTTP_NOT_FOUND;
            $response = [
                'message' => $e->getMessage()
            ];
        } else {
            $response = [
                'message' => $e->getMessage()                
            ];
        }

        return response($response, $code);
    }
}