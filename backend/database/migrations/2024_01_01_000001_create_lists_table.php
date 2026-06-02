<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('budget')->nullable(); // en pesos CLP
            $table->char('code', 5)->unique();               // código de acceso "AB12C"
            $table->enum('status', ['active', 'paid'])->default('active');
            $table->unsignedBigInteger('closed_at')->nullable();
            $table->unsignedBigInteger('created_at')->nullable();
            $table->unsignedBigInteger('updated_at')->nullable();

            $table->index('code');
            $table->index(['owner_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lists');
    }
};
