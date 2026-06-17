<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cross_impact_responses', function (Blueprint $table) {
            $table->id();
            $table->json('matrix_data'); // Stores the 2D array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cross_impact_responses');
    }
};