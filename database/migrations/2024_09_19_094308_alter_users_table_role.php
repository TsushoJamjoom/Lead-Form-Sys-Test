<?php

use App\Helpers\AppHelper;
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
        $roles = array_keys(AppHelper::getRoles());
        $roles[] = ['TJT'];
        Schema::table('users', function (Blueprint $table) use($roles) {
            $table->enum('role', $roles)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $roles = array_keys(AppHelper::getRoles());
        Schema::table('users', function (Blueprint $table) use($roles) {
            $table->enum('role', $roles)->change();
        });
    }
};
