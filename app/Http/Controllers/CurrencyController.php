<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CurrencyController extends ApiController
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function index()
    {
        try {
            $result = $this->currencyService->get();
            return $this->response($result);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function create(Request $request)
    {
        
        try {
            $result = $this->currencyService->create($request);
            if ($result) {
                return $this->response('Successfully create currency');
            } else {
                return $this->errorResponse('Failed create currency');
            }
        } catch (ValidationException $e) {
            return $this->exceptionResponse($e);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function update(Request $request, $id) {
        try {
            $result = $this->currencyService->update($request, $id);
            if ($result) {
                return $this->response('Successfully update currency');
            } else {
                return $this->errorResponse('Failed update currency');
            }
        } catch (ValidationException $e) {
            return $this->exceptionResponse($e);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function delete($id) {
        try {
            $result = $this->currencyService->delete($id);
            if ($result) {
                return $this->response('Successfully delete currency');
            } else {
                return $this->errorResponse('Failed delete currency');
            }
        } catch (Exception $e) {
            return $this->exceptionResponse($e, Response::HTTP_BAD_REQUEST);
        }
    }

    public function rate(Request $request, $from, $to)
    {
        try {
            $result = $this->currencyService->rate($request, $from, $to);
            return $this->response($result);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}