<?php


namespace Hsvisus\Attendance\ClockModules;


use Illuminate\Support\Facades\Http;

class Holiday
{
    private $legal_url = 'http://api.tianapi.com/txapi/jiejiari/index?key=9c09c8237fa10a013bac4546513e84b6&type=1&date=';

    /**
     * 法定节日获取
     * @return array
     */
    public function legal():array
    {
        $response = Http::get($this->legal_url.date('Y'))->json();
        if (200 == $response['code']){
            return $response['newslist'];
        }
        return [];
    }

}
