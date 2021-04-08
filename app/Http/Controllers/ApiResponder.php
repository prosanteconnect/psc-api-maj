<?php


namespace App\Http\Controllers\Api;


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
     * @return mixed
     */
    public function errorResponse($message, $code){
        return response()->json([
            'status'=>'Error',
            'message' => $message,
            'data' => null
        ], $code);
    }

    /**
     * @param $data
     * @param null $message
     * @return mixed
     */
    public function successResponse($data, $message = null)
    {
        return response()->json([
            'status'=> 'Success',
            'message' => $message,
            'data' => $data
        ]);
    }

}
