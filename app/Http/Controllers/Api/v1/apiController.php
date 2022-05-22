<?php
/**
 * Created by PhpStorm.
 * User: eDr
 * Date: 9/6/2017
 * Time: 12:51 PM
 */

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;

class apiController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth.basic', ['only' => ['store', 'destroy']]);
    }

    protected $statusCode = 200;

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param $data
     * @param array $headers
     * @return mixed
     */
    public function respond($data, $headers = [])
    {
        return response($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($description, $message = 'no message set')// با استفاده از متود به راحتی مقدار کد وضعیت رو در قسمت دیتا هم قرار می دهیم
    {
        return $this->respond([
            'ok' => false,
            'error_code' => $this->getStatusCode(),
            'description' => $description,
            'message' => $message
        ]);
    }

    public function respondWithMessage($description, $message = 'no message set')// با استفاده از متود به راحتی مقدار کد وضعیت رو در قسمت دیتا هم قرار می دهیم
    {
        return $this->respond([
            'ok' => true,
            'error_code' => $this->getStatusCode(),
            'description' => $description,
            'message' => $message
        ]);
    }


    public function respondSuccessMessage($description = 'The request has succeeded.', $message = 'no message set')
    {
        $this->setStatusCode(200);
        return $this->respondWithError($description, $message);
    }

    public function respondTrue($result, $ok = true, $message = 'null')
    {
        return $this->respond([
            'ok' => $ok,
            'message' => $message,
            'result' => $result,
            'status' => $this->getStatusCode()

        ]);
    }


    //متدهایی برای ارورهای معروفی که خیلی بیشتر اتفاق خواهند افتاد

    public function respondCreated($message, $statusCode = 201, $description = 'info created.')
    {
        $this->setStatusCode($statusCode);
        return $this->respondWithMessage($description, $message);

    }


    public function respondNotFound($description, $message = 'no message set')
    {
        $this->setStatusCode(404);
        return $this->respondWithError($description, $message);
    }

    public function respondInternalError($description = 'Internal Error', $message = 'no message set')
    {
        $this->setStatusCode(500);
        return $this->respondWithError($description, $message);
    }

    public function respondDeleted($message)
    {
        $this->setStatusCode(202);
        return $this->respondTrue([
            'message' => $message
        ]);
    }

    public function respondValidationError($description = 'The server understood the request but refuses to authorize it.', $message = 'no message set')
    {
        $this->setStatusCode(403);
        return $this->respondWithError($description, $message);
    }

    public function respondBadRequest($description = 'Bad Request', $message = 'no message set')
    {
        $this->setStatusCode(400);
        return $this->respondWithError($description, $message);
    }

    public function respondErrorMessageWith200Status($description = 'The request has succeeded.', $message = 'no message set')
    {
        $this->setStatusCode(200);
        return $this->respondWithError($description, $message);
    }

    protected function getPaginationInfo($data)
    {
        return [
            'total' => $data->total(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'next_page_url' => $data->nextPageUrl(),
            'prev_page_url' => $data->previousPageUrl(),
            'limit' => $data->perPage()
        ];
    }


    public function checkOperatorAccess($operator,$level)
    {
        if ($operator == null || $operator->level_id < $level)
            return false;
        return true;
    }


// ذخیره عکس
    public function saveImage($nameFolder, $nameFile, $base64, $model, $width = 800, $height = 600, $size = "400")
    {
        $date = date_format(new \DateTime(), 'Y-m-d H:i:s');
        $date = str_replace(' ', '-', $date);
        $date = str_replace(':', '-', $date);

        $entry = base64_decode($base64);
        $image = imagecreatefromstring($entry);
        $folderName = $model->id;
        $title = $nameFile . '-' . $model->id . '-' . $date;
        $directory = "/images/$nameFolder/" . $folderName . '/';
        $directoryF = base_path() . "/public_html" . $directory;
        $directoryF = str_replace('\\', '/', $directoryF);
        if (!file_exists($directoryF))
        {
            mkdir($directoryF, 0777, true);
        }
        $directoryF = $directoryF . $title . ".jpg";
        $imageURL = (url($directory . $title . ".jpg"));

        $png = imagecreatefrompng(base_path() . "/public_html/php_image" . '/site_logo.png');
        imagejpeg($image, $directoryF, 100);
        $jpeg = $this->resize_image($directoryF, $width, $height, false);

//                list($width, $height) = getimagesize(base_path()."/public_html/php_image".'/food.jpg');
        list($newwidth, $newheight) = getimagesize(base_path() . "/public_html/php_image" . '/site_logo.png');


        // Set the margins for the stamp and get the height/width of the stamp image

        $whiteBackground = imagecolorallocate($jpeg, 255, 255, 255);
        imagefill($jpeg, 0, 0, $whiteBackground);
        $marge_right = 10;
        $marge_bottom = 10;
        $sx = imagesx($png);
        $sy = imagesy($png);
//
        imagecopyresampled($jpeg, $png,
            imagesx($jpeg) - $sx - $marge_right,
            imagesy($jpeg) - $sy - $marge_bottom, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
        imagejpeg($jpeg, $directoryF, 100);
        return $imageURL;
    }

// ویرایش اندازه عکس
    private
    function resize_image($file, $w, $h, $crop = FALSE)
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop)
        {
            if ($width > $height)
            {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            }
            else
            {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        }
        else
        {
            if ($w / $h > $r)
            {
                $newwidth = $h * $r;
                $newheight = $h;
            }
            else
            {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);

        $whiteBackground = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $whiteBackground);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }


    public function sendSmsServices($phoneNumber, $type, $token20, $token1 = null, $token2 = null, $token3 = null)
    {

        try
        {
            $kavenegar = new KavenegarApi('');//ADD My Key
            $messageM = [
                'ok' => true,
                'message' => 'پیام با موفقیت ارسال شد'
            ];


            if (substr_count($token20, ' ') <= 8)
            {
                $kavenegar->VerifyLookup($phoneNumber, $token1, $token2, $token3, $type, null, null, $token20);
                return $messageM;
            }

            else
            {
                $messageM['message'] = 'با توجه به زیاد بودن تعداد خط فاصله ( تعداد اسپیس ) پیام از خط نبلیغاتی برای کاربر ارسال شد';
                $errorSms = $this->sendSMS($phoneNumber, " درخواست شما با مضمون :  " . "\n" . $token20 . "\n" . "در سامانه پاسخ داده شد دانشگاه س و ب");
                if ($errorSms != null)
                {
                    $messageM['ok'] = false;
                    $messageM['message'] = $errorSms;
                }

                return $messageM;
            }


        } catch (\Kavenegar\Exceptions\ApiException $e)
        {
            return $messageM = [
                'ok' => false,
                'message' => $e->getMessage()
            ];


        } catch (\Kavenegar\Exceptions\HttpException $e)
        {
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد


            return $messageM = [
                'ok' => false,
                'message' => ' ( از متصل بودن اینترنت خود اطمینان حاصل فرماید ) مشکل برقراری با وب سرویس پیامک لطفا دوباره تلاش کنید',
            ];
        }


    }


    // ارسال پیام به کاربر
    public
    function sendSMS($phone_number, $text)
    {
        try
        {
            $kavenegar = new KavenegarApi('');// TODO ADD MY KEY
            $result = $kavenegar->Send('10000090990900', $phone_number, $text);
            $result = $result[0];
            if ($result->status == 200)
                return null;

        } catch (\Kavenegar\Exceptions\ApiException $e)
        {
            return $e->getMessage();


        } catch (\Kavenegar\Exceptions\HttpException $e)
        {
            // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
            return ' ( از متصل بودن اینترنت خود اطمینان حاصل فرماید ) مشکل برقراری با وب سرویس پیامک لطفا دوباره تلاش کنید';
        }

        return 'ok';

    }
}
