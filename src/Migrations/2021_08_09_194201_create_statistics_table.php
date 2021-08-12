<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('日期');
            $table->bigInteger('person_id')->comment('人员ID');
            $table->char('name', 10)->nullable()->comment('职员姓名');
            $table->char('number', 50)->nullable()->comment('职员编号');
            $table->integer('last')->default(0)->comment('迟到天数');
            $table->integer('lack')->default(0)->comment('缺卡天数');
            $table->integer('absent')->default(0)->comment('旷工天数');
            $table->integer('normal')->default(0)->comment('出勤天数');
            $table->integer('leave')->default(0)->comment('请假天数');
            $table->integer('early')->default(0)->comment('早退天数');
            $table->bigInteger('auditor')->nullable()->comment('审核人');
            $table->bigInteger('company_id')->nullable()->comment('公司ID');
            $table->tinyInteger('status')->default(0)->comment('-1拒绝0待审核1同意');
            $table->tinyInteger('work')->default(0)->comment('0休息1上班');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistics');
    }
}
