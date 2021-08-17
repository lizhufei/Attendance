### 考勤系统功能
- 发布`php artisan vendor:publish --provider="Hsvisus\Attendance\AttendProvider"`
- 数据库迀移`php artisan migrate`
- 添加服务和门面别名 在app.config里
  `Hsvisus\Attendance\AttendProvider::class; 'Attend' => Hsvisus\Attendance\FacadeService::class,`  
- 总共分两个模块 考勤打卡模块和考勤统计模块
- 编写打卡事件订阅者
``` 
    / 处理上班打卡事件
    public function handleOnduty($event)
    {
        ww(['OndutyEvent'], 'event_on.txt');
    }
     // 处理离岗事件
    public function handleUndergo($event)
    {
        ww(['UndergoEvent'], 'event_un.txt');
    }
    // 处理下班打卡事件
    public function handleOffduty($event)
    {
        ww(['OffdutyEvent'], 'event_off.txt');
    }
```
- 门面方法:
```
  /**
    * 判断打卡时间状态(上班 ,迟到, 旷工, 下班)
    * @param Model $person
    * @param Model $face
    * @return array
    */
      public function auditClock(Model $person, Model $face):array
   /**
     * 获取法定节假日
     * @return array
     */
    public function getLegal()
  /**
     * 获取考勤数据
     * @param string $type
     * @param array $params
     * @return array
     */
    public function getAttendanceData($type='today', array $params=[])
  /**
     * 生成考勤统计表
     * @param string $month
     * @param int|null $company_id
     * @return mixed
     */
    public function generateStatistics(string $month='', int $company_id=null)
```
