<?php


namespace App\Http\Resources\v1;


use Illuminate\Http\Resources\Json\JsonResource;

class SearchModel extends JsonResource
{
    public function __construct($resource)
    {
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

        $model='city';
        $title='?';


        if(isset($this->code_school))
        {
            $model='school';
            $title=$this->name;
        }

        if(isset($this->issuace_number))
        {
            $model = 'vote';
            $title = $this->first_name.' '.$this->last_name;
        }

        if(isset($this->nick_name))
        {
            $model = 'operator';
            $title = $this->nick_name;
        }

       else if(isset($this->name))
            $title=$this->name;


        return
            [
                'id' => $this->id,
                'name' => $title,
                'status' => $this->status,
                'model' => $model
            ];
    }

}
