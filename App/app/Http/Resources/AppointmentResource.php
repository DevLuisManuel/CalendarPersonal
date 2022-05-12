<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class AppointmentResource extends JsonResource
{
    public static $wrap = false;

    #[ArrayShape(['id' => "mixed", 'appointmentDate' => "mixed", 'user' => "\App\Http\Resources\UserResource"])] public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'appointmentDate' => (new Carbon($this->appointmentDate))->format('Y-m-d H:i'),
            'user' => (new UserResource($this->user)),
        ];
    }
}
