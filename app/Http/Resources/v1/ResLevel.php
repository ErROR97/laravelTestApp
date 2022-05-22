<?php


namespace App\Http\Resources\v1;


use Illuminate\Http\Resources\Json\JsonResource;

class ResLevel extends JsonResource
{

    public function toArray($request)
    {

        $options = json_decode($this->options);

        $data='';

        if ($options->vote == "1") $data = $data . ' رای ها: دارد ';
        else $data = $data . ' رای ها: ندارد ';

        if ($options->school == "1") $data = $data . ' مدرسه ها: دارد ';
        else $data = $data . '  مدرسه ها: ندارد ';

        if ($options->city == "1") $data = $data . ' شهر ها: دارد ';
        else $data = $data . ' شهر ها: ندارد ';

        if ($options->report == "1") $data = $data . ' گزارش ها: دارد ';
        else $data = $data . ' گزارش ها: ندارد ';

        if ($options->operator == "1") $data = $data . ' اپراتور ها: دارد ';
        else $data = $data . ' اپراتور ها: ندارد ';


        return
            [
                'id' => $this->id,
                'title' => $this->title,
                'options' => $data,
                'explain' => $this->explain
            ];
    }
}
