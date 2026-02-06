<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Sales Invoices
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // SI-Timestamp
            $table->enum('source_type', ['office', 'agent']);
            $table->foreignId('source_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Party Link
            $table->foreignId('party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();

            // Payment & Delivery
            $table->enum('payment_type', ['cash', 'credit']);
            $table->date('due_date')->nullable();

            $table->boolean('delivery_required')->default(false);
            $table->foreignId('delivery_address_id')->nullable()->constrained('customer_addresses')->nullOnDelete();
            // Store snapshot of location to preserve history if address changes/deletes
            $table->text('delivery_address_text')->nullable();
            $table->decimal('delivery_lat', 10, 8)->nullable();
            $table->decimal('delivery_lng', 11, 8)->nullable();

            // Financials
            $table->decimal('subtotal_iqd', 15, 2)->default(0);
            $table->decimal('discount_iqd', 15, 2)->default(0);
            $table->decimal('total_iqd', 15, 2)->default(0);
            $table->decimal('paid_iqd', 15, 2)->default(0);
            $table->decimal('remaining_iqd', 15, 2)->default(0); // Virtual column logic usually, but stored for ease

            // Workflow Status
            $table->enum('status', [
                'draft',
                'pending_approval',
                'approved',
                'preparing',
                'prepared',
                'assigned_to_driver',
                'out_for_delivery',
                'delivered',
                'canceled',
                'returned'
            ])->default('draft');

            // Workflow Actors
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('prepared_by_staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->foreignId('driver_staff_id')->nullable()->constrained('staff')->nullOnDelete();

            $table->timestamp('delivered_at')->nullable();

            // Accounting Link
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Sales Invoice Lines
        Schema::create('sales_invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();

            $table->decimal('qty', 15, 4);
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('unit_factor', 15, 4)->default(1);

            $table->decimal('price_iqd', 15, 2);
            $table->decimal('line_total_iqd', 15, 2);

            // Auditing Cost usually from Avg Cost at the time of PREPARING (stock dedication)
            $table->decimal('cost_iqd_snapshot', 15, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Sales Returns
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no')->unique(); // SR-Timestamp
            $table->foreignId('sales_invoice_id')->nullable()->constrained('sales_invoices')->restrictOnDelete();

            $table->date('return_date');

            $table->decimal('total_iqd', 15, 2)->default(0);
            $table->enum('status', ['draft', 'posted', 'canceled'])->default('draft');

            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Sales Return Lines
        Schema::create('sales_return_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();

            $table->decimal('qty', 15, 4);
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('unit_factor', 15, 4)->default(1);

            $table->decimal('price_iqd', 15, 2);
            $table->decimal('line_total_iqd', 15, 2);

            // For calculating COGS reversal
            $table->decimal('cost_iqd_snapshot', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_return_lines');
        Schema::dropIfExists('sales_returns');
        Schema::dropIfExists('sales_invoice_lines');
        Schema::dropIfExists('sales_invoices');
    }
};
