<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Mail; Symphony
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\InscripcionController;

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
    // Ruta para mostrar la página "Olvidé mi Contraseña"
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->
    name('password.request');

    // Ruta para enviar el link de contraseña al correo
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->
    name('password.email');

    // Página para reiniciar la contraseña
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->
    name('password.reset');

    // Ruta para almacenar la nueva contraseña
    Route::post('reset-password', [NewPasswordController::class, 'store'])->
    name('password.update');
});

Route::get( '/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])->
name('verification.verify');




Route::get('/paypal/iniciar-pago/{pedidoId}', [PayPalController::class, 'iniciarPago'])->
    name('paypal.iniciarPago');

Route::get('/paypal/success', [PayPalController::class, 'pagoExitoso'])->
    name('paypal.pagoExitoso');

Route::get('/paypal/cancel', [PayPalController::class, 'pagoCancelado'])->
    name('paypal.pagoCancelado');





Route::get('/inscripcion', [InscripcionController::class, 'index'])->name('inscripcion.index');
Route::post('/inscripcion', [InscripcionController::class, 'store'])->name('inscripcion.store');

require __DIR__.'/auth.php';
