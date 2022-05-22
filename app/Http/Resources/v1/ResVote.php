<?php


namespace App\Http\Resources\v1;


use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;

class ResVote extends JsonResource
{


    public function toArray($request)
    {
        return
            [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'date_vote' => $this->date_vote,
                'date_entery' => $this->date_entery,
                'school_name' => $this->school_name,
                'id_school' => $this->id_school,
                'issuace_number' => $this->issuace_number,
                'subject' => $this->subject,
                'explain' => $this->explain,
                'status' => $this->status
            ];
    }

}
