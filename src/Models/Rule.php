<?php

namespace Hsvisus\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $table = 'rules';
    protected $guarded = [];

    /**
     * 是否是休息日
     * @param string $week
     * @param null $company_id
     * @return bool
     */
    protected function isRest(string $week, $company_id=null):bool
    {
        return $this->where('week', $week)
            ->when($company_id, function($q)use($company_id){
                $q->where('company_id', $company_id);
            })
            ->where('status', 0)
            ->exists();
    }

    /**
     * 获取规则
     * @param string $week
     * @param int $company_id
     * @return mixed
     */
    protected function getRule(string $week, int $company_id)
    {
        return $this->where('week', $week)
            ->when($company_id, function($q)use($company_id){
                $q->where('company_id', $company_id);
            })
            ->first();
    }
}
