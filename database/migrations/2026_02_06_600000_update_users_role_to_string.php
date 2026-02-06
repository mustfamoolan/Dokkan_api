<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // altering enum is complex, easier to drop and re-add or change if using MySQL
            // But standard way to support new roles easily: change to string
            $table->string('role')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert is hard without exact list, simplified:
            // $table->enum('role', ['manager', 'supervisor', 'employee'])->change(); 
        });
    }
};
