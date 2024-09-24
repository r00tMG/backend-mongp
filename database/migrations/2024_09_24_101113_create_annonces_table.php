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
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gp_id')->constrained('users')->onDelete('cascade');
            $table->integer('kilos_disponibles');
            $table->dateTime('date_depart');
            $table->dateTime('date_arrivee');
            $table->text('description');
            $table->decimal('prix_du_kilo');
            $table->string('origin');
            $table->string('destination');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonces');
    }
};
