<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Resources\v1\ResSchool;
use App\Models\City;
use App\Models\Log;
use App\Models\School;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class SchoolController extends apiController
{
    public function addSchool(Request $request)
    {
        $validData = $this->validate($request, [
                'name' => 'required | string ',
                'code_school' => 'required | Integer',
                'id_city' => 'required | Integer',
                'status' => 'Integer'
            ]
        );
        $operatorM = auth()->user()->operator()->first();

        if ($operatorM->level_id < 7 && $operatorM->fk != $validData['id_city'])
            return $this->respondSuccessMessage('خطا شما به اطلاعات این شهر دسترسی ندارید');

        if (City::where('id', $validData['id_city'])->exists() == false)
            return $this->respondSuccessMessage('این شهر در سیستم موجود نمی باشد');


        $schoolM = School::where('name', $validData['name'])
            ->where('id_city', $validData['id_city']);

        if ($schoolM->exists())
            return $this->respondSuccessMessage('error', 'خطا این مدرسه از قبل در این  شهر ثت شده است');


        if (School::where('code_school', $validData['code_school'])->exists())
            return $this->respondSuccessMessage('error', 'این کد مدرسه در سیستم موجود میباشد');


        try
        {

            $school = School::create
            ([
                'name' => $validData['name'],
                'code_school' => $validData['code_school'],
                'id_city' => $validData['id_city'],
                'status' => $validData['status']
            ]);

            Log::create
            ([
                'action' => 'addSchool',
                'id_operator' => $operatorM->id,
                'model' => 'schools',
                'fk' => $school->id,
                'id_pc' => $operatorM->id_pc,

            ]);

            return $this->respondWithMessage('ok', 'مدرسه با موفقیت اضافه شد');


        } catch (Exception $e)
        {
            return $this->respondSuccessMessage('insert operator ERROR!', 'خطایی در هنگام اضافه کردن مدرسه رخ داده است');
        }


    }

    public function updateSchool(Request $request)
    {
        $validData = $this->validate($request,
            [
                'id' => 'required | Integer ',
                'name' => 'required | string ',
                'code_school' => 'required | Integer',
                'id_city' => 'required | Integer',
                'status' => 'Integer'
            ]
        );

        $operatorM = auth()->user()->operator()->first();


        $schoolM = School::where('id', $validData['id']);

        if ($schoolM->exists() == false)
            return $this->respondSuccessMessage('error', 'خطا این مدرسه وجود ندارد');

        $schoolM = $schoolM->first();

        if ($operatorM->level_id < 7 && $schoolM->id_city != $operatorM->fk)
            return $this->respondSuccessMessage('error', 'خطا شما سطح دسترسی برای تغیییر این مدرسه را ندارید');


        $schoolM->name = $request->name;
        $schoolM->code_school = $request->code_school;
        $schoolM->id_city = $request->id_city;
        $schoolM->status = $request->status;

        $schoolM->save();

        Log::create
        ([
            'action' => 'updateSchool',
            'id_operator' => $operatorM->id,
            'model' => 'schools',
            'fk' => $schoolM->id,
            'id_pc' => $operatorM->id_pc,

        ]);


        return $this->respondWithMessage('ok', 'اطلاعات مدرسه با موفقیت اپدیت شد');

    }

    public function getListSchool()
    {
        $operatorM = auth()->user()->operator()->first();

        $cityM = School::get();
        if ($operatorM->level_id < 7)
            $cityM->where('id_city', $operatorM->fk);


        return $this->respondTrue(ResSchool::collection($cityM));

    }

    public function getSchool(Request $request)
    {
        $validData = $this->validate($request,
            [
                'id' => 'required | Integer '
            ]
        );
        $operator = auth()->user()->operator()->first();

        if ($this->checkOperatorAccess($operator, 1))
        {
            if ($this->checkOperatorAccess($operator, 7))
            {
                $school = School::join('cities', 'cities.id', '=', 'schools.id_city')
                    ->where('schools.id', $validData['id'])
                    ->get(['schools.*', 'cities.name as city_name']);
            }
            else
            {
                $school = School::join('cities', 'cities.id', '=', 'schools.id_city')
                    ->where('schools.id', $validData['id'])
                    ->where('cities.id', $operator->fk)
                    ->get(['schools.*', 'cities.name as city_name']);
            }

            if ($school->count())
            {
                return $this->respondTrue
                (
                    $school->first(),
                    true,
                    'مدرسه با موفقیت پیدا شد'
                );
            }
            else
            {
                return $this->respondSuccessMessage
                (
                    'its not valid!',
                    'این مدرسه در سیستم موجود نمی باشد! '
                );
            }
        }
        else
        {
            return $this->respondSuccessMessage
            (
                'its not valid!',
                'کاربر گرامی شما دسترسی لازم برای این کار را ندارید! '
            );
        }
    }


    public function searchSchool(Request $request)
    {
        $validData = $this->validate($request,
            [
                'name_column' => 'required | String ',
            ]
        );


        $operatorM = auth()->user()->operator()->first();


        $text = '';
        if (isset($request->text))
            $text = $request->text;

        if (strlen($text) > 10)
            return $this->respondSuccessMessage('its not valid!', 'حداکثر طول جستجو ده می باشد');


        $result = [];

        if ($validData['name_column'] == 'id')
            $result = School::where('id', 'like', "%$text%")->get();

        else if ($validData['name_column'] == 'name_school')
            $result = School::where('name', 'like', "%$text%")->get();

        else if ($validData['name_column'] == 'code_school')
            $result = School::where('code_school', 'like', "%$text%")->get();


        if ($result != [] && $operatorM->level_id < 7)
            $result = $result->where('city_id', $operatorM->fk);


        return $this->respondTrue(ResSchool::collection($result));

    }

}
