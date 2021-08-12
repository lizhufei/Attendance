<?php

namespace Hsvisus\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'holidays';
    protected $guarded = [];

    /**
     * 是否是节假日
     * @param string $date
     * @param int $company_id
     * @return bool
     */
    protected function isFestival(string $date, $company_id=null):bool
    {
        return $this->whereDate('start', '<=', $date)
            ->when($company_id, function($q)use($company_id){
                $q->where('company_id', $company_id);
            })
            ->whereDate('end', '>=', $date)
            ->exists();
    }
}
