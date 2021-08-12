<?php

namespace Hsvisus\Attendance\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public const NORMAL_CLOCK = 1;    //上班卡
    public const ON_LACK_CLOCK = 2;   //上班缺卡
    public const LASE_CLOCK = 3;      //迟到卡
    public const ABSENT_CLOCK = 4;    //旷工卡

    public const FINISH_CLOCK = 5;    //下班卡
    public const OFF_LACK_CLOCK = 6;  //下班缺卡
    public const EARLY_CLOCK = 7;     //早退卡

    public const INVALID_CLOCK = 0;   //无效卡

    private $regime = [
        ['code' => '600', 'msg' => '无效卡'],
        ['code' => '601', 'msg' => '上班打卡'],
        ['code' => '602', 'msg' => '缺少上班卡'],
        ['code' => '603', 'msg' => '迟到'],
        ['code' => '604', 'msg' => '旷工'],
        ['code' => '605', 'msg' => '下班打卡'],
        ['code' => '606', 'msg' => '缺少下班卡'],
        ['code' => '607', 'msg' => '早退'],

    ];

    public function face()
    {
        return $this->belongsTo(
            config('attend.face_model', 'Hsvisus\Equipment\Models\Face'),
            'face_id'
        );
    }
    public function person()
    {
        return $this->belongsTo(
            config('attend.person_model', 'App\Models\Person'),
            'person_id'
        );
    }
    public function getDescribeAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setDescribeAttribute($value)
    {
        $this->attributes['describe'] = json_encode($value, 320);
    }


    /**
     * 是否是第一次打卡
     * @param int $person_id
     * @param string $date
     * @param int $company_id
     * @return bool
     */
    protected function isFirst(int $person_id, string $date, int $company_id):bool
    {
        return $this->where('person_id', $person_id)
            ->when($company_id, function($q)use($company_id){
                $q->where('company_id', $company_id);
            })
            ->whereDte('date', $date)
            ->exists();
    }

    /**
     * 保存打卡记录
     * @param string $device_sn
     * @param int $person_id
     * @param string $dateTime
     * @param int $data
     * @param $company_id
     */
    protected function store(string $device_sn, int $person_id, string $dateTime, int $data, $company_id)
    {
        $date = explode(' ', $dateTime);
        $fields = $this->where('person_id', $person_id)
            ->whereData('date', $date[0])
            ->first();
        if ($fields){
            if (Attendance::ABSENT_CLOCK == $data){
                $fields->status = 0;
            }elseif (Attendance::FINISH_CLOCK > $data){
                $fields->on_duty = $data;
            }else{
                $fields->off_duty = $data;
            }
            $this->regime[$data]['device_sn'] = $device_sn;
            $fields->describe[] = $this->regime[$data];
            return $fields->save();
        }else{
            $fields = [
                'person_id' => $person_id,
                'date' => $date[0],
                'company_id' => $company_id,
            ];
            if (Attendance::ABSENT_CLOCK == $data){
                $fields['status'] = 1;
            }elseif (Attendance::FINISH_CLOCK > $data){
                $fields['on_duty'] = $data;
            }else{
                $fields['off_duty'] = $data;
            }
            $this->regime[$data]['device_sn'] = $device_sn;
            $this->regime[$data]['time'] = $date[1];
            $fields['describe'][] = $this->regime[$data];
            return $this->create($fields);
        }
    }

    /**
     * 获取某时间区间考勤数据
     * @param string $star
     * @param string $end
     * @return array
     */
    protected function region(string $star, string $end):array
    {
        return $this->whereBetween('date', [$star, $end])
            ->select('attendances.*','persons.name', 'persons.number')
            ->leftJoin('persons', 'attendances.person_id', '=', 'persons.id')
            ->get()->toArray();
    }

    /**
     * 月考勤
     * @param string $month
     * @param int|null $company_id
     * @return array
     */
    protected function statement(string $month, int $company_id=null)
    {
        $attendances = $this->whereMonth('date', $month)
            ->when($company_id, function($q)use($company_id){
                $q->where('company_id', $company_id);
            })
            ->get();
        $data = [];
        foreach ($attendances as $item){
            $data[$item->person_id][$item->date] = $item->toArray();
        }
        return $data;
    }
}
