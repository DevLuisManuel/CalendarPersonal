<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class UserResource extends JsonResource
{
    #[ArrayShape(['id' => "mixed", 'name' => "\Illuminate\Support\Stringable", 'email' => "mixed"])] public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => Str::of($this->name)->ucfirst(),
            'email' => $this->email,
        ];
    }
}
