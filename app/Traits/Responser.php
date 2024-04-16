<?php

namespace App\Traits;

trait Responser
{
    public function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    public function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    public function showAll(Collection $collection, $status, $msg, $code = 200)
    {
        return $this->successResponse(['payload' => $collection, 'success' => $status, 'msg' => $msg], $code);
    }

    public function showOne(Model $model, $code = 200)
    {
        return $this->successResponse(['payload' => $model], $code);
    }
}
