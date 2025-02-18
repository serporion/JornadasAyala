<?php

namespace App\Listeners;

//use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Queue\InteractsWithQueue;

use App\Services\Mail;
use Illuminate\Auth\Events\Registered;

class EnvioCorreoBienvenida
{
    private Mail $mail;

    /**
     * Inyectar tu servicio de PHPMailer
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Manejar los eventos de envío de correo.
     */
    public function handle(Registered $event)
    {
        $user = $event->user;

        // Configurar el contenido del correo
        //$subject = 'Bienvenido a la Plataforma';
        //$body = "<!--<h1>¡Hola, {$user->name}!</h1>
                 //<p>Gracias por registrarte. Haz clic en el siguiente enlace para confirmar tu cuenta:</p>
                 //<a href='" . url('/verify-email') . "'>Verifica tu cuenta aquí</a>";

        // Llamar al servicio Mail
        //$this->mail->sendMail($user->email, $subject, $body);
        $this->mail->sendVerificationEmail($user);
    }
}

