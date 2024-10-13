<?php

use App\Models\Income;
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
        Schema::create('income_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Income::class);
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('month');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_budgets');
    }
};
