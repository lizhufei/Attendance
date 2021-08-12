<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->char('name', 50)->nullable()->comment('机器别名');
            $table->char('device_sn')->unique()->comment('机器SN码');
            $table->bigInteger('company_id')->nullable()->comment('公司ID');
            $table->char('gate', 10)->default('entrance')->comment('出:exit|入:entrance');
            $table->tinyInteger('type')->default(1)->comment('1监控机2考勤机3访客机...');
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
        Schema::dropIfExists('machines');
    }
}
