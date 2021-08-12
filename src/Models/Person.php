<?php

namespace Hsvisus\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';
    protected $guarded = [];

    /**
     * 全体职员
     * @param int|null $company_id
     * @return array
     */
    protected function workforce($company_id=null):array
    {
        return $this->when($company_id, function($q)use($company_id){
            $q->where('company_id', $company_id);
        })
            ->where('status', 1)
            ->get()->toArray();
    }
}
