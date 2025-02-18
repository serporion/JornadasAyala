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
        Schema::create('eventos_ponentes', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('speaker_id');

            $table->primary(['event_id', 'speaker_id']);

            $table->foreign('event_id')->references('id')->on('eventos')->onDelete('cascade');
            $table->foreign('speaker_id')->references('id')->on('ponentes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_ponentes');
    }
};

