<?php

namespace Hsvisus\Attendance\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $table = 'leaves';
    protected $guarded = [];

    /**
     * 今天是否请假
     * @param int $person_id
     * @return mixed
     */
    protected function isLeaveForToday(int $person_id)
    {
        $today = Carbon::today();

        return $this->from('leave_lists')
            ->whereDate('date', $today->toDateString())
            ->where('person_id', $person_id)
            ->value('proportion');

    }

}
