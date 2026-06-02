<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('lists')->cascadeOnDelete();
            $table->foreignId('paid_by')->constrained('users');
            $table->unsignedBigInteger('cart_total')->default(0);
            $table->unsignedBigInteger('total_real')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_closures');
    }
};
