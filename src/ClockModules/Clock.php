<?php
/**
 * 打卡时间计算表
 */

namespace Hsvisus\Attendance\ClockModules;


use Carbon\Carbon;
use Hsvisus\Attendance\Events\OffdutyEvent;
use Hsvisus\Attendance\Events\OndutyEvent;
use Hsvisus\Attendance\Events\UndergoEvent;
use Hsvisus\Attendance\Models\Attendance;
use Hsvisus\Attendance\Models\Rule;
use Hsvisus\Attendance\Models\Holiday;

class Clock
{
    private $rule;
    private $day;
    private $company_id;
    private $begin;
    private $finish;
    private $end;
    private $on;
    private $off;

    public function __construct(Carbon $day, Rule $rule, int $company_id=null)
    {
        $this->day = $day;
        $this->company_id = $company_id;
        $this->rule = $rule;
        //上班时间
        $this->on = Carbon::parse($rule->on);
        //下班时间
        $this->off = Carbon::parse($rule->off);
        //上班打卡开始时间
        $this->begin = Carbon::parse($rule->on_star);
        //上班打卡结束时间
        $this->end = Carbon::parse($rule->on_end);
        //下班结束时间
        $this->finish = Carbon::parse($rule->delay?:'23:59:59');

    }
    /**
     * 是否休息日
     * @return bool
     */
    public function isRest():bool
    {
        //节假日
        if (Holiday::isFestival($this->day->toDateString(), $this->company_id)){
            return true;
        }else{
            //规则
            return $this->rule->status ?false :true;
        }
    }

    /**
     * 是否在有效时间范围
     * @return bool
     */
    public function isScope():bool
    {
        if ($this->day->lt($this->begin)){
            return false;
        }
        if ($this->day->gt($this->finish)){
            return false;
        }
        return true;
    }

    /**
     * 打卡状态
     * @param int $person_id
     * @param string $device_sn
     * @param int|null $company_id
     * @return int
     */
    public function state(int $person_id, string $device_sn)
    {
        //迟到半天算旷工
        //是否第一次打卡来判断是上班还是下班
        $clock_time = $this->day->toDateTimeString();

        if ($sign = Attendance::getSign($person_id, $clock_time, $this->company_id)){
            //下班卡
            if ($this->day->gte($this->off) && $this->day->lte($this->finish)){
                if (Attendance::OFF_LACK_CLOCK != $sign->off_duty){
                    return Attendance::INVALID_CLOCK; //无效卡
                }
                event(new OffdutyEvent($person_id, $clock_time, $device_sn));   //下班打卡事件
                return Attendance::FINISH_CLOCK; //下班卡
            }
        }else{
            event(new OndutyEvent($person_id, $clock_time, $device_sn)); //上班打卡事件
            //上班卡
            if ($this->day->lte($this->on)){
                return Attendance::NORMAL_CLOCK; //上班正常卡
            }
            if ($this->day->gte($this->end)){
                return Attendance::ABSENT_CLOCK; //旷工卡
            }else{
                return Attendance::LASE_CLOCK;  //迟到卡
            }
        }
        event(new UndergoEvent($person_id, $clock_time, $device_sn));  //离岗事件
        return Attendance::INVALID_CLOCK; //无效卡
    }
}
