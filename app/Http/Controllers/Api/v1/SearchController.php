<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Resources\v1\getCity;
use App\Http\Resources\v1\SearchModel;
use App\Http\Resources\v1\User;
use App\Models\City;
use App\Models\Operator;
use App\Models\School;
use App\Models\Vote;
use Illuminate\Http\Request;

class SearchController extends apiController
{

    public function searchModel(Request $request)
    {
        $validData = $this->validate($request,
            [
                'table' => 'required | String ',
                'name_column' => 'required | String ',
            ]
        );

        $operatorM = auth()->user()->operator()->first();
        $table = $validData['table'];
        $text = '';
        if (isset($request->text))
            $text = $request->text;

        if (strlen($text) > 10)
            return $this->respondSuccessMessage('its not valid!', 'حداکثر طول جستجو ده می باشد');

        $validData['text'] = $text;
        $messageM['ok'] = false;
        $messageM['message'] = 'خطا اطلاعاتی پیدا نشد';
        $messageM['result'] = null;


        if ($table == 'cities')
            $messageM = $this->searchCity($validData, $operatorM);

        elseif ($table == 'votes')
            $messageM = $this->searchVote($validData, $operatorM);

        elseif ($table == 'schools')
            $messageM = $this->searchSchool($validData, $operatorM);

        elseif ($table == 'operators')
            $messageM = $this->searchOperator($validData, $operatorM);


        else
            return $this->respondSuccessMessage('its not valid!', 'این جدول در سیستم موجود نمی باشد! ');

        if ($messageM['ok'] == false)
            return $this->respondSuccessMessage('error', $messageM['message']);

        $data = SearchModel::collection($messageM['result']);

        return $this->respondTrue($data);
    }


    private function searchCity($validData, $operatorM)
    {

        $messageM['ok'] = false;
        $messageM['message'] = '';
        $messageM['result'] = null;


        if (!$this->checkOperatorAccess($operatorM, 7))
        {
            $messageM['message'] = 'کاربر گرامی شما دسترسی لازم برای این کار را ندارید!';
            return $messageM;
        }


        $text = $validData['text'];

        if ($validData['name_column'] == 'id' || $validData['name_column'] == 'name')
        {

            if ($validData['name_column'] == 'id')
                $result = City::where('id', 'like', "%$text%")->get();

            else if ($validData['name_column'] == 'name')
                $result = City::where('name', 'like', "%$text%")->get();


            $messageM['ok'] = true;
            $messageM['result'] = $result;

            return $messageM;
        }

        $messageM['message'] = 'خطا مقادیر ارسالی اشتباه می باشد';

        return $messageM;

    }

    private function searchVote($validData, $operatorM)
    {
        $vote = null;
        $messageM['ok'] = false;
        $messageM['message'] = '';
        $messageM['result'] = null;

        if (!$this->checkOperatorAccess($operatorM, 1))
        {
            $messageM['message'] = 'کاربر گرامی شما دسترسی لازم برای این کار را ندارید!';
            return $messageM;
        }


        if ($validData['name_column'] != 'id' &&
            $validData['name_column'] != 'first_name' &&
            $validData['name_column'] != 'last_name' &&
            $validData['name_column'] != 'issuace_number')
        {
            $messageM['message'] = 'مقادیر ارسالی اشتباه می باشد';
            return $messageM;
        }

        $text = $validData['text'];

        if ($operatorM->level_id >= 1 && $operatorM->level_id < 7)
        {
            if ($validData['name_column'] == 'id')
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('schools.id_city', $operatorM->fk)
                    ->where('votes.id', 'like', "%$text%")
                    ->get
                    (
                        [
                            'votes.id',
                            'votes.id_operator',
                            'votes.first_name',
                            'votes.last_name',
                            'votes.date_entery',
                            'votes.issuace_number',
                            'votes.subject',
                            'votes.explain',
                            'votes.date_vote',
                            'votes.status',
                            'schools.name as school_name',
                            'schools.code_school'
                        ]
                    );

            else if ($validData['name_column'] == 'first_name')
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('schools.id_city', $operatorM->fk)
                    ->where('votes.first_name', 'like', "%$text%")
                    ->get
                    (
                        [
                            'votes.id',
                            'votes.id_operator',
                            'votes.first_name',
                            'votes.last_name',
                            'votes.date_entery',
                            'votes.issuace_number',
                            'votes.subject',
                            'votes.explain',
                            'votes.date_vote',
                            'votes.status',
                            'schools.name as school_name',
                            'schools.code_school'
                        ]
                    );

            else if ($validData['name_column'] == 'last_name')
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('schools.id_city', $operatorM->fk)
                    ->where('votes.last_name', 'like', "%$text%")
                    ->get
                    (
                        [
                            'votes.id',
                            'votes.id_operator',
                            'votes.first_name',
                            'votes.last_name',
                            'votes.date_entery',
                            'votes.issuace_number',
                            'votes.subject',
                            'votes.explain',
                            'votes.date_vote',
                            'votes.status',
                            'schools.name as school_name',
                            'schools.code_school'
                        ]
                    );

            else if ($validData['name_column'] == 'issuace_number')
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('schools.id_city', $operatorM->fk)
                    ->where('votes.issuace_number', 'like', "%$text%")
                    ->get
                    (
                        [
                            'votes.id',
                            'votes.id_operator',
                            'votes.first_name',
                            'votes.last_name',
                            'votes.date_entery',
                            'votes.issuace_number',
                            'votes.subject',
                            'votes.explain',
                            'votes.date_vote',
                            'votes.status',
                            'schools.name as school_name',
                            'schools.code_school'
                        ]
                    );

        }

        if ($operatorM->level_id >= 7)
        {
            if ($validData['name_column'] == 'id')
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('votes.id', 'like', "%$text%")
                    ->get
                    (
                        [
                            'votes.id',
                            'votes.id_operator',
                            'votes.first_name',
                            'votes.last_name',
                            'votes.date_entery',
                            'votes.issuace_number',
                            'votes.subject',
                            'votes.explain',
                            'votes.date_vote',
                            'votes.status',
                            'schools.name as school_name',
                            'schools.code_school'
                        ]
                    );

            else if ($validData['name_column'] == 'first_name')
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('votes.first_name', 'like', "%$text%")
                    ->get
                    (
                        [
                            'votes.id',
                            'votes.id_operator',
                            'votes.first_name',
                            'votes.last_name',
                            'votes.date_entery',
                            'votes.issuace_number',
                            'votes.subject',
                            'votes.explain',
                            'votes.date_vote',
                            'votes.status',
                            'schools.name as school_name',
                            'schools.code_school'
                        ]
                    );

            else if ($validData['name_column'] == 'last_name')
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('votes.last_name', 'like', "%$text%")
                    ->get
                    (
                        [
                            'votes.id',
                            'votes.id_operator',
                            'votes.first_name',
                            'votes.last_name',
                            'votes.date_entery',
                            'votes.issuace_number',
                            'votes.subject',
                            'votes.explain',
                            'votes.date_vote',
                            'votes.status',
                            'schools.name as school_name',
                            'schools.code_school'
                        ]
                    );

            else if ($validData['name_column'] == 'issuace_number')
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('votes.issuace_number', 'like', "%$text%")
                    ->get
                    (
                        [
                            'votes.id',
                            'votes.id_operator',
                            'votes.first_name',
                            'votes.last_name',
                            'votes.date_entery',
                            'votes.issuace_number',
                            'votes.subject',
                            'votes.explain',
                            'votes.date_vote',
                            'votes.status',
                            'schools.name as school_name',
                            'schools.code_school'
                        ]
                    );
        }


        if ($vote->count())
        {
            $messageM['message'] = 'رای ها با موفقیت پیدا شدند';
            $messageM['result'] = $vote;
            $messageM['ok'] = true;
            return $messageM;
        }
        else
        {
            $messageM['message'] = 'این رای در سیستم موجود نمیباشد';
            return $messageM;
        }

    }

    private function searchSchool($validData, $operatorM)
    {
        $school = null;
        $messageM['ok'] = false;
        $messageM['message'] = '';
        $messageM['result'] = null;
        $text = $validData['text'];


        if ($validData['name_column'] != 'id' && $validData['name_column'] != 'name' && $validData['name_column'] != 'code_school')
        {
            $messageM['message'] = 'مقادیر ارسالی اشتباه می باشد';
            return $messageM;
        }


        if (!$this->checkOperatorAccess($operatorM, 1))
        {
            $messageM['message'] = 'کاربر گرامی شما دسترسی لازم برای این کار را ندارید!';
            return $messageM;
        }


        if ($operatorM->level_id == 1)
        {

            if ($validData['name_column'] == 'id')
                $school = School::join('cities', 'cities.id', '=', 'schools.id_city')
                    ->where('schools.id', 'like', "%$text%")
                    ->where('cities.id', $operatorM->fk)
                    ->get(['schools.id',
                        'schools.name',
                        'schools.code_school',
                        'schools.status',
                        'cities.name as city_name']);

            else if ($validData['name_column'] == 'name')
                $school = School::join('cities', 'cities.id', '=', 'schools.id_city')
                    ->where('schools.name', 'like', "%$text%")
                    ->where('cities.id', $operatorM->fk)
                    ->get(['schools.id',
                        'schools.name',
                        'schools.code_school',
                        'schools.status',
                        'cities.name as city_name']);

            else if ($validData['name_column'] == 'code_school')
                $school = School::join('cities', 'cities.id', '=', 'schools.id_city')
                    ->where('schools.code_school', 'like', "%$text%")
                    ->where('cities.id', $operatorM->fk)
                    ->get(['schools.id',
                        'schools.name',
                        'schools.code_school',
                        'schools.status',
                        'cities.name as city_name']);

        }


        if ($operatorM->level_id >= 7)
        {
            if ($validData['name_column'] == 'id')
                $school = School::join('cities', 'cities.id', '=', 'schools.id_city')
                    ->where('schools.id', 'like', "%$text%")
                    ->get(['schools.id',
                        'schools.name',
                        'schools.code_school',
                        'schools.status',
                        'cities.name as city_name']);

            else if ($validData['name_column'] == 'name')
                $school = School::join('cities', 'cities.id', '=', 'schools.id_city')
                    ->where('schools.name', 'like', "%$text%")
                    ->get(['schools.id',
                        'schools.name',
                        'schools.code_school',
                        'schools.status',
                        'cities.name as city_name']);

            else if ($validData['name_column'] == 'code_school')
                $school = School::join('cities', 'cities.id', '=', 'schools.id_city')
                    ->where('schools.code_school', 'like', "%$text%")
                    ->get(['schools.id',
                        'schools.name',
                        'schools.code_school',
                        'schools.status',
                        'cities.name as city_name']);

        }


        if ($school->count())
        {
            $messageM['message'] = 'مدرسه ها با موفقیت پیدا شدند';
            $messageM['result'] = $school;
            $messageM['ok'] = true;
            return $messageM;
        }
        else
        {
            $messageM['message'] = 'این مدرسه در سیستم موجود نمیباشد';
            return $messageM;
        }

    }

    private function searchOperator($validData, $operatorM)
    {
        $user = null;
        $messageM['ok'] = false;
        $messageM['message'] = '';
        $messageM['result'] = null;
        $text = $validData['text'];

        if (!$this->checkOperatorAccess($operatorM, 7))
        {
            $messageM['message'] = 'کاربر گرامی شما دسترسی لازم برای این کار را ندارید!';
            return $messageM;
        }

        if ($validData['name_column'] != 'id' && $validData['name_column'] != 'nick_name')
        {
            $messageM['message'] = 'مقادیر ارسالی اشتباه می باشد';
            return $messageM;
        }

        if ($validData['name_column'] == 'id')
            $user = Operator::where('id', 'like', "%$text%")->get
            (
                [
                    'id',
                    'nick_name',
                    'user_id',
                    'level_id',
                    'id_pc',
                    'model',
                    'status',
                    'fk'
                ]
            );


        else if ($validData['name_column'] == 'nick_name')
            $user = Operator::where('nick_name', 'like', "%$text%")->get
            (
                [
                    'id',
                    'nick_name',
                    'user_id',
                    'level_id',
                    'id_pc',
                    'model',
                    'status',
                    'fk'
                ]
            );

        if ($user->count())
        {
            $messageM['message'] = 'اپراتور ها با موفقیت پیدا شدند';
            $messageM['result'] = $user;
            $messageM['ok'] = true;
            return $messageM;
        }
        else
        {
            $messageM['message'] = 'این اپراتور در سیستم موجود نمیباشد';
            return $messageM;
        }

    }

    public function searchUser(Request $request)
    {
        $validData = $this->validate($request,
            [
                'name_column' => 'required | String ',
            ]
        );

        $operatorM = auth()->user()->operator()->first();

        if (!$this->checkOperatorAccess($operatorM, 7))
            return $this->respondSuccessMessage('error', 'کاربر گرامی شما دسترسی لازم برای این کار را ندارید ! ');

        $text = "";

        if (isset($request->text))
            $text= $request->text;

        if (strlen($text) > 10)
            return $this->respondSuccessMessage('its not valid!', 'حداکثر طول جستجو ده می باشد');


        if ($validData['name_column'] != 'phone_number' && $validData['name_column'] != 'last_name')
            return $this->respondSuccessMessage('error', 'مقادیر ارسالی اشتباه می باشد');


        if ($validData['name_column'] == 'phone_number')
            $userM = \App\Models\User::where('phone_number', 'like', "%$text%");


        else if ($validData['name_column'] == 'last_name')
            $userM = \App\Models\User::where('last_name', 'like', "%$text%");



        $userM = $userM->get
        (
            [
                'id',
                'phone_number',
                'first_name',
                'last_name',
                'username',
                'status'
            ]
        );

        return $this->respondTrue($userM);


    }

}
