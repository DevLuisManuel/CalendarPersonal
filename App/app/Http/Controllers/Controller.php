<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\{Auth\Access\AuthorizesRequests, Bus\DispatchesJobs, Validation\ValidatesRequests};
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0",
 *      title="Calendar API Documentation",
 *      description="Personal calendar, developed by Engineer Luis Manuel Zuñiga Moreno",
 *      @OA\Contact(
 *          email="ing.luiszuniga@dvloper.com.co"
 *      ),
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Api Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
