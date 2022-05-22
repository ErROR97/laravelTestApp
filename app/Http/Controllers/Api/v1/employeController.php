<?php


namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\ResCity;
use App\Models\City;
use App\Models\Comment;
use App\Models\Employe;
use App\Models\Log;
use App\Models\Soldier;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;



class employeesController extends apiController
{
    public function InsertEmployeData(Request $request)
    {
        $process = new Process(['python3', '/Users/error/Documents/Develop/Php/Mavara/tmsbserver/python/insertDataEmployees.py']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output_data = $process->getOutput();
        $employeesJsonArry = json_decode($output_data, TRUE);
        for ($i = 0; $i < count($employeesJsonArry); $i++) {

            $dbEmploye = Employe::where('personnel_id', $employeesJsonArry[$i]['personnel_id']);
            $dbEmploye = $dbEmploye->first();
            if ($dbEmploye) {
                $dbEmploye->national_id = $employeesJsonArry[$i]['national_id'];
                $dbEmploye->name = $employeesJsonArry[$i]['name'];
                $dbEmploye->last_name = $employeesJsonArry[$i]['last_name'];
                $dbEmploye->unit_name = $employeesJsonArry[$i]['unit'];
                $dbEmploye->company_name = $employeesJsonArry[$i]['company'];
                $dbEmploye->job_name = $employeesJsonArry[$i]['job'];
                $dbEmploye->military_rank = $employeesJsonArry[$i]['military'];
                $dbEmploye->father_name = $employeesJsonArry[$i]['father_name'];
                $dbEmploye->date_of_birth = $employeesJsonArry[$i]['date_of_birth'];
                $dbEmploye->place_of_issue = $employeesJsonArry[$i]['place_of_issue'];
                $dbEmploye->weight = $employeesJsonArry[$i]['weight'];
                $dbEmploye->height = $employeesJsonArry[$i]['height'];
                $dbEmploye->blood_type = $employeesJsonArry[$i]['blood_type'];
                $dbEmploye->bank_account_number = $employeesJsonArry[$i]['bank_account_number'];
                $dbEmploye->home_address = $employeesJsonArry[$i]['home_address'];
                $dbEmploye->access_type = $employeesJsonArry[$i]['access_type'];
                $dbEmploye->save();
            } else {
                Employe::create([
                    'personnel_id' => $employeesJsonArry[$i]['personnel_id'],
                    'national_id' => $employeesJsonArry[$i]['national_id'],
                    'name' => $employeesJsonArry[$i]['name'],
                    'last_name' => $employeesJsonArry[$i]['last_name'],
                    'unit_name' => $employeesJsonArry[$i]['unit'],
                    'company_name' => $employeesJsonArry[$i]['company'],
                    'job_name' => $employeesJsonArry[$i]['job'],
                    'military_rank' => $employeesJsonArry[$i]['military'],
                    'father_name' => $employeesJsonArry[$i]['father_name'],
                    'date_of_birth' => $employeesJsonArry[$i]['date_of_birth'],
                    'place_of_issue' => $employeesJsonArry[$i]['place_of_issue'],
                    'weight' => $employeesJsonArry[$i]['weight'],
                    'height' => $employeesJsonArry[$i]['height'],
                    'blood_type' => $employeesJsonArry[$i]['blood_type'],
                    'bank_account_number' => $employeesJsonArry[$i]['bank_account_number'],
                    'home_address' => $employeesJsonArry[$i]['home_address'],
                    'access_type' => $employeesJsonArry[$i]['access_type']
                ])->save();
            }
        }

        return $this->respondWithMessage('insert or add data', 'اطلاعات پرسنل با موفقیت اپدیت و اضافه شد');
    }

    public function getListEmploye()
    {
        $dataArry = array();

        $allEmploye = Employe::get();
        for ($i = 0; $i < count($allSoldier); $i++) {
            $comments = Comment::get()->where('personnel_id_receiver', $allEmploye[$i]['personnel_id']);
            $data = [
                'personnel_id' => $allEmploye[$i]['personnel_id'],
                'national_id' => $allEmploye[$i]['national_id'],
                'name' => $allEmploye[$i]['name'],
                'last_name' => $allEmploye[$i]['last_name'],
                'unit_name' => $allEmploye[$i]['unit_name'],
                'company_name' => $allEmploye[$i]['company_name'],
                'job_name' => $allEmploye[$i]['job_name'],
                'military_rank' => $allEmploye[$i]['military_rank'],
                'father_name' => $allEmploye[$i]['father_name'],
                'date_of_birth' => $allEmploye[$i]['date_of_birth'],
                'place_of_issue' => $allEmploye[$i]['place_of_issue'],
                'weight' => $allEmploye[$i]['weight'],
                'height' => $allEmploye[$i]['height'],
                'blood_type' => $allEmploye[$i]['blood_type'],
                'bank_account_number' => $allEmploye[$i]['bank_account_number'],
                'home_address' => $allEmploye[$i]['home_address'],
                'created_at' => $allEmploye[$i]['created_at'],
                'comments' => $comments
            ];
            array_push($dataArry, $data);
        }


        return $this->respondTrue(
            $dataArry,
            true,
            'پرسنل ها با موفقیت پیدا شدند'
        );
    }

    public function getEmploye(Request $request)
    {
        $validData = $this->validate(
            $request,
            [
                'personnel_id' => 'required'
            ]
        );
        $employe = Employe::get()->where('personnel_id', $validData['personnel_id'])->first();
        $comments = Comment::get()->where('personnel_id_receiver', $validData['personnel_id']);
        $data = [
            'personnel_id' => $employe->personnel_id,
            'national_id' => $employe->national_id,
            'name' => $employe->name,
            'last_name' => $employe->last_name,
            'unit_name' => $employe->unit_name,
            'company_name' => $employe->company_name,
            'job_name' => $employe->job_name,
            'military_rank' => $employe->military_rank,
            'father_name' => $employe->father_name,
            'date_of_birth' => $employe->date_of_birth,
            'place_of_issue' => $employe->place_of_issue,
            'weight' => $employe->weight,
            'height' => $employe->height,
            'blood_type' => $employe->blood_type,
            'bank_account_number' => $employe->bank_account_number,
            'home_address' => $employe->home_address,
            'created_at' => $employe->created_at,
            'comments' => $comments
        ];
        return $data;
    }

    public function searchEmploye(Request $request)
    {
        $result = Employe::where('personnel_id', 'like', "%$request->personnel_id%")
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
            'پرسنل با موفقیت پیدا شدند'
        );
    }

    public function addCommentEmploye(Request $request)
    {
        $validData = $this->validate(
            $request,
            [
                'personnel_id_sender' => 'required',
                'personnel_id_receiver' => 'required',
                'comment' => 'required'
            ]
        );
        $sender = Employe::get()->where('personnel_id', $validData['personnel_id_sender'])->first();
        $receiver = Employe::get()->where('personnel_id', $validData['personnel_id_receiver'])->first();

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

    public function deleteCommentEmploye(Request $request)
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
