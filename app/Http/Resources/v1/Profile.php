<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class Profile extends JsonResource
{

    public function toArray($request)
    {

        $date = Jalalian::forge($this->created_at)->ago();
        $tmpDate = explode(' ', $date);
        if ($tmpDate[1] == 'هفته' || $tmpDate[1] == 'ماه')
            $date = Jalalian::forge($this->created_at)->format('Y/m/d');
        return [
            'id' => $this->id,
            'image' => $this->image,
            'title' => $this->title,
            'explain' => $this->body,
            'date' => $date,
        ];
    }

}
