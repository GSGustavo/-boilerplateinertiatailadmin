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
        Schema::create('subscription_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained();
            $table->foreignId('plan_id')->constrained();
            $table->decimal("price")->comment("Valor copiado do plano na hora do pagamento, pois a pessoa pagará este valor para sempre.");

            $table->timestamp("paid_at")->comment("Data do pagamento")->nullable();

            $table->timestamp("start_on")->comment("Início do plano");
            $table->timestamp("end_on")->comment("Término do plano. Nulo para plano infinito")->nullable();
            $table->boolean("status")->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_renewals');
    }
};
