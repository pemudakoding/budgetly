<?php

use App\Models\IncomeBudget;
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
        Schema::create('income_budget_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IncomeBudget::class);
            $table->string('description');
            $table->unsignedBigInteger('amount')->default(0);
            $table->date('revenue_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_budget_histories');
    }
};
