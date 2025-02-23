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

        Schema::create('ponentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255)->notNull();
            $table->text('fotografia')->nullable()->comment('Ruta del archivo almacenado'); // Flexible para rutas largas
            $table->string('area_experiencia', 255)->nullable();
            $table->string('red_social', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ponentes');
    }
};
