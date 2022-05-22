<?php

namespace App\Http\Resources\v1;

use App\Models\Profile;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    public $token;

    public function __construct($resource, $token = null)
    {
        $this->token = $token;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'first_name' => $this->first_name ,
            'last_name' =>  $this->last_name,
            'phone_number' => $this->phone_number,
            'username' => $this->username,
            'image' => $this->image,
            'status' => $this->status,
            'api_token' => $this->token,

        ];
    }

    public function with($request)
    {
        return [
            'status' => 'success'
        ];
    }
}
