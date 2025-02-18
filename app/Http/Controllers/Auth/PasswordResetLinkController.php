<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Services\Mail;

class PasswordResetLinkController extends Controller
{

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Encuentra el usuario por su correo
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No se encontró ningún usuario con ese correo']);
        }

        // Generar el token de restablecimiento
        $token = Password::getRepository()->create($user);

        // Llamar al servicio Mail para enviar el enlace
        if ($this->mail->sendPasswordResetEmail($user, $token)) {
            return back()->with('status', '¡Se ha enviado el enlace de restablecimiento a tu correo!');
        }

        return back()->withErrors(['email' => 'No se pudo enviar el correo de restablecimiento. Inténtalo más tarde.']);
    }
}
