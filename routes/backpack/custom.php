<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('role', 'RoleCrudController');
    Route::crud('user', 'UserCrudController');
    Route::crud('evento', 'EventoCrudController');
    Route::crud('ponente', 'PonenteCrudController');
    Route::crud('tipo-inscripcion', 'TipoInscripcionCrudController');
    Route::crud('inscripcion', 'InscripcionCrudController');
    Route::crud('evento-ponente', 'EventoPonenteCrudController');
    Route::crud('asistente-evento', 'AsistenteEventoCrudController');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
