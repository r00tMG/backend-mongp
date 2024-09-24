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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('payment_intent_id')->nullable();
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pending');  // Statut de la commande (ex : pending, paid, canceled)
            $table->string('payment_status')->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->string('email');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('demande_id')->constrained('demandes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
