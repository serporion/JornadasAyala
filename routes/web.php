<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\PonenteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Mail; Symphony
//use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\InscripcionController;
use Illuminate\Support\Facades\Storage;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('landing');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->get('/pagina-verificacion', function () {
    return view('pagina-verificacion');
})->name('verification.custom-prompt');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//Ruta para probar correo directamente sin controlador. Sigue dando fallos SSL.
/*
Route::get('/test-mail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Este es un correo de prueba', function ($message) {
            $message->to('oscardelgadohuertas@hotmail.com')
                ->subject('Correo de Prueba desde Laravel');
        });

        return 'Correo enviado exitosamente';
    } catch (\Exception $e) {
        return 'Error enviando el correo: ' . $e->getMessage();
    }
});
*/

Route::middleware('guest')->group(function () {

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->
    name('password.request');


    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->
    name('password.email');


    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->
    name('password.reset');


    Route::post('reset-password', [NewPasswordController::class, 'store'])->
    name('password.update');
});



Route::get('/inscripcion', [InscripcionController::class, 'index'])->name('inscripcion.index');


Route::middleware('auth')->group(function () {

    Route::post('/inscripcion', [InscripcionController::class, 'store'])->name('inscripcion.store');
    Route::post('/inscripcion/confirmacion', [InscripcionController::class, 'confirmacion'])->name('inscripcion.confirmacion');
});

/*
Route::post('/inscripcion.gestionarTransaccion', [InscripcionController::class, 'gestionarTransaccion'])
    ->name('inscripcion.gestionarTransaccion');
*/




Route::post('/inscripcion/iniciarProcesoPago', [InscripcionController::class, 'iniciarProcesoPago'])
    ->name('inscripcion/iniciarProcesoPago');

/*
Route::get('/paypal/iniciar-pago/{pedidoId}', [PayPalController::class, 'iniciarPago'])->
name('paypal.iniciarPago');
*/

Route::get('/paypal/iniciar-pago', [PayPalController::class, 'iniciarPago'])->
name('paypal.iniciarPago');

Route::get('/PayPal/pagoExitoso', [PayPalController::class, 'pagoExitoso'])->
name('PayPal.pagoExitoso');


Route::get('/paypal/cancel', [PayPalController::class, 'pagoCancelado'])->
name('paypal.pagoCancelado');


Route::get('ponentesWeb', [PonenteController::class, 'indexMostrar'])->name('ponentesWeb.indexMostrar');



/*
Route::get('/imagen/{filename}', [PonenteController::class, 'mostrarImagen'])->
name('imagen.ponente');
*/

Route::get('/mostrar-imagen/{filename}', [PonenteController::class, 'mostrarImagen'])
    ->where('filename', '.*')
    ->name('mostrar-imagen');


require __DIR__.'/auth.php';
