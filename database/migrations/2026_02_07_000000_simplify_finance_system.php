<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['cash_account_id']);
            $table->dropColumn('cash_account_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['cash_account_id']);
            $table->dropColumn('cash_account_id');
        });

        Schema::dropIfExists('cash_accounts');
    }

    public function down(): void
    {
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['cash', 'bank'])->default('cash');
            $table->foreignId('account_id')->constrained('accounts')->restrictOnDelete();
            $table->string('currency')->default('IQD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->foreignId('cash_account_id')->nullable()->constrained('cash_accounts')->restrictOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('cash_account_id')->nullable()->constrained('cash_accounts')->restrictOnDelete();
        });
    }
};
