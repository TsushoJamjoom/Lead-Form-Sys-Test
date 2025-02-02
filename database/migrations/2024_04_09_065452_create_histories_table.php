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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            // Section 1 (Company Details)
            $table->string('company_name', 100)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('crid', 20)->nullable();
            $table->string('customer_code', 20)->nullable();
            $table->string('position', 50)->nullable();
            $table->string('mobile_no', 15)->nullable();
            $table->string('email', 30)->nullable();
            $table->string('website')->nullable();
            $table->string('user_logo')->nullable();
            $table->string('vat', 20)->nullable();
            $table->tinyInteger('construction')->default(0);
            $table->tinyInteger('food')->default(0);
            $table->tinyInteger('rental')->default(0);
            $table->tinyInteger('logistics')->default(0);
            $table->string('describe_other', 100)->nullable();
            $table->string('national_address')->nullable();
            // Section 2 (Feet Details)
            // HINO
            $table->integer('hino_pick_up')->nullable();
            $table->integer('hino_light_duty_truck')->nullable();
            $table->integer('hino_medium_duty_truck')->nullable();
            $table->integer('hino_heavy_duty_truck')->nullable();
            $table->integer('hino_total')->nullable();
            $table->string('hino_oldest', 30)->nullable();
            $table->string('hino_latest', 30)->nullable();
            // ISUZU
            $table->integer('isuzu_pick_up')->nullable();
            $table->integer('isuzu_light_duty_truck')->nullable();
            $table->integer('isuzu_medium_duty_truck')->nullable();
            $table->integer('isuzu_heavy_duty_truck')->nullable();
            $table->integer('isuzu_total')->nullable();
            $table->string('isuzu_oldest', 30)->nullable();
            $table->string('isuzu_latest', 30)->nullable();
            // FUSO
            $table->integer('fuso_pick_up')->nullable();
            $table->integer('fuso_light_duty_truck')->nullable();
            $table->integer('fuso_medium_duty_truck')->nullable();
            $table->integer('fuso_heavy_duty_truck')->nullable();
            $table->integer('fuso_total')->nullable();
            $table->string('fuso_oldest', 30)->nullable();
            $table->string('fuso_latest', 30)->nullable();
            // SITRAK
            $table->integer('sitrak_pick_up')->nullable();
            $table->integer('sitrak_light_duty_truck')->nullable();
            $table->integer('sitrak_medium_duty_truck')->nullable();
            $table->integer('sitrak_heavy_duty_truck')->nullable();
            $table->integer('sitrak_total')->nullable();
            $table->string('sitrak_oldest', 30)->nullable();
            $table->string('sitrak_latest', 30)->nullable();
            // SANY
            $table->integer('sany_pick_up')->nullable();
            $table->integer('sany_light_duty_truck')->nullable();
            $table->integer('sany_medium_duty_truck')->nullable();
            $table->integer('sany_heavy_duty_truck')->nullable();
            $table->integer('sany_total')->nullable();
            $table->string('sany_oldest', 30)->nullable();
            $table->string('sany_latest', 30)->nullable();
            // SHACMAN
            $table->integer('shacman_pick_up')->nullable();
            $table->integer('shacman_light_duty_truck')->nullable();
            $table->integer('shacman_medium_duty_truck')->nullable();
            $table->integer('shacman_heavy_duty_truck')->nullable();
            $table->integer('shacman_total')->nullable();
            $table->string('shacman_oldest', 30)->nullable();
            $table->string('shacman_latest', 30)->nullable();
            // FAW
            $table->integer('faw_pick_up')->nullable();
            $table->integer('faw_light_duty_truck')->nullable();
            $table->integer('faw_medium_duty_truck')->nullable();
            $table->integer('faw_heavy_duty_truck')->nullable();
            $table->integer('faw_total')->nullable();
            $table->string('faw_oldest', 30)->nullable();
            $table->string('faw_latest', 30)->nullable();
            // SINOTRUK
            $table->integer('sinotruk_pick_up')->nullable();
            $table->integer('sinotruk_light_duty_truck')->nullable();
            $table->integer('sinotruk_medium_duty_truck')->nullable();
            $table->integer('sinotruk_heavy_duty_truck')->nullable();
            $table->integer('sinotruk_total')->nullable();
            $table->string('sinotruk_oldest', 30)->nullable();
            $table->string('sinotruk_latest', 30)->nullable();
            // EUROPEAN
            $table->integer('european_pick_up')->nullable();
            $table->integer('european_light_duty_truck')->nullable();
            $table->integer('european_medium_duty_truck')->nullable();
            $table->integer('european_heavy_duty_truck')->nullable();
            $table->integer('european_total')->nullable();
            $table->string('european_oldest', 30)->nullable();
            $table->string('european_latest', 30)->nullable();
            // Citeis of operation
            $table->tinyInteger('jeddah')->default(0);
            $table->tinyInteger('madina')->default(0);
            $table->tinyInteger('riyadh')->default(0);
            $table->tinyInteger('dammam')->default(0);
            $table->tinyInteger('al_khobar')->default(0);
            $table->tinyInteger('abha')->default(0);
            $table->tinyInteger('hafr_batin')->default(0);
            $table->tinyInteger('makkah')->default(0);
            $table->tinyInteger('alyth')->default(0);
            $table->tinyInteger('yanbu')->default(0);
            $table->tinyInteger('buraidah')->default(0);
            $table->tinyInteger('hail')->default(0);
            $table->tinyInteger('al_baha')->default(0);
            $table->tinyInteger('alqassim')->default(0);
            $table->tinyInteger('najran')->default(0);
            $table->tinyInteger('jizan')->default(0);
            $table->tinyInteger('khamis')->default(0);
            $table->tinyInteger('tabuk')->default(0);
            $table->tinyInteger('taif')->default(0);
            $table->tinyInteger('neom')->default(0);
            $table->tinyInteger('jubail')->default(0);
            $table->string('other_cities', 200)->nullable();

            $table->string('new_vehicle_inquiry', 100)->nullable();
            $table->string('vehicle_shelf_life', 20)->nullable();
            $table->string('coordinates', 100)->nullable();
            $table->string('payment_term_of_sales')->nullable();
            $table->longText('images')->nullable();
            // Customer Own Workshop
            $table->string('custownws_no_of_ws')->nullable();
            $table->string('custownws_no_of_tech')->nullable();
            $table->string('custownws_tech_languages', 100)->nullable();
            $table->string('custownws_oil_used', 100)->nullable();
            $table->tinyInteger('custownws_parts_genuine')->default(0);
            $table->tinyInteger('custownws_parts_non_genunine')->default(0);
            $table->tinyInteger('custownws_parts_mix')->default(0);
            $table->tinyInteger('custownws_parts_gray')->default(0);
            $table->string('custownws_parts_source', 200)->nullable();
            // Local Workshop
            $table->string('locws_noof_ws', 20)->nullable();
            $table->string('locws_name_of_ws', 20)->nullable();
            $table->string('locws_noof_tech', 20)->nullable();
            $table->string('locws_approx_price', 20)->nullable();
            $table->string('locws_parts_utilized', 20)->nullable();
            // HINO Dealer
            $table->string('hinod_city', 20)->nullable();
            $table->string('hinod_amc_lvl', 20)->nullable();
            // Last 12 months Transactions
            $table->string('l12m_parts_1half', 20)->nullable();
            $table->string('l12m_parts_2half', 20)->nullable();
            $table->string('l12m_parts_date', 20)->nullable();
            $table->string('l12m_service_1half', 20)->nullable();
            $table->string('l12m_service_2half', 20)->nullable();
            $table->string('l12m_service_date', 20)->nullable();
            $table->string('l12m_sales_1half', 20)->nullable();
            $table->string('l12m_sales_2half', 20)->nullable();
            $table->string('l12m_sales_date', 20)->nullable();
            $table->integer('dept_id')->nullable();
            $table->text('customer_voice')->nullable();
            $table->string('customer_satisfaction', 50)->nullable();
            $table->string('aftersales_contact_person', 100)->nullable();
            $table->string('title', 100)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('visit_date', 20)->nullable();

            // Action Recomended by TJT
            $table->longText('sales_note')->nullable();
            $table->unsignedBigInteger('sales_dept_person_id')->nullable()->comment('0-All');
            $table->longText('spare_note')->nullable();
            $table->unsignedBigInteger('spare_dept_person_id')->nullable()->comment('0-All');
            $table->longText('service_note')->nullable();
            $table->unsignedBigInteger('service_dept_person_id')->nullable()->comment('0-All');

            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
