<?php

namespace App\Http\Resources\v1;

use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class ResSchool extends JsonResource
{

    public function toArray($request)
    {


        $cityM = City::find($this->id_city);

        return
            [
                'id' => $this->id,
                'name_school' => $this->name,
                'code' => $this->code_school,
                'status' => $this->status,
                'name_city' => $cityM->name,
                'id_city' => $cityM->id,

            ];
    }

}
