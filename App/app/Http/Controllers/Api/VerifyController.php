<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifyController extends BaseController
{
    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email|exists:user,email'
        ]);

        if ($validator->fails()) {
            return [
                "data" => [],
                "errors" => $validator->messages()->toArray(),
                "message" => __('User/User.errors.validation'),
                "success" => false
            ];
        }

        return $this->response(
            data: (new UserResource(User::query()->where("email", $request->get('email'))->first()))->jsonSerialize(),
            message: __('User/User.success.userFound')
        );
    }
}
