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
    Schema::create('deliveries', function (Blueprint $table) {
        $table->id();

        $table->foreignId('order_id')
            ->unique()
            ->constrained()
            ->cascadeOnDelete();

        $table->dateTime('scheduled_at');
        $table->string('status')->default('scheduled');
        $table->string('proof_path')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
