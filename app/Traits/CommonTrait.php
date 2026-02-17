<?php

namespace App\Traits;

trait CommonTrait
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendViewResponse($response, $message = null)
    {
        $response['status'] = 'success';
        $response['success'] = true;
        return response()->json($response, 200);
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($response, $message = null)
    {
        $cresponse['status'] = 'success';
        $cresponse['success'] = true;
        $cresponse['data'] = $response;
        $cresponse['message'] = $message;
        return response()->json($cresponse, 200, array('Content-Type'=>'application/json', 'charset'=>'utf-8' ));
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 404)
    {
        $response = $this->getFormatedErrors($error, $code);
        return response()->json($response, 200);
    }

    public function getFormatedErrors($error, $code)
    {
        return [
            'stausCode' => $code,
            'status' => 'error',
            'success' => false,
            'errors' => $error,
        ];
    }
}