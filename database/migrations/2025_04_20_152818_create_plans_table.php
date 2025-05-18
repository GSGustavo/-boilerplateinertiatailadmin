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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->decimal("price")->comment("0 para plano grátis.");
            $table->integer("days")->comment("0 para plano infinito.");;
            $table->boolean("status")->default(true);
            $table->boolean("internal")->comment("É um plano interno, como um plano exclusivo e que não vai aparecer no site.")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
