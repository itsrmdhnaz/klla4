<?php

namespace App\Helpers;

/**
 * Format response.
 */
class Response
{
    /**
     * Give success response.
     */
    public static function success($data = null, ?string $message = null, $code = 200)
    {
        return response()->json([
            'error' => false,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Give error response.
     */
    public static function error($data = null, ?string $message = null, $code = 400)
    {
        return response()->json([
            'error' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Give error response.
     */
    public static function errorValidate($data = null, ?string $message = null, $code = 422)
    {
        return response()->json([
            'error' => true,
            'code' => $code,
            'message' => $message,
            'errors' => $data,
        ], $code);
    }

    /**
     * Give catch error response.
     */
    public static function errorCatch(\Exception $e, ?string $message = null, ?int $code = 500) {
        
        if (self::isValidHttpStatusCode($e->getCode())) {
            $code = $e->getCode();
        }

        if (config('app.debug') == true) {
            return response()->json([
                'error' => true,
                'getLine' => $e->getLine(),
                'code' => $code,
                'message' => $message ?? $e->getMessage(),
            ], $code);
        }
         
        return response()->json([
            'error' => true,
            'code' => 500,
            'message' => 'Something went wrong!',
        ], 500);
    }

    private static function isValidHttpStatusCode($code) {
        return is_int($code) && $code >= 100 && $code < 600;
    }
}
