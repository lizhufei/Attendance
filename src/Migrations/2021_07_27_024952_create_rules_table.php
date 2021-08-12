<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (env('IS_RULE', 1)){
            Schema::create('rules', function (Blueprint $table) {
                $table->id();
                $table->char('name', 30)->nullable()->comment('规则名称');
                $table->integer('week')->comment('星期几 0-6');
                $table->time('on')->nullable()->comment('上班时间');
                $table->time('off')->nullable()->comment('下班时间');
                $table->time('on-star')->nullable()->comment('上班打卡开始时间');
                $table->time('on-end')->nullable()->comment('上班打卡结束时间');
                $table->time('delay')->nullable()->comment('下班打卡结束时间 null表示直到当天结束(0点)');
                $table->bigInteger('company_id')->nullable()->comment('公司ID');
                $table->tinyInteger('status')->default(1)->comment('0休息1上班');

                $table->timestamps();
            });
            $prefix = env('DB_PREFIX');
            Illuminate\Support\Facades\DB::statement("ALTER TABLE `".$prefix."rules` comment'考勤规则表'");//表注释
        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rules');
    }
}
