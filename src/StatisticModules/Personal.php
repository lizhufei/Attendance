<?php


namespace Hsvisus\Attendance\StatisticModules;


use Carbon\Carbon;
use Hsvisus\Attendance\Models\Attendance;

class Personal
{
    /**
     * 当天数据
     * @return array
     */
    public function day():array
    {
        $today = Carbon::today();
        return Attendance::whereDate('date', $today->toDateString())
            ->select('attendances.*', 'persons.name', 'persons.number')
            ->leftJoin('persons', 'attendances.person_id', '=', 'persons.id')
            ->get()->toArray();
    }

    /**
     * 本周(now), 上周(last)
     * @param string $period
     * @return array
     */
    public function week(string $period='now'):array
    {
        $today = Carbon::now();
        $end = $today->toDateString();
        switch ($period){
            case 'now':
                $start = $today->startOfWeek()->toDateString();
                break;
            case 'last':
                $subWeek = $today->subWeek();
                $start = $subWeek->startOfWeek()->toDateString();
                $end = $subWeek->endOfWeek()->toDateString();
                break;
        }
        return Attendance::region($start, $end);
    }

    /**
     * 某月份的数据
     * @param string $month
     * @return array
     */
    public function month(string $month):array
    {
        $month = Carbon::parse(date('Y-m'));
        $start = $month->toDateString();
        $end = $month->endOfMonth()->toDateString();
        return Attendance::region($start, $end);
    }

    /**
     * 自定义段
     * @param string $star
     * @param string $end
     * @return array
     */
    public function custom(string $start, string $end):array
    {
        return Attendance::region($start, $end);
    }
}
