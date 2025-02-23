<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Clase poco optimizada y muy redundante. Arreglar.
 *
 * Clase utilizada para el envío de correos usando PHPMailer.
 *
 */
class Mail
{
    private $correo;
    private $nombre;
    private $token;

    // Constructor vacío
    public function __construct()
    {
        // No necesitas repositorios aquí de momento, pero podrías incluirlos si es necesario en el futuro.
    }

    /**
     * Inicializa los datos del correo.
     *
     * @param string $correo
     * @param string $nombre
     * @param string $token
     */
    public function initialize($correo, $nombre, $token)
    {
        $this->correo = $correo;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    /**
     * Enviar email general con PHPMailer.
     *
     * @param string $to       Correo destinatario.
     * @param string $subject  Asunto del correo.
     * @param string $body     Contenido del correo (HTML permitido).
     * @return bool
     */
    public function sendMail(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        try {
            // Configurar SMTP
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Port = config('mail.mailers.smtp.port');
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Opciones SSL personalizadas (desactivar validaciones)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            // Configuración del correo
            $mail->setFrom(config('from_address'), 'Your App Name');
            $mail->addAddress($to); // Agregar destinatario
            $mail->Subject = $subject; // Título del correo
            $mail->isHTML(true); // Enviar correo en formato HTML
            $mail->Body = $body; // Contenido

            // Enviar correo
            $mail->send();
            return true; // Retorna true en caso de éxito
        } catch (Exception $e) {
            Log::error("Error al enviar el correo: " . $e->getMessage());
            return false; // En caso de error se retorna false
        }
    }

    /**
     * Enviar correo de verificación.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|bool
     */
    public function sendVerificationEmail(User $user)
    {
        // Validar que el usuario no esté ya verificado.
        if ($user->hasVerifiedEmail()) {
            // Se recomienda retornar un valor bool aquí en lugar de un JSON (las respuestas HTTP deben ir en los controladores).
            return false;
        }

        // Generar un enlace de verificación manualmente.
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify', // Nombre de la ruta que maneja la verificación.
            Carbon::now()->addMinutes(60), // Tiempo de validez en minutos.
            [
                'id' => $user->id,           // ID del usuario.
                'hash' => sha1($user->email) // Hash del correo.
            ]
        );

        // Enviar correo con PHPMailer
        if ($this->sendVerificationEmailWithPHPMailer($user, $verificationUrl)) {
            // Retorna true si el envío fue exitoso.
            return true;
        }

        // Si algo falla, retornar false.
        return false;
    }

    /**
     * Configuración de correo de verificación específico con PHPMailer.
     *
     * @param User $user
     * @param string $verificationUrl
     * @throws Exception
     * @return bool
     */
    public function sendVerificationEmailWithPHPMailer($user, $verificationUrl): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Configurar SMTP
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Port = config('mail.mailers.smtp.port');
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Opciones SSL personalizadas (desactivar validaciones)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            // Configuración del remitente y destinatario
            $mail->setFrom(config('mail.from.address'), config('mail.from.name')); // Remitente
            $mail->addAddress($user->email, $user->name); // Destinatario

            // Configuración del correo
            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu correo electrónico';
            $mail->Body = "
                <p>Hello, {$user->name}!</p>
                <p>Please click the button below to verify your email address.</p>
                <p><a href=\"{$verificationUrl}\"
                      style=\"padding:10px 15px; background-color:#007bff; color:white; text-decoration:none; border-radius:5px;\">Verify Email Address</a></p>
                <p>If you did not create an account, no further action is required.</p>
                <br/>
                <p>Regards,<br/>Laravel</p>
                <p>If you're having trouble clicking the \"Verify Email Address\" button, copy and paste the URL below into your web browser:</p>
                <p>{$verificationUrl}</p>
            ";

            // Enviar correo
            $mail->send();

            return true; // Éxito al enviar
        } catch (Exception $e) {
            Log::error("Error al enviar el correo de verificación: " . $mail->ErrorInfo);
            return false; // Error
        }
    }

    /**
     * Envía un correo electrónico para el restablecimiento de contraseña al usuario especificado.
     *
     * @param User $user El usuario que solicita el restablecimiento de contraseña.
     * @param string $token El token único utilizado para generar el enlace de restablecimiento de contraseña.
     *
     * @return bool Retorna verdadero si el correo se envía correctamente, falso en caso contrario.
     */
    public function sendPasswordResetEmail(User $user, string $token)
    {
        $mail = new PHPMailer(true);

        try {
            // Configurar SMTP (mismo que otros métodos)
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Port = config('mail.mailers.smtp.port');
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Opciones SSL personalizadas
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            // Configuración del remitente y destinatario
            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
            $mail->addAddress($user->email, $user->name);

            // Generar la URL del enlace de restablecimiento
            $resetUrl = url('/reset-password/' . $token);

            // Configuración del correo (asunto y contenido)
            $mail->isHTML(true);
            $mail->Subject = 'Restablece tu contraseña';
            $mail->Body = "
            <p>Hola, {$user->name}:</p>
            <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
            <p>
                <a href=\"{$resetUrl}\" style=\"padding:10px 15px; background-color:#007bff; color:white; text-decoration:none; border-radius:5px;\">Restablecer Contraseña</a>
            </p>
            <p>Si no solicitaste esta acción, puedes ignorar este correo.</p>
            <br/>
            <p>Gracias,<br/>El equipo de Jornadas Ayala</p>
        ";

            // Enviar correo
            $mail->send();

            return true;
        } catch (Exception $e) {
            Log::error("Error al enviar el correo de restablecimiento de contraseña: " . $mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Enviar correo con detalles de las inscripciones realizadas.
     *
     * @param User $user Usuario al que se enviará el correo.
     * @param array $eventosSeleccionados  Lista de los eventos seleccionados por el usuario.
     * @return bool
     */
    public function sendInscriptionDetails($detalleJson, $total): bool
    {

        $user = auth()->user(); // Usuario autenticado

        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Port = config('mail.mailers.smtp.port');
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Opciones SSL personalizadas
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
            $mail->addAddress($user->email, $user->name); // Destinatario


            // Decodifico el JSON para obtener datos sobre los eventos
            $detalles = json_decode($detalleJson, true);

            $eventosIds = array_column($detalles, 'eventoId');

            // Uso esta vez la propia tabla para sacar lo datos. Podría haber usado el Json.
            $eventos = Evento::whereIn('id', $eventosIds)->get();

            // Inicializar coste total
            // $costeTotal = 0;

            // Maquetar el contenido HTML del correo
            $body = "<h1>Confirmación de Inscripción</h1>";
            $body .= "<p>Gracias por tu inscripción. Aquí tienes los detalles de los eventos:</p>";
            $body .= "<ul>";

            foreach ($eventos as $evento) {
                //$costeTotal += $evento->coste; // Sumar el coste de cada evento al total

                $body .= "<li><strong>Evento:</strong> {$evento->nombre} <br>";
                $body .= "<strong>Fecha y Hora:</strong> {$evento->fecha} - {$evento->hora}<br>";
                $body .= "<strong>Lugar:</strong> {$evento->lugar} <br>";
                //$body .= "<strong>Precio:</strong> {$evento->coste} €</li><br>";
            }

            $body .= "</ul>";
            $body .= "<p><strong>Coste Total:</strong> {$total} €</p>";
            $body .= "<p>Esperamos verte allí. ¡Gracias por inscribirte!</p>";

            // Crear el cuerpo del correo
            $mail->isHTML(true);
            $mail->Subject = 'Detalles de tus Inscripciones';
            $mail->Body = $body;


            // Enviar correo
            $mail->send();

            return true;
        } catch (Exception $e) {
            Log::error("Error al enviar los detalles de inscripción: " . $mail->ErrorInfo);
            return false;
        }
    }

}
