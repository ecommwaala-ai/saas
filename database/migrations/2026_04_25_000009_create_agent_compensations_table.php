<?php

use App\Models\AgentCompensation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_compensations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default(AgentCompensation::TYPE_SALARY);
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->json('incentive_details')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'agent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_compensations');
    }
};
