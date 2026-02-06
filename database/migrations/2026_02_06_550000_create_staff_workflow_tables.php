<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Staff Table
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            // Types: employee, agent, driver, picker, manager
            $table->string('staff_type')->index();
            $table->decimal('salary_monthly', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete(); // For salary/advances
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Customer Addresses
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('title')->nullable(); // Home, Work
            $table->text('address_text');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 3. Parties (Unified Party Concept)
        // This allows invoice to link to a 'party' which can be a customer, staff, or generic walk-in
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            // Types: customer, employee, agent, driver, picker, walk_in
            $table->string('party_type')->index();
            $table->string('name');
            $table->string('phone')->nullable();
            // Links
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Sales Order Logs (Audit Trail)
        // Creating structure now, leaving sales_invoice_id nullable or strictly typed later
        Schema::create('sales_order_status_logs', function (Blueprint $table) {
            $table->id();
            // We use unsignedBigInteger manually because sales_invoices table doesn't exist yet
            $table->unsignedBigInteger('sales_invoice_id')->index();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_status_logs');
        Schema::dropIfExists('parties');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('staff');
    }
};
