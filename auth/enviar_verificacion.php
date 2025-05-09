<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function enviarCorreoVerificacion($destinatario, $nombre, $token) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP de Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'llensaaudioverificacion@gmail.com'; // Tu correo real
        $mail->Password = 'wufj mima mbaj gsbz'; // La contraseña de app, NO la real
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('llensaaudioverificacion@gmail.com', 'LlensaAudio');
        $mail->addAddress($destinatario, $nombre);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Confirma tu correo - LlensaAudio';
        $mail->Body = "
            <h3>Hola $nombre,</h3>
            <p>Gracias por registrarte. Para activar tu cuenta, hacé clic en el siguiente enlace:</p>
            <p><a href='https://http://127.0.0.1/tienda/auth//verificar.php?token=$token'>Verificar correo</a></p>
            <p>Si no creaste esta cuenta, ignorá este mensaje.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Error al enviar: {$mail->ErrorInfo}";
    }
}
?>
