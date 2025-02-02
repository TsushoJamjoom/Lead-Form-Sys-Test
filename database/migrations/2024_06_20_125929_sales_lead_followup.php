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
        Schema::create('sales_lead_followup', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_id');
            $table->string('comment', 200);
            $table->integer('value');
            $table->timestamps();
            $table->foreign('sales_lead_id')->references('id')->on('sales_leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_lead_followup');
    }
};
