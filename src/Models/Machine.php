<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $table = 'machines';
    protected $guarded = [];

    public const MONITOR = 1; //监控机
    public const ATTEND = 2;  //考勤机
    public const VISITOR = 3; //访客机

    /**
     * 获取同类型设备
     * @param int $type
     * @param int|null $company_id
     * @return array|null
     */
    protected function same(int $type=2, int $company_id=null)
    {
        return $this->where('type', $type)
            ->when($company_id, function($q)use($company_id){
                $q->where('company_id', $company_id);
            })
            ->get();
    }

    /**
     * 获取设备类型信息
     * @param string $device_sn
     * @return mixed
     */
    protected function info(string $device_sn)
    {
        return $this->where('device_sn', $device_sn)->first();
    }

}
