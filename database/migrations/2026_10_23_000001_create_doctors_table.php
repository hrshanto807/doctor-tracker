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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialization');
            $table->string('hospital');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->date('joined_date')->default(now());
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            // Query performance optimization indexes (Searching & Filtering)
            $table->index(['specialization', 'hospital']);
            $table->index('joined_date');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
