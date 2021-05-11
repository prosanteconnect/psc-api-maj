<?php


namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;

trait ApiResponder
{

    /**
     * @param string $message
     * @param null $data
     * @return mixed
     */
    public function notFoundResponse($message = 'Not found!', $data = null){
        return $this->errorResponse($message, 404, $data);
    }

    /**
     * @param string $message
     * @param null $data
     * @return mixed
     */
    public function alreadyExistsResponse($message = 'Already exists!', $data = null){
        return $this->errorResponse($message, 409, $data);
    }

    /**
     * @param string $message
     * @param null $data
     * @return mixed
     */
    public function internalErrorResponse($message = 'Internal Error!', $data = null){
        return $this->errorResponse($message, 500, $data);
    }

    /**
     * @param $message
     * @param $code
     * @param null $data
     * @return JsonResponse
     */
    public function errorResponse($message, $code, $data = null): JsonResponse
    {
        return response()->json([
            'status'=>'Error',
            'message' => $message,
            'data' => $data
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
