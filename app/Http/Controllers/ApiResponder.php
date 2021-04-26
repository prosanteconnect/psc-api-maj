<?php


namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;

trait ApiResponder
{

    /**
     * @param string $message
     * @return mixed
     */
    public function notFoundResponse($message = 'Not found!'){
        return $this->errorResponse($message, 404);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function alreadyExistsResponse($message = 'Already exists!'){
        return $this->errorResponse($message, 409);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function internalErrorResponse($message = 'Internal Error!'){
        return $this->errorResponse($message, 500);
    }

    /**
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    public function errorResponse($message, $code): JsonResponse
    {
        return response()->json([
            'status'=>'Error',
            'message' => $message,
            'data' => null
        ], $code);
    }

    /**
     * @param $data
     * @param null $message
     * @return JsonResponse
     */
    public function successResponse($data, $message = null): JsonResponse
    {
        return response()->json([
            'status'=> 'Success',
            'message' => $message,
            'data' => $data
        ]);
    }

}
