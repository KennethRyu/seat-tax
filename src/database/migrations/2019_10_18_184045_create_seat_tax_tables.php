<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatTaxTables extends Migration
{
    /**
     * Run the migrations.
     * 运行迁移。
     * @return void
     */
    public function up()
    {
        // 公司账单
        Schema::create('seat_tax_corp_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('corporation_id')->comment('公司ID');
            $table->smallInteger('month')->comment('月份');
            $table->smallInteger('year')->comment('年份');
            $table->bigInteger('pve_bill')->comment('pve账单');
            $table->bigInteger('mining_bill')->comment('采矿账单');
            $table->smallInteger('pve_taxrate')->comment('PVE税率');
            $table->smallInteger('mining_taxrate')->comment('采矿税率');
            $table->smallInteger('mining_modifier')->comment('my comment');
            $table->timestamps();
        });

        // 人物账单
        Schema::create('seat_tax_character_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id')->comment('角色ID');
            $table->bigInteger('corporation_id')->comment('公司ID');
            $table->smallInteger('month')->comment('月份');
            $table->smallInteger('year')->comment('年份');
            $table->bigInteger('mining_bill')->comment('采矿账单');
            $table->smallInteger('mining_taxrate')->comment('采矿税率');
            $table->smallInteger('mining_modifier');
            $table->timestamps();
        });

        // 军团税率变更记录表
        Schema::create('seat_tax_rate_change', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('corporation_id')->comment('公司ID');
            $table->smallInteger('pve_taxrate_old')->comment('PVE税率 - 旧');
            $table->smallInteger('pve_taxrate_new')->comment('PVE税率 - 新');
            $table->timestamp('begin_time')->comment('开始时间');
            $table->timestamp('end_time')->comment('结束时间');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     * 反向迁移。
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_tax_corp_bill');
        Schema::dropIfExists('seat_tax_character_bill');
        Schema::dropIfExists('seat_tax_rate_change');
    }
}
