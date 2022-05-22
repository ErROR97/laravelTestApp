<?php


namespace App\Http\Controllers\Api\v1;


use App\Http\Resources\v1\ResCity;
use App\Models\City;
use App\Models\Comment;
use App\Models\Log;
use App\Models\Soldier;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class SoldierController extends apiController
{
    public function InsertSoldierData(Request $request)
    {
        $process = new Process(['python3', '/Users/error/Documents/Develop/Php/Mavara/tmsbserver/python/insertData.py']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output_data = $process->getOutput();
        $soldiersJsonArry = json_decode($output_data, TRUE);
        for ($i = 0; $i < count($soldiersJsonArry); $i++) {

            $dbSoldier = Soldier::where('personnel_id', $soldiersJsonArry[$i]['personnel_id']);
            $dbSoldier = $dbSoldier->first();
            if ($dbSoldier) {
                $dbSoldier->national_id = $soldiersJsonArry[$i]['national_id'];
                $dbSoldier->name = $soldiersJsonArry[$i]['name'];
                $dbSoldier->last_name = $soldiersJsonArry[$i]['last_name'];
                $dbSoldier->unit_name = $soldiersJsonArry[$i]['unit'];
                $dbSoldier->company_name = $soldiersJsonArry[$i]['company'];
                $dbSoldier->job_name = $soldiersJsonArry[$i]['job'];
                $dbSoldier->military_rank = $soldiersJsonArry[$i]['military'];
                $dbSoldier->father_name = $soldiersJsonArry[$i]['father_name'];
                $dbSoldier->date_of_birth = $soldiersJsonArry[$i]['date_of_birth'];
                $dbSoldier->place_of_issue = $soldiersJsonArry[$i]['place_of_issue'];
                $dbSoldier->weight = $soldiersJsonArry[$i]['weight'];
                $dbSoldier->height = $soldiersJsonArry[$i]['height'];
                $dbSoldier->blood_type = $soldiersJsonArry[$i]['blood_type'];
                $dbSoldier->bank_account_number = $soldiersJsonArry[$i]['bank_account_number'];
                $dbSoldier->home_address = $soldiersJsonArry[$i]['home_address'];
                $dbSoldier->save();

                // $sender = Comment::get()->where('personnel_id_sender', $soldiersJsonArry[$i]['personnel_id'])->first();
                // $receiver = Comment::get()->where('personnel_id_receiver', $soldiersJsonArry[$i]['personnel_id'])->first();
                // if ($sender) {
                //     $comment = Comment::where('personnel_id_sender',$soldiersJsonArry[$i]['personnel_id']);
                //     $comment->personnel_id_sender = $soldiersJsonArry[$i]['personnel_id'];
                //     $comment->save();
                // } else if ($receiver) {
                //     $comment = Comment::where('personnel_id_receiver',$soldiersJsonArry[$i]['personnel_id']);
                //     $comment->personnel_id_sender = $soldiersJsonArry[$i]['personnel_id_receiver'];
                //     $comment->save();
                // }
            } else {
                Soldier::create([
                    'personnel_id' => $soldiersJsonArry[$i]['personnel_id'],
                    'national_id' => $soldiersJsonArry[$i]['national_id'],
                    'name' => $soldiersJsonArry[$i]['name'],
                    'last_name' => $soldiersJsonArry[$i]['last_name'],
                    'unit_name' => $soldiersJsonArry[$i]['unit'],
                    'company_name' => $soldiersJsonArry[$i]['company'],
                    'job_name' => $soldiersJsonArry[$i]['job'],
                    'military_rank' => $soldiersJsonArry[$i]['military'],
                    'father_name' => $soldiersJsonArry[$i]['father_name'],
                    'date_of_birth' => $soldiersJsonArry[$i]['date_of_birth'],
                    'place_of_issue' => $soldiersJsonArry[$i]['place_of_issue'],
                    'weight' => $soldiersJsonArry[$i]['weight'],
                    'height' => $soldiersJsonArry[$i]['height'],
                    'blood_type' => $soldiersJsonArry[$i]['blood_type'],
                    'bank_account_number' => $soldiersJsonArry[$i]['bank_account_number'],
                    'home_address' => $soldiersJsonArry[$i]['home_address'],
                ])->save();
            }
        }

        return $this->respondWithMessage('insert or add data', 'اطلاعات سربازان با موفقیت اپدیت و اضافه شد');
    }

    public function getListSoldier()
    {
        $dataArry = array();

        $allSoldier = Soldier::get();
        for ($i = 0; $i < count($allSoldier); $i++) {
            $comments = Comment::get()->where('personnel_id_receiver', $allSoldier[$i]['personnel_id']);
            $data = [
                'personnel_id' => $allSoldier[$i]['personnel_id'],
                'national_id' => $allSoldier[$i]['national_id'],
                'name' => $allSoldier[$i]['name'],
                'last_name' => $allSoldier[$i]['last_name'],
                'unit_name' => $allSoldier[$i]['unit_name'],
                'company_name' => $allSoldier[$i]['company_name'],
                'job_name' => $allSoldier[$i]['job_name'],
                'military_rank' => $allSoldier[$i]['military_rank'],
                'father_name' => $allSoldier[$i]['father_name'],
                'date_of_birth' => $allSoldier[$i]['date_of_birth'],
                'place_of_issue' => $allSoldier[$i]['place_of_issue'],
                'weight' => $allSoldier[$i]['weight'],
                'height' => $allSoldier[$i]['height'],
                'blood_type' => $allSoldier[$i]['blood_type'],
                'bank_account_number' => $allSoldier[$i]['bank_account_number'],
                'home_address' => $allSoldier[$i]['home_address'],
                'created_at' => $allSoldier[$i]['created_at'],
                'comments' => $comments
            ];
            array_push($dataArry, $data);
        }


        return $this->respondTrue(
            $dataArry,
            true,
            'سرباز ها با موفقیت پیدا شدند'
        );
    }

    public function getSoldier(Request $request)
    {
        $validData = $this->validate(
            $request,
            [
                'personnel_id' => 'required'
            ]
        );
        $soldier = Soldier::get()->where('personnel_id', $validData['personnel_id'])->first();
        $comments = Comment::get()->where('personnel_id_receiver', $validData['personnel_id']);
        $data = [
            'personnel_id' => $soldier->personnel_id,
            'national_id' => $soldier->national_id,
            'name' => $soldier->name,
            'last_name' => $soldier->last_name,
            'unit_name' => $soldier->unit_name,
            'company_name' => $soldier->company_name,
            'job_name' => $soldier->job_name,
            'military_rank' => $soldier->military_rank,
            'father_name' => $soldier->father_name,
            'date_of_birth' => $soldier->date_of_birth,
            'place_of_issue' => $soldier->place_of_issue,
            'weight' => $soldier->weight,
            'height' => $soldier->height,
            'blood_type' => $soldier->blood_type,
            'bank_account_number' => $soldier->bank_account_number,
            'home_address' => $soldier->home_address,
            'created_at' => $soldier->created_at,
            'comments' => $comments
        ];
        return $data;
    }

    public function searchSoldier(Request $request)
    {
        $result = Soldier::where('personnel_id', 'like', "%$request->personnel_id%")
            ->where('national_id', 'like', "%$request->national_id%")
            ->where('name', 'like', "%$request->name%")
            ->where('last_name', 'like', "%$request->last_name%")
            ->where('father_name', 'like', "%$request->father_name%")
            ->where('weight', 'like', "%$request->weight%")
            ->where('height', 'like', "%$request->height%")
            ->where('blood_type', 'like', "%$request->blood_type%")
            ->where('unit_name', 'like', "%$request->unit_name%")
            ->where('company_name', 'like', "%$request->company_name%")
            ->where('date_of_birth', 'like', "%$request->date_of_birth%")
            ->where('place_of_issue', 'like', "%$request->place_of_issue%")
            ->get();

        return $this->respondTrue(
            $result,
            true,
            'سرباز ها با موفقیت پیدا شدند'
        );
    }

    public function addCommentSoldier(Request $request)
    {
        $validData = $this->validate(
            $request,
            [
                'personnel_id_sender' => 'required',
                'personnel_id_receiver' => 'required',
                'comment' => 'required'
            ]
        );
        $sender = Soldier::get()->where('personnel_id', $validData['personnel_id_sender'])->first();
        $receiver = Soldier::get()->where('personnel_id', $validData['personnel_id_receiver'])->first();

        if ($sender) {
            if ($receiver) {
                Comment::create([
                    'personnel_id_sender' => $validData['personnel_id_sender'],
                    'personnel_id_receiver' => $validData['personnel_id_receiver'],
                    'comment' => $validData['comment']
                ])->save();
                return $this->respondWithMessage('insert or add data', 'دیدگاه با موفقیت اضافه شد');
            }
        }
        return $this->respondSuccessMessage('error', 'خطا شماره پرسنلی را کنترل کنید');
    }

    public function deleteCommentSoldier(Request $request)
    {
        $validData = $this->validate(
            $request,
            [
                'id' => 'required'
            ]
        );

        if (Comment::where('id', $validData['id'])->delete())
            return $this->respondWithMessage('delete data', 'دیدگاه با موفقیت حذف شد');

        return $this->respondSuccessMessage('error', 'خطا شماره کامنت را کنترل کنید');
    }
}
