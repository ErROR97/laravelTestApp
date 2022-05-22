<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Resources\v1\ResVote;
use App\Http\Resources\v1\SearchModel;
use App\Models\City;
use App\Models\Log;
use App\Models\Operator;
use App\Models\School;
use App\Models\Vote;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

class VoteController extends apiController
{
    public function addVote(Request $request)
    {
        $validData = $this->validate($request, [
                'first_name' => 'required | string',
                'last_name' => 'required | string',
                'id_school' => 'required | Integer',
                'date_entery' => 'required|string',
                'issuace_number' => 'required | Integer',
                'subject' => 'required | string',
                'date_vote' => 'required|string',
                'status' => 'required|string'
            ]
        );

        $operatorM = auth()->user()->operator()->first();

        if ($operatorM == null)
            return $this->respondSuccessMessage('error', 'خطا شما سطح دسترسی ندارید');

        if ($validData['status'] != 'show' || $validData['status'] == 'no_show')
            return $this->respondSuccessMessage('error', 'خطا وضعیت فقط می تواند فعال و غیر فعال باشد');


        $schoolM = School::where('id', $validData['id_school']);

        if ($schoolM->exists() == false)
            return $this->respondSuccessMessage('error ', 'خطا این مدرسه وجود ندارد');

        $schoolM = $schoolM->first();

        if ($operatorM->level_id < 7 && $operatorM->fk != $schoolM->id)
            return $this->respondSuccessMessage('error', 'خطا شما سطح دسترسی برای افزودن رای برای این مدرسه را ندارید');


        if (Vote::where('issuace_number', $validData['issuace_number'])->exists())
            return $this->respondSuccessMessage('error', 'این شماره رای در سیستم موجود میباشد');

        $validData['id_operator'] = $operatorM->id;

        if (isset($request->explain))
            $validData['explain'] = $request->explain;


        try
        {


            $voteM = Vote::create($validData);


            Log::create
            ([
                'action' => 'addVote',
                'id_operator' => $operatorM->id,
                'model' => 'votes',
                'fk' => $voteM->id,
                'id_pc' => $operatorM->id_pc,

            ]);

            return $this->respondWithMessage('ok', 'رای با موفقیت اضافه شد');

        } catch (Exception $e)
        {
            return $this->respondSuccessMessage('insert operator ERROR!', 'خطایی در هنگام اضافه کردن رای رخ داده است');
        }

    }

    public function updateVote(Request $request)
    {

        $validData = $this->validate($request, [
                'id' => 'required | Integer ',
                'first_name' => 'required | string',
                'last_name' => 'required | string',
                'id_school' => 'required | Integer',
                'date_entery' => 'required|string',
                'issuace_number' => 'required | Integer',
                'subject' => 'required | string',
                'date_vote' => 'required|string',
                'status' => 'required|string',
                'explain' => 'required|string',

            ]
        );


        $operatorM = auth()->user()->operator()->first();

        if ($operatorM == null)
            return $this->respondSuccessMessage('error', 'شما سطح دسترسی ندارید');


        if ($operatorM->level_id < 7 && $operatorM->fk != $validData['id_city'])
            return $this->respondSuccessMessage('error', 'شما سطح دسترسی برای تغییر این رای را ندارید');

        $voteM = Vote::where('id', $validData['id']);


        if (!$voteM->exists())
            return $this->respondSuccessMessage('its not valid!', 'این رای در سیستم موجود نمی باشد! ');

        $voteM = $voteM->first();



        $voteM->first_name = $validData['first_name'];
        $voteM->last_name = $validData['last_name'];
        $voteM->id_school = $validData['id_school'];
        $voteM->date_entery = $validData['date_entery'];
        $voteM->issuace_number = $validData['issuace_number'];
        $voteM->subject = $validData['subject'];

        $voteM->date_vote = $validData['date_vote'];
        $voteM->explain = $validData['explain'];
        $voteM->status = $validData['status'];

        $voteM->save();

        Log::create
        ([
            'action' => 'updateVote',
            'id_operator' => $operatorM->id,
            'model' => 'votes',
            'fk' => $voteM->id,
            'id_pc' => $operatorM->id_pc,

        ]);




        return $this->respondWithMessage('ok', 'اطلاعات رای با موفقیت اپدیت شد');

    }

    public function getListVote()
    {
        $operatorM = auth()->user()->operator()->first();

        if ($operatorM == null)
            return $this->respondSuccessMessage('error', 'خطا شما سطح دسترسی ندارید');


        $listVote = null;

        if ($operatorM->level_id < 7)
            $listVote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                ->where('schools.id_city', $operatorM->fk)
                ->get(['votes.*', 'schools.name as school_name', 'schools.id as school_id']);

        else
        {
            $listVote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                ->get(['votes.*', 'schools.name as school_name', 'schools.id as school_id']);
        }

        return $this->respondTrue(ResVote::collection($listVote));

    }

    public function getVote(Request $request)
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
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('votes.id', $validData['id'])
                    ->get(['votes.*', 'schools.name as school_name', 'schools.code_school']);
            }
            else
            {
                $vote = Vote::join('schools', 'schools.id', '=', 'votes.id_school')
                    ->where('votes.id', $validData['id'])
                    ->where('schools.id_city', $operator->fk)
                    ->get(['votes.*', 'schools.name as school_name', 'schools.code_school']);
            }

            if ($vote->count())
            {
                return $this->respondTrue
                (
                    $vote->first(),
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

    public function searchVote(Request $request)
    {
        $validData = $this->validate($request,
            [
                'name_column' => 'required | String ',
            ]
        );

        $operatorM = auth()->user()->operator()->first();
        $vote = null;


        if ($operatorM == null)
            return $this->respondSuccessMessage('error', 'خطا شما سطح دسترسی ندارید');


        $text = '';

        if (isset($request->text))
            $text = $request->text;


        if ($operatorM->level_id >= 7)
        {
            $voteM = Vote::join('schools', 'schools.id', '=', 'votes.id_school');

        }

        else
        {
            $voteM = Vote::join('schools', 'schools.id', '=', 'votes.id_school');

        }


        if ($validData['name_column'] == 'issuace_number')
            $voteM = $voteM->where('votes.issuace_number', 'like', "%$text%");


        else if ($validData['name_column'] == 'full_name')
            $voteM = $voteM->where('votes.first_name', 'like', "%$text%");


        else if ($validData['name_column'] == 'name_school')
            $voteM = $voteM->where('schools.name', 'like', "%$text%");

        $voteM = $voteM->get(['votes.*', 'schools.name as school_name', 'schools.id as school_id']);

        return $this->respondTrue(ResVote::collection($voteM));


    }
}
