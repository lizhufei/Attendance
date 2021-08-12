<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(env('IS_HOLIDAY', 1)) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('company_id')->default(0)->comment('公司ID');
                $table->char('name', 20)->comment('节假日名字');
                $table->date('start')->nullable()->comment('开始日期');
                $table->date('end')->nullable()->comment('结束日期');
                $table->tinyInteger('type')->default(1)->comment('1休息2补班3周未设置');
                $table->string('remark')->default('')->comment('说明描述');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('holidays');
    }
}
