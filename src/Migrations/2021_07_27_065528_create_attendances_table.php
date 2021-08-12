<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(env('IS_ATTENDANCE', 1)){
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('person_id')->comment('职员ID');
//                $table->bigInteger('face_id')->comment('刷脸ID');
                $table->tinyInteger('on_duty')->default(2)->comment('上班');
                $table->tinyInteger('off_duty')->default(6)->comment('下班');
                $table->date('date')->comment('考勤日期');
                $table->bigInteger('company_id')->nullable()->comment('公司ID');
                $table->json('describe')->nullable()->comment('打卡结果说明');
                $table->tinyInteger('status')->default(1)->comment('0旷工1上班');
                $table->timestamps();
            });
            $prefix = env('DB_PREFIX');
            Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$prefix."attendances` comment'考勤表'");//表注释
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
