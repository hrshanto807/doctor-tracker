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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->string('phone');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->enum('condition', ['Critical', 'Stable', 'Under Observation', 'Recovered'])->default('Stable');
            $table->date('admission_date')->default(now());
            $table->string('room')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Query optimization indexes (Searching, Filtering & Aggregation)
            $table->index('doctor_id');
            $table->index('condition');
            $table->index('admission_date');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
