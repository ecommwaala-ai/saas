<?php

use App\Models\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('contact_info');
            $table->decimal('sale_amount', 12, 2);
            $table->string('product_service')->nullable();
            $table->string('status')->default(Sale::STATUS_PENDING)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_deleted')->default(false)->index();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['tenant_id', 'agent_id', 'is_deleted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
