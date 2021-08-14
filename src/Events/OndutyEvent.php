<?php


namespace Hsvisus\Attendance\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OndutyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $pid;
    private $time;
    private $sn;

    /**
     * 创建一个事件实例
     * OndutyEvent constructor.
     * @param $person_id  人员ID
     * @param $clock_time (日期加时间)
     * @param $device_sn  设备SN
     */
    public function __construct($person_id, $clock_time, $device_sn)
    {
        $this->pid = $person_id;
        $this->sn = $device_sn;
        $this->time = Carbon::parse($clock_time);

    }

}
