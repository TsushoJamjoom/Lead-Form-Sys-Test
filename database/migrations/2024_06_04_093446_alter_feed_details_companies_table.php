<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // MAN
            $table->integer('man_pick_up')->nullable()->after('european_latest');
            $table->integer('man_light_duty_truck')->nullable()->after('man_pick_up');
            $table->integer('man_medium_duty_truck')->nullable()->after('man_light_duty_truck');
            $table->integer('man_heavy_duty_truck')->nullable()->after('man_medium_duty_truck');
            $table->integer('man_total')->nullable()->after('man_heavy_duty_truck');
            $table->string('man_oldest', 30)->nullable()->after('man_total');
            $table->string('man_latest', 30)->nullable()->after('man_oldest');
            // VOLVO
            $table->integer('volvo_pick_up')->nullable()->after('man_latest');
            $table->integer('volvo_light_duty_truck')->nullable();
            $table->integer('volvo_medium_duty_truck')->nullable();
            $table->integer('volvo_heavy_duty_truck')->nullable();
            $table->integer('volvo_total')->nullable();
            $table->string('volvo_oldest', 30)->nullable();
            $table->string('volvo_latest', 30)->nullable();
            // MERCEDES
            $table->integer('mercedes_pick_up')->nullable()->after('volvo_latest');
            $table->integer('mercedes_light_duty_truck')->nullable();
            $table->integer('mercedes_medium_duty_truck')->nullable();
            $table->integer('mercedes_heavy_duty_truck')->nullable();
            $table->integer('mercedes_total')->nullable();
            $table->string('mercedes_oldest', 30)->nullable();
            $table->string('mercedes_latest', 30)->nullable();
            // UD
            $table->integer('ud_pick_up')->nullable()->after('mercedes_latest');
            $table->integer('ud_light_duty_truck')->nullable();
            $table->integer('ud_medium_duty_truck')->nullable();
            $table->integer('ud_heavy_duty_truck')->nullable();
            $table->integer('ud_total')->nullable();
            $table->string('ud_oldest', 30)->nullable();
            $table->string('ud_latest', 30)->nullable();
            // OTHER
            $table->integer('other_pick_up')->nullable()->after('ud_latest');
            $table->integer('other_light_duty_truck')->nullable();
            $table->integer('other_medium_duty_truck')->nullable();
            $table->integer('other_heavy_duty_truck')->nullable();
            $table->integer('other_total')->nullable();
            $table->string('other_oldest', 30)->nullable();
            $table->string('other_latest', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
