### 考勤系统功能
- 发布`php artisan vendor:publish --provider="Hsvisus\Attendance\AttendProvider"`
- 数据库迀移`php artisan migrate`
- 添加门面别名 在app.config里` 'Attend' => Hsvisus\Attendance\FacadeService::class,`  
- 总共分两个模块 考勤打卡模块和考勤统计模块
- 门面方法: 
  ```
  /**
    * 审核打卡时间
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
