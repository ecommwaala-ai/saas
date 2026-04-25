<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->date('shift_date')->index();
            $table->dateTime('clock_in');
            $table->dateTime('clock_out')->nullable();
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'agent_id', 'clock_out']);
            $table->index(['tenant_id', 'shift_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
