<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function response(array $data, string $message = "", array $errors = [], bool $success = true, int $code = 200): JsonResponse
    {
        $response = [
            "Success" => $success,
            "Data" => $data,
            "Message" => $message,
        ];
        if (count($errors) > 0) {
            $response["Errors"] = $errors;
        }
        return response()->json($response, $code);
    }
}
