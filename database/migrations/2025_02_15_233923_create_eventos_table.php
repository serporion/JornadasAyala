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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['conferencia', 'taller'])->notNull();
            $table->string('nombre', 255)->notNull();
            $table->text('descripcion')->nullable();
            $table->date('fecha')->notNull();
            $table->time('hora_inicio')->notNull();
            $table->integer('duracion')->notNull()->default(55);
            $table->string('lugar', 255)->notNull();
            $table->integer('cupo_maximo')->notNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
