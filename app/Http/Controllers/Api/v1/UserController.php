<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\ResOperator;
use App\Models\City;
use App\Models\Level;
use App\Models\Operator;
use App\Models\Pc;
use App\Models\TmpRegister;
use App\Models\User;

use App\TMAccount;
use App\UserServices;
use Illuminate\Http\Request;
use App\Http\Resources\v1\User as UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Kavenegar\KavenegarApi;


class UserController extends apiController
{

    public function checkPhoneNumber($validData)
    {

        $returnValue =
            [
                'ok' => false,
                'message' => 'خطا موردی پیدا نشد',
            ];

        $verifyCode = rand(100000, 999999);//کد رند در دیتابیس ذخیره شود
        $phoneNumber = $this->convertFaNumToEN($validData['phone_number']);

        $listCode = TmpRegister::where('phone_number', $phoneNumber)
            ->where('status', ' == ', 0)->get();


        foreach ($listCode as $item)
        {
            $item->status = -1;
            $item->explain = 'expire by server';
            $item->save();
        }

        $kavenegar = new KavenegarApi('');//TODO Add My Token
        $result = $kavenegar->VerifyLookup($phoneNumber, $verifyCode, null, null, 'usbplus');

        $resultArray = $result[0];
        $status = $resultArray->status;
//        $status = 5;

        if ($status == 5)//پیام با موفقیت ارسال شده است
        {
            TmpRegister::create(
                [
                    'phone_number' => $phoneNumber,
                    'code' => $verifyCode,
                    'type' => $validData['type'],
                    'explain' => 'code send to user ',
                ]);
            $returnValue['ok'] = true;
            $returnValue['message'] = 'لطفا کد ارسال شده به شماره همراه خود را وارد کنید';
            return $returnValue;
        }

        $returnValue['message'] = 'مشکل ارسال پیامک در زمانی دیگر دوباره تلاش کنید یا موضوع رو به پشتیبانی اطلاع دهید';

        return $returnValue;

    }

    public function verifyCode(Request $request)
    {
        $validData = $this->validate($request, [
                'phone_number' => 'required | string | size:11',
                'code' => 'required | string',
                'type' => 'required|string'
            ]
        );

        $rModel = TmpRegister::where('phone_number', $validData['phone_number'])
            ->where('type', $validData['type'])
            ->where('status', 0)
            ->where('code', $validData['code'])
            ->latest('created_at');


        if (!$rModel->exists())
        {
            return $this->respondTrue
            (
                [
                    'commend' => 'error',
                    'user' => null,
                ],
                false,
                'خطا کد وارد شده صحیح نمی باشد'
            );
        }

        $rModel = $rModel->first();


        $dateCreateCode = date('Y-m-d H:i:s', strtotime($rModel->created_at . ' +3 min'));
        $dateNew = date_format(new \DateTime(), 'Y-m-d H:i:s');


        // در صورت منقضی شدن کد ارسال شده
        if ($dateNew > $dateCreateCode)
        {
            $rModel->type = 'expire time';
            $rModel->status = -1;
            $rModel->save();

            $rSendCode = $this->checkPhoneNumber($validData);
            if ($rSendCode['ok'])
            {
                return $this->respondTrue
                (
                    [
                        'commend' => 'expire_time',
                        'user' => null,
                    ],
                    true,
                    'کد وارد شده منقضی شده است . کد جدید به شماره همراه شما ارسال شد'
                );
            }

            else
            {
                $message = 'کد وارد شده منقضی شده است . خطا در ارسال کد جدید لطفا دوباره تلاش کنید یا با پشتیان سامانه تماس بگیرید';
                return $this->respondTrue
                (
                    [
                        'commend' => 'error',
                        'user' => null,
                    ],
                    false,
                    $message

                );
            }

        }


        if ($validData['type'] == 'register')
        {
            $message = "لطفا برای تکمیل ثبت نام مشخصات خود را وارد کنید.";
            $rModel->status = 1;
            $rModel->explain = "ver->register";
            $rModel->save();
            return $this->respondTrue
            (
                [
                    'commend' => 'view_register',
                    'user' => null,
                ],
                true,
                $message
            );
        }


        $userM = User::where('phone_number', $validData['phone_number'])->first();
        Auth::loginUsingId($userM->id);

        $androidId = null;
        if (isset($request['android_id']))
            $androidId = $request['android_id'];
        $user = $this->loginGetToken($androidId);


        if ($validData['type'] == 'reset_pass')
        {
            $pass = rand(111111, 999999);

            $userM->password = bcrypt($pass);
            $userM->phone_number = $this->convertFaNumToEN($userM->phone_number);
            $userM->save();
            $kavenegar = new KavenegarApi('');//TODO ADD MY APP
//            $mTTS = "رمز جدید شما: " . $pass . "\nشبکه اجتماعی دانشگاه س و ب ";
            $receptor = $userM->phone_number;
            $kavenegar->VerifyLookup($receptor, $pass, null, null, 'newpass');

            $rModel->status = 2;
            $rModel->explain = "reset_pass";
            $rModel->save();

            $message = 'رمز شما با موفقیت تغییر و از طریق پیامک ارسال شد ';

            return $this->respondTrue
            (
                [
                    'commend' => 'view_check_exists_user',
                    'user' => null
                ]
                ,
                true,
                $message
            );
        }


        else if ($validData['type'] == 'login_by_code')
        {
            $rModel->status = 2;
            $rModel->explain = "login_by_code";
            $rModel->save();

            return $this->respondTrue
            (
                [
                    'commend' => 'view_home',
                    'user' => $user
                ]
                ,
                true,
                'به اپلیکیشن جی بار خوش آمدید'
            );
        }


        return $this->respondTrue
        (
            [
                'commend' => 'no_find',
                'user' => null,
            ],
            false,
            'خطا دستور وارد شده پیدا نشد'
        );

    }

    public function sendCode(Request $request)
    {
        $validData = $this->validate($request,
            [
                'phone_number' => 'required | string | size:11',
                'type' => 'required | string',
            ]
        );

        if ($validData['type'] == 'register')
        {
            $userM = User::where('phone_number', $validData['phone_number']);
            if ($userM->exists())
            {
                return $this->respondTrue
                (
                    [
                        'commend' => 'error',
                        'user' => null,
                    ],
                    false,
                    'خطا این شماره تماس از قبل در سیستم ثبت شده است'
                );
            }

            $mSendCode = $this->checkPhoneNumber($validData);

            if ($mSendCode['ok'])
            {
                return $this->respondTrue
                (
                    [
                        'commend' => 'verify_register',
                        'length_code' => 6
                    ]
                    ,
                    true,
                    $mSendCode['message']
                );

            }
            else
                return $this->respondWithError('خطا در ارسال پیام', $mSendCode['message']);


        }

        if ($validData['type'] == 'reset_pass')
        {
            $userM = User::where('phone_number', $validData['phone_number']);

            if (!$userM->exists())
                return $this->respondWithError('error', ' خطا این شماره تماس در سیستم ثبت نشده است  ');


            $mSendCode = $this->checkPhoneNumber($validData);

            if ($mSendCode['ok'])
            {
                return $this->respondTrue
                (
                    [
                        'commend' => 'verify_reset_pass',
                        'length_code' => 6
                    ]
                    ,
                    true,
                    $mSendCode['message']
                );
            }
            else
                return $this->respondWithError('خطا در ارسال پیام', $mSendCode['message']);
        }

        if ($validData['type'] == 'login_by_code')
        {
            $userM = User::where('phone_number', $validData['phone_number']);

            if (!$userM->exists())
                return $this->respondWithError('error', ' خطا این شماره تماس در سیستم ثبت نشده است  ');


            $mSendCode = $this->checkPhoneNumber($validData);

            if ($mSendCode['ok'])
            {
                return $this->respondTrue
                (
                    [
                        'commend' => 'verify_login_by_code',
                        'length_code' => 6
                    ]
                    ,
                    true,
                    $mSendCode['message']
                );
            }
            else
                return $this->respondWithError('خطا در ارسال پیام', $mSendCode['message']);
        }

        return $this->respondWithError('no find code ', 'این ساختار یافت نشد');


    }

    public function login(Request $request)
    {
        $validData = $this->validate($request, [
                'phone_number' => 'required',
                'password' => 'required'
            ]
        );




        if (!auth()->attempt($validData)) {
            $message = 'رمز عبور یا شماره همراه اشتباه می باشد';
            return $this->respondErrorMessageWith200Status('The server understood the request but refuses to authorize it . ', $message);
        }

//        $tokens = Auth::user()->tokens;


//        foreach ($tokens as $token)
//        {
//            if ($typeUser == $token->name)
//                $token->delete();
//        }
//        $user = $this->loginGetToken($androidId);
//
//        $operatorM = Operator::where('user_id', collect($user))->first();

//        $result=collect($user);
//
//        $operator= new ResOperator($operatorM);
//
//        $result['operator']=$operator;
//
//
        return $this->respondTrue
        (
            User::where('phone_number', $validData['phone_number'])->first(),
            true,
            'کاربر با موفقیت وارد شد'
        );



    }

    public function register(Request $request)
    {
        $validData = $this->validate($request, [
            'first_name' => 'required | string | max:255',
            'last_name' => 'required | string | max:255',
            'username' => 'required | string | max:255',
            'password' => 'required | string | max:40',
            'phone_number' => 'required | string | size:11'

        ]);

        $phoneNumberUser = User::where('phone_number', $validData['phone_number']);
        if ($phoneNumberUser->exists())
            return $this->respondSuccessMessage('its not valid!', 'این  شماره تماس  در سیستم موجود می باشد، لطفا  با شماره تماس دیگری تلاش کنید ');

        $userM = User::where('username', $validData['username']);
        if ($userM->exists())
            return $this->respondSuccessMessage('its not valid!', 'این نام کاربری در سیستم موجود می باشد، لطفا نام کاربری دیگری انتخاب کنید . ');

        if (strlen($validData['password']) < 6)
            return $this->respondSuccessMessage('its not valid!', 'رمز کوتاه!!لطفا رمزی بیشتر از 6 رقم انتخاب کنید . ');

        $androidId = null;
        if (isset($request['android_id']))
            $androidId = $request['android_id'];


        $user = User::create([
            'first_name' => $validData['first_name'],
            'last_name' => $validData['last_name'],
            'username' => $validData['username'],
            'phone_number' => $validData['phone_number'],
            'password' => bcrypt($validData['password']),
            'api_token' => Str::random(100),
        ]);


        Auth::loginUsingId($user->id);

        $user->save();


        return $this->respondTrue($user);

    }

    public function updateUser(Request $request)
    {
        $validData = $this->validate($request,
            [
                'id' => 'required | Integer '
            ]
        );
        $user = User::where('id', $validData['id']);

        if (!$user->exists())
            return $this->respondSuccessMessage('its not valid!', 'این کاربر در سیستم موجود نمی باشد! ');


        else
        {
            $user = $user->first();

            if (isset($request->nick_name)) $user->nick_name = $request->nick_name;
            if (isset($request->level_id)) $user->level_id = $request->level_id;
            if (isset($request->model)) $user->model = $request->model;
            if (isset($request->fk)) $user->fk = $request->fk;
            if (isset($request->more)) $user->more = $request->more;
            if (isset($request->mac_address)) $user->mac_address = $request->mac_address;
            if (isset($request->device_model)) $user->device_model = $request->device_model;
            if (isset($request->version_install)) $user->version_install = $request->version_install;
            if (isset($request->status)) $user->status = $request->status;
            if (isset($request->type)) $user->type = $request->type;

            $user->save();
            return $this->respondCreated('کاربر با موفقیت اپدیت شد', 201, 'info updated');

        }

    }

    public function getListUser()
    {
        //ß$operatorM = auth()->user()->operator()->first();

        return $this->respondTrue
        (
            User::get(),
            true,
            'کاربران با موفقیت پیدا شدند'
        );
    }

    public function getUser(Request $request)
    {
        $validData = $this->validate($request,
            [
                'id' => 'required | Integer '
            ]
        );
//        $operatorM = auth()->user()->operator()->first();



        $user = User::where('id', $validData['id']);
        if ($user->exists())
        {

            return $this->respondTrue
            (
                $user->first(),
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

    public function loginGetToken($androidId = false, $admin = false){


        auth()->user()->update(
            [
//                'api_token' => Str::random(100),
                'android_id' => $androidId
            ]
        );

        if ($androidId && $admin == true)
            $token = auth()->user()->createToken('android_admin')->accessToken;
        else if ($androidId && $admin == false)
            $token = auth()->user()->createToken('android_user')->accessToken;
        else if ($admin == false)
            $token = auth()->user()->createToken('web_user')->accessToken;
//        return auth()->user()->tokens()->get() ;

        $user = new UserResource(auth()->user(), $token);
        return $user;
    }

    public function respondInternalError($description = 'Internal Error', $message = 'no message set')
    {
        return parent::respondInternalError($description, $message);
    }

}
