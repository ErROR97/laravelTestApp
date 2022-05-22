<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Resources\v1\ResLevel;
use App\Models\Level;

class LevelController extends apiController
{
    public function getListLevel()
    {
        $operator = auth()->user()->operator()->first();

        if ($this->checkOperatorAccess($operator, 7))
        {
            return $this->respondTrue
            (
                ResLevel::collection(Level::get()),
                true,
                'لول ها با موفقیت پیدا شدند'
            );
        }
        else
            return $this->respondSuccessMessage('شما سطح دسترسی ندارید');
    }
}
