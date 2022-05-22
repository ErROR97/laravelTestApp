<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class ResSoldier extends JsonResource
{

    public function toArray($request)
    {
        return
            [
                'id' => $this->id,
                'name' => $this->name,
                'status' => $this->status,

            ];
    }

}
