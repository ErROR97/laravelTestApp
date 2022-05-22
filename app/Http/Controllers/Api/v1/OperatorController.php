<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Resources\v1\ResCity;
use App\Http\Resources\v1\ResOperator;
use App\Models\City;
use App\Models\Operator;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class OperatorController extends apiController
{

    public function addOperator(Request $request)
    {
        $validData = $this->validate($request, [
                'user_id' => 'required | Integer ',
                'nick_name' => 'required | string',
                'level_id' => 'required | Integer',
                'city_id' => 'required | Integer'
            ]
        );

        $operator = auth()->user()->operator()->first();

        if (!$this->checkOperatorAccess($operator, 7))
            return $this->respondSuccessMessage('its not valid!', 'شما سطح دسترسی ندارید! ');


        if (!User::where('id', $validData['user_id'])->exists())
            return $this->respondSuccessMessage('its not valid!', 'این نام کاربری در سیستم موجود نمیباشد');

        if (Operator::where('user_id', $validData['user_id'])->exists())
            return $this->respondSuccessMessage('این اپراتور در سیستم موجود میباشد');


        try
        {

            Operator::create
            ([
                'user_id' => $validData['user_id'],
                'nick_name' => $validData['nick_name'],
                'level_id' => $validData['level_id'],
                'fk' => $validData['city_id'],
            ])->save();
            return $this->respondWithMessage('ok', 'اپراتور با موفقیت اضافه شد');

        } catch (Exception $e)
        {
            return $this->respondSuccessMessage('insert operator ERROR!', 'خطایی در هنگام اضافه کردن اپراتور رخ داده است');
        }


    }

    public function updateOperator(Request $request)
    {
        $validData = $this->validate($request,
            [
                'id' => 'required | Integer '
            ]
        );
        $operator = Operator::where('id', $validData['id']);

        if (!$this->checkOperatorAccess($operator, 7))
            return $this->respondSuccessMessage('its not valid!', 'شما سطح دسترسی ندارید! ');


        if (!$operator->exists())
            return $this->respondSuccessMessage('its not valid!', 'این کاربر در سیستم موجود نمی باشد! ');


        else
        {
            $operator = $operator->first();

            if (isset($request->nick_name)) $operator->nick_name = $request->nick_name;
            if (isset($request->level_id)) $operator->level_id = $request->level_id;
            if (isset($request->model)) $operator->model = $request->model;
            if (isset($request->fk)) $operator->fk = $request->fk;
            if (isset($request->more)) $operator->more = $request->more;
            if (isset($request->mac_address)) $operator->mac_address = $request->mac_address;
            if (isset($request->device_model)) $operator->device_model = $request->device_model;
            if (isset($request->version_install)) $operator->version_install = $request->version_install;
            if (isset($request->status)) $operator->status = $request->status;
            if (isset($request->type)) $operator->type = $request->type;

            $operator->save();
            return $this->respondCreated('اپراتور با موفقیت اپدیت شد', 201, 'info updated');

        }

    }

    public function getListOperator()
    {
        $operator = auth()->user()->operator()->first();
        if (!$this->checkOperatorAccess($operator, 7))
            return $this->respondSuccessMessage('its not valid!', 'شما سطح دسترسی ندارید! ');

        return $this->respondTrue
        (
            ResOperator::collection(Operator::get()),
            true,
            'اپراتور ها با موفقیت پیدا شدند'
        );
    }

    public function getOperator(Request $request)
    {
        $validData = $this->validate($request,
            [
                'id' => 'required | Integer '
            ]
        );

        if (!$this->checkOperatorAccess(auth()->user()->operator()->first(), 7))
            return $this->respondSuccessMessage('its not valid!', 'شما سطح دسترسی ندارید! ');

        $operator = Operator::where('id', $validData['id']);
        if ($operator->exists())
        {

            return $this->respondTrue
            (
                new ResOperator($operator->first()),
                true,
                'کاربر با موفقیت پیدا شد'
            );
        }
        else
        {
            return $this->respondSuccessMessage
            (
                'its not valid!',
                'این کاربر در سیستم موجود نمی باشد! '
            );
        }


    }

    public function searchOperator(Request $request)
    {
        $validData = $this->validate($request,
            [
                'name_column' => 'required | String ',
            ]
        );


        $operator = auth()->user()->operator()->first();

        if (!$this->checkOperatorAccess($operator, 7))
            return $this->respondSuccessMessage('its not valid!', 'شما سطح دسترسی ندارید! ');


        $text = '';
        if (isset($request->text))
            $text = $request->text;

        if (strlen($text) > 10)
            return $this->respondSuccessMessage('its not valid!', 'حداکثر طول جستجو ده می باشد');


        $result = null;

        if ($validData['name_column'] == 'id')
            $result = Operator::where('id', 'like', "%$text%")->get();

        else if ($validData['name_column'] == 'phone_number')
            $result = Operator::join('users', 'users.id', '=', 'operators.user_id')
                ->where('users.phone_number', 'like', "%$text%")
                ->get(['operators.*', 'users.phone_number']);


        return $this->respondTrue(ResOperator::collection($result));

    }

}
