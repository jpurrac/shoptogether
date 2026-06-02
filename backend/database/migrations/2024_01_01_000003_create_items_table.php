<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('lists')->cascadeOnDelete();
            $table->foreignId('added_by')->constrained('users');
            $table->string('name');
            $table->unsignedSmallInteger('qty')->default(1);
            $table->unsignedBigInteger('price')->default(0); // en pesos CLP
            $table->boolean('checked')->default(false);
            $table->unsignedBigInteger('checked_at')->nullable();
            $table->string('checked_by')->nullable();
            $table->unsignedBigInteger('created_at')->nullable();
            $table->unsignedBigInteger('updated_at')->nullable();

            $table->index(['list_id', 'checked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
