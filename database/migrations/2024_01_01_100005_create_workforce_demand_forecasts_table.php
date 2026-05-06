<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workforce_demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->string('trade');
            $table->string('location')->nullable();
            $table->date('forecast_date');
            $table->unsignedInteger('predicted_demand');
            $table->unsignedInteger('current_supply')->default(0);
            $table->json('inputs')->nullable();
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workforce_demand_forecasts');
    }
};
