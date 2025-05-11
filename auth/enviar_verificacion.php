<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Asegurar que BASE_URL esté definido
if (!defined('BASE_URL')) {
    // Esta es una forma básica, si tu proyecto está en /tienda
    // Deberías tener una forma más robusta en un config global
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST']; // ej: 127.0.0.1 o localhost
    // Asume que la carpeta del proyecto es el segundo segmento si se accede desde XAMPP (ej /tienda/)
    $project_folder = explode('/', trim($_SERVER['REQUEST_URI'], '/'))[0];
    define('BASE_URL', '/' . $project_folder);
}


function enviarCorreoVerificacion($destinatario, $nombre, $token) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP de Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'llensaaudioverificacion@gmail.com'; // Tu correo real
        $mail->Password = 'wufj mima mbaj gsbz'; // La contraseña de app
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';


        // Remitente y destinatario
        $mail->setFrom('llensaaudioverificacion@gmail.com', 'LlensaAudio');
        $mail->addAddress($destinatario, $nombre);

        // Contenido
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        // Construir el enlace de verificación correctamente
        $enlace_verificacion = $protocolo . $host . BASE_URL . "/auth/verificar.php?token=" . $token;


        $mail->isHTML(true);
        $mail->Subject = 'Confirma tu correo - LlensaAudio';
        $mail->Body = "
            <h3>Hola $nombre,</h3>
            <p>Gracias por registrarte en LlensaAudio. Para activar tu cuenta, hacé clic en el siguiente enlace:</p>
            <p><a href='$enlace_verificacion' style='padding:10px 15px; background-color:#a972ff; color:white; text-decoration:none; border-radius:5px;'>Verificar correo</a></p>
            <p>Si no creaste esta cuenta, podés ignorar este mensaje.</p>
            <br>
            <p>Saludos,<br>El equipo de LlensaAudio</p>
        ";
        $mail->AltBody = "Hola $nombre,\n\nGracias por registrarte en LlensaAudio. Para activar tu cuenta, copiá y pegá el siguiente enlace en tu navegador:\n$enlace_verificacion\n\nSi no creaste esta cuenta, podés ignorar este mensaje.\n\nSaludos,\nEl equipo de LlensaAudio";


        $mail->send();
        return true;
    } catch (Exception $e) {
        // En desarrollo, puedes mostrar el error. En producción, loggear.
        // error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return "Error al enviar: {$mail->ErrorInfo}";
    }
}
?>