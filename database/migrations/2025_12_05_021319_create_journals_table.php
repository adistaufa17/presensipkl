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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('presence_id')->constrained('presences')->onDelete('cascade');

            $table->date('date');
            $table->string('title', 191);
            $table->text('description');

            $table->string('photo_path')->nullable();

            $table->timestamps();

            // Index untuk performa query
            $table->index('date');
            $table->index(['user_id', 'date']);
            $table->index('presence_id');
            
            // Constraint: satu presence hanya bisa punya satu jurnal
            $table->unique('presence_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};