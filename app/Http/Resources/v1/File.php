<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class File extends JsonResource
{

    public function toArray($request)
    {


        return [
            'id' => $this->id,
            'image' => $this->url,
            'model' => $this->model,

        ];
    }

}
