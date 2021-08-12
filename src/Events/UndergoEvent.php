<?php


namespace Hsvisus\Attendance\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UndergoEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 创建一个事件实例
     * @return void
     */
    public function __construct()
    {
    }


}
