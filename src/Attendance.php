<?php


namespace Hsvisus\Attendance;


use Hsvisus\Attendance\ClockModules\Clock;
use Hsvisus\Attendance\ClockModules\Holiday;
use Hsvisus\Attendance\Models\Statistic;
use Hsvisus\Attendance\StatisticModules\Personal;
use Hsvisus\Attendance\StatisticModules\Report;
use Illuminate\Database\Eloquent\Model;

class Attendance
{
    /**
     * 审核打卡时间
     * @param Model $person
     * @param Model $face
     * @return array
     */
    public function auditClock(Model $person, Model $face):array
    {
        $dateTime = $face->screen_time;
        $company = $person->company_id??null;
        $clock = new Clock($dateTime, $company);
        if ($clock->isRest()){
            return false;
        }
        if ($clock->isScope()){
            $result = $clock->state($person->id, $company);

            return \Hsvisus\Attendance\Models\Attendance::INVALID_CLOCK == $result
                ? false
                : \Hsvisus\Attendance\Models\Attendance::store(
                        $face->device_sn,
                        $person->id,
                        $dateTime,
                        $result,
                        $company,
                        $face->mask??0,
                        $face->temperature??0
                    );
        }
        return false;
    }

    /**
     * 获取法定节假日
     * @return array
     */
    public function getLegal()
    {
        $data = (new Holiday())->legal();
        if (empty($data)){
            return false;
        }
        \Hsvisus\Attendance\Models\Holiday::whereCompanyId(0)->delete();
        foreach($data as $item){
            $vacation = explode('|', $item['vacation']);
            \Hsvisus\Attendance\Models\Holiday::create([
                'name' => $item['name'],
                'start' => $vacation[$item['start']],
                'end' => $vacation[$item['end']],
                'remark' => $item['rest']
            ]);
        }

    }

    /**
     * 获取考勤数据
     * @param string $type
     * @param array $params
     * @return array
     */
    public function getAttendanceData($type='today', array $params=[])
    {
        $attendanceData = new Personal();
        switch ($type){
            case 'today':
                return $attendanceData->day();
            case 'week':
                return $attendanceData->week($params['week']);
            case 'month':
                return $attendanceData->month($params['month']);
            default:
                return $attendanceData->custom($params['start'], $params['end']);
        }
    }

    /**
     * 生成考勤统计表
     * @param string $month
     * @param int|null $company_id
     * @return mixed
     */
    public function generateStatistics(string $month='', int $company_id=null)
    {
        $statistics = new Report();
        $data = $statistics->statement($month, $company_id);
        return Statistic::insert($data);
    }

}
