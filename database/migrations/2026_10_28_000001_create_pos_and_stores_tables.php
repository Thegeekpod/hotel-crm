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
        // ==========================================
        // 1. POS SALES MODULE TABLES
        // ==========================================
        
        if (!Schema::hasTable('pos_items')) {
            Schema::create('pos_items', function (Blueprint $table) {
                $table->id();
                $table->string('code')->nullable();
                $table->string('name');
                $table->string('category')->default('Main Course'); // Breakfast, Starters, Main Course, Beverages, Desserts
                $table->decimal('price', 10, 2)->default(0.00);
                $table->string('emoji')->default('🍽️');
                $table->text('description')->nullable();
                $table->string('status')->default('Active'); // Active, Inactive, Out of Stock
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pos_orders')) {
            Schema::create('pos_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique(); // POS-2026-0001
                $table->string('order_type')->default('Room Dining'); // Room Dining, Table Dine-in, Takeaway
                $table->string('table_number')->nullable();
                $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
                $table->string('customer_name')->nullable();
                $table->string('room_number')->nullable();
                $table->decimal('subtotal', 10, 2)->default(0.00);
                $table->decimal('tax_percentage', 5, 2)->default(12.00);
                $table->decimal('tax_amount', 10, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->string('billing_type')->default('Charged to Room Folio'); // Charged to Room Folio, Cash Paid, Digital UPI / Card Paid
                $table->string('payment_status')->default('Pending'); // Charged to Room Folio, Paid, Pending
                $table->string('status')->default('Received'); // Received, In Kitchen, Completed, Cancelled
                $table->string('server_name')->nullable();
                $table->text('special_instructions')->nullable();
                $table->foreignId('room_service_order_id')->nullable()->constrained('room_service_orders')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pos_order_items')) {
            Schema::create('pos_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pos_order_id')->constrained('pos_orders')->cascadeOnDelete();
                $table->foreignId('pos_item_id')->nullable()->constrained('pos_items')->nullOnDelete();
                $table->string('item_name');
                $table->decimal('unit_price', 10, 2)->default(0.00);
                $table->integer('quantity')->default(1);
                $table->decimal('line_total', 10, 2)->default(0.00);
                $table->string('instructions')->nullable();
                $table->timestamps();
            });
        }

        // ==========================================
        // 2. STORES & INVENTORY MODULE TABLES
        // ==========================================

        if (!Schema::hasTable('store_categories')) {
            Schema::create('store_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('Active'); // Active, Inactive
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_suppliers')) {
            Schema::create('store_suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('gst_number')->nullable();
                $table->text('address')->nullable();
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_items')) {
            Schema::create('store_items', function (Blueprint $table) {
                $table->id();
                $table->string('sku')->unique();
                $table->string('name');
                $table->foreignId('store_category_id')->nullable()->constrained('store_categories')->nullOnDelete();
                $table->foreignId('default_supplier_id')->nullable()->constrained('store_suppliers')->nullOnDelete();
                $table->string('unit')->default('Pcs'); // Pcs, Bottles, Kg, Kits, Liters, Boxes, Cans
                $table->decimal('unit_cost', 10, 2)->default(0.00);
                $table->integer('current_stock')->default(0);
                $table->integer('min_threshold')->default(10);
                $table->string('location')->nullable();
                $table->text('description')->nullable();
                $table->string('status')->default('Optimal'); // Optimal, Low Stock Alert, Out of Stock
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_movements')) {
            Schema::create('store_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_item_id')->constrained('store_items')->cascadeOnDelete();
                $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT'])->default('IN');
                $table->integer('quantity');
                $table->integer('stock_before');
                $table->integer('stock_after');
                $table->string('reference_no')->nullable();
                $table->string('issued_to_department')->nullable();
                $table->string('issued_to_person')->nullable();
                $table->decimal('unit_cost', 10, 2)->nullable();
                $table->text('remarks')->nullable();
                $table->string('performed_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_requisitions')) {
            Schema::create('store_requisitions', function (Blueprint $table) {
                $table->id();
                $table->string('requisition_no')->unique(); // PR-2026-0001
                $table->foreignId('store_item_id')->constrained('store_items')->cascadeOnDelete();
                $table->foreignId('store_supplier_id')->nullable()->constrained('store_suppliers')->nullOnDelete();
                $table->integer('quantity_requested')->default(1);
                $table->decimal('estimated_cost', 10, 2)->default(0.00);
                $table->string('priority')->default('Normal'); // Low, Normal, High, Urgent
                $table->string('status')->default('Pending'); // Pending, Approved, Ordered, Received, Cancelled
                $table->string('requested_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('required_by_date')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_requisitions');
        Schema::dropIfExists('store_movements');
        Schema::dropIfExists('store_items');
        Schema::dropIfExists('store_suppliers');
        Schema::dropIfExists('store_categories');
        Schema::dropIfExists('pos_order_items');
        Schema::dropIfExists('pos_orders');
        Schema::dropIfExists('pos_items');
    }
};
