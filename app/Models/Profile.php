<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = [];

    public function getElementsAttribute($value)
    {
        if($value != null) {

            $value = unserialize($value) ;
            $this->e1 = $value['e1'] ;

        }

        return $value;
    }

    public function getEleBodyAttribute()
    {
        $value = -1 ;
        if(isset($this->elements))
        {

            $value = $this->elements['e1'] ;
        }

        return $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
