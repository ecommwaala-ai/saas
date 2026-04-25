<?php

use App\Models\Leave;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default(Leave::TYPE_FULL_DAY);
            $table->date('date')->index();
            $table->text('reason')->nullable();
            $table->string('status')->default(Leave::STATUS_PENDING)->index();
            $table->timestamps();

            $table->index(['tenant_id', 'agent_id']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
