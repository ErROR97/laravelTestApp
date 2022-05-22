<?php


namespace App\Http\Resources\v1;


use App\Models\City;
use App\Models\Level;
use Illuminate\Http\Resources\Json\JsonResource;

class ResOperator extends JsonResource
{
    public function toArray($request)
    {

     $userM = \App\Models\User::find($this->user_id);

     $levelM=Level::find($this->level_id);

     $cityM=City::find($this->fk);
        return
            [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'phone_number' => $userM->phone_number,
                'nick_name' => $this->nick_name,
                'type' => $this->type,

                'city_id'=>$cityM->id,
                'city_name'=>$cityM->name,

                'level_id' => $this->level_id,
                'level_title' => $levelM->title,
                'options'=>json_decode($levelM->options),


            ];
    }
}
