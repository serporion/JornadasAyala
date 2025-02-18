<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            /* Problemas con las migraciones al estar ya creada. Aún así he preferido usarla.

            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
            */

            //$table->unsignedInteger('role_id');->nullable()->after('id'); //Luego tengo que indicar el rol de admin. E
            // Añade las otras columnas aquí
            //$table->string('token')->nullable()->after('role_id');
        });

            //DB::table('users')->update(['role_id' => 1]);


            // Luego, crea la nueva columna y las demás
            //$table->unsignedInteger('role_id')->nullable()->after('id');


        Schema::table('users', function (Blueprint $table) {
            // Finalmente, añade la clave foránea
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }


};
