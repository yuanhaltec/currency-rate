<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ApiController extends Controller
{

    protected function response($data, $message = null, $code = Response::HTTP_OK)
    {
        $message ??= 'Success retrieve data';
        $response['message'] = $message;

        if (is_object($data) || is_array($data)) {
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

    protected function exceptionResponse($e, $code = Response::HTTP_INTERNAL_SERVER_ERROR) {

        if ($e instanceof ValidationException) {
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
            $response = [
                'message' => 'Invalid input',
                'validation' => $e->errors()
            ];
        } elseif ($e instanceof ModelNotFoundException) {
            $code = Response::HTTP_NOT_FOUND;
            $response = [
                'message' => $e->getMessage()
            ];
        } elseif ($e instanceof UnprocessableEntityHttpException) {
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
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