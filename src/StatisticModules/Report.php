<?php


namespace Hsvisus\Attendance\StatisticModules;

use Carbon\Carbon;
use Hsvisus\Attendance\Models\Attendance;
use Hsvisus\Attendance\Models\Holiday;
use Hsvisus\Attendance\Models\Leave;
use Hsvisus\Attendance\Models\Person;
use Hsvisus\Attendance\Models\Rule;

class Report
{
    /**
     * 月报表
     * @param string $month
     * @param int|null $company_id
     * @return array
     */
    public function statement(string $month='', int $company_id=null)
    {
        if (empty($month)){
            $month = Carbon::now();
        }else{
            $month = Carbon::parse(date('Y').'-'. $month);
        }

        //正常要出勤天数
        $attendanceDays = $this->fullAttend($month->month, $company_id);
        //公司实际全部考勤
        $reality = Attendance::statement($month->month);
        //公司所有人员
        $persons = Person::workforce($company_id);
        $data = [];
        $index = 0;
        foreach ($persons as $p){
            $startOfMonth = $month->copy()->startOfMonth();
            //月份天数
            for ($i=0; $i<$month->daysInMonth; $i++){
                //循环到的月号数
                if (0 != $i){
                    $startOfMonth->addDay(1);
                }
                $data[$index] = [
                    'date' => $startOfMonth->toDateString(),
                    'person_id' => $p['id'],
                    'name' => $p['name'],
                    'number' => $p['number']
                ];
                //实际要出勤的日子
                if (in_array($i+1, $attendanceDays['days'])){
                    $data[$index]['work'] = 1;
                    //是否存在考勤
                    if (
                        isset($reality[$p['id']]) &&
                        isset($reality[$p['id']][$startOfMonth->toDateString()])
                    ){
                        $list = $reality[$p['id']][$startOfMonth->toDateString()];
                        if (2 == $list['status']){
                            $data[$index]['absent'] = 1;
                        }
                        if (
                            Attendance::NORMAL_CLOCK == $list['on_duty'] &&
                            Attendance::FINISH_CLOCK == $list['off_duty'])
                        {
                            $data[$index]['normal'] = 1;
                        }else{
                            if (Attendance::LASE_CLOCK == $list['on_duty']){
                                $data[$index]['last'] = 1;
                            }
                            if (Attendance::ON_LACK_CLOCK == $list['on_duty']){
                                $data[$index]['lack'] = 1;
                            }
                            if (Attendance::OFF_LACK_CLOCK == $list['on_duty']){
                                $data[$index]['lack'] = $data[$i]['lack']??$data[$i]['lack']++;
                            }
                            if (Attendance::EARLY_CLOCK == $list['off_duty']){
                                $data[$index]['early'] = 1;
                            }
                        }
                        $data[$index]['leave'] = Leave::isLeaveForToday($p['id'])?:0;
                    }else{
                        $data[$index]['absent'] = 1;
                    }
                }
                $index++;
            }
        }
        return $data;
    }

    /**
     * 一个月里全勤的日子
     * @param string $month
     * @param int|null $company_id
     * @return array
     */
    public function fullAttend(string $month, $company_id=null):array
    {
        $currentMonth = Carbon::parse(date('Y').'-'.$month);
        $mountCount = $currentMonth->daysInMonth;
        $data = ['days' => [], 'count' => 0];
        for ($i=0; $i<$mountCount; $i++){
            $inTheDay = $currentMonth->copy()->addDay($i);
            if (Holiday::isFestival($inTheDay->toDateString(), $company_id)){
                continue;
            }

            if (Rule::isRest($inTheDay->dayOfWeek, $company_id)){
                continue;
            }
            $data['count'] += 1;
            $data['days'][] = $i+1;
        }
        return $data;
    }











}
