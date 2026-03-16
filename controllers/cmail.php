<?php
// ... otras funciones como envmail() irán aquí ...
// Usamos las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Función para enviar correos electrónicos usando PHPMailer.
 * Se asume que 'cmail.php' está dentro de 'controllers/'.
 * * @param string $destinatario Correo electrónico del receptor.
 * @param string $asunto Asunto del correo.
 * @param string $cuerpo_html Contenido del correo en formato HTML.
 * @return bool Retorna TRUE si el envío fue exitoso, FALSE en caso de error.
 */
function envmail($destinatario, $asunto, $cuerpo_html){
    
    // --- 1. CONFIGURACIÓN DEL SERVIDOR SMTP ---
    // ⚠️ ¡IMPORTANTE! MODIFICA estos valores con tu configuración real de correo.
    // Consulta a tu proveedor de hosting (CPanel/Plesk) o usa una contraseña de aplicación si es Gmail.
    define('SMTP_HOST', 'smtp.gmail.com');        // EJ: 'smtp.gmail.com'
    define('SMTP_USER', 'stockp548@gmail.com');     // EJ: 'soporte@stockpilot.com'
    define('SMTP_PASS', 'qadq pgjn tral typi');       // La contraseña de aplicación o token
    define('SMTP_PORT', 587);                          // Puerto: 587 (TLS) o 465 (SSL)
    define('SMTP_SECURE', PHPMailer::ENCRYPTION_STARTTLS); // Usar ENCRYPTION_SMTPS para puerto 465

    $mail = new PHPMailer(true); // El 'true' habilita las excepciones
    
    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE; 
        $mail->Port       = SMTP_PORT;
        
        // El servidor que envía el correo
        $mail->setFrom(SMTP_USER, 'StockPilot - Soporte');
        
        // Destinatario
        $mail->addAddress($destinatario);

        // Contenido del correo
        $mail->isHTML(true); // Establecer el formato del correo a HTML
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo_html;
        $mail->AltBody = strip_tags($cuerpo_html); 
        
        // Establecer idioma español (la ruta sale de controllers/ a vendor/)
        $mail->setLanguage('es', '../vendor/phpmailer/phpmailer/language/');
        
        // Envío
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Manejo y registro de errores
        error_log("Error al enviar el correo a $destinatario. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Genera la plantilla HTML para el correo de olvido de contraseña.
 * * @param string $nomusu Nombre del usuario (para el saludo).
 * @param string $emausu Correo del usuario (para la referencia).
 * @param string $keyolv Token de seguridad generado.
 * @return string Retorna el cuerpo del correo en formato HTML.
 */
function plaOlvCon($nomusu, $emausu, $keyolv) {
    // ⚠️ Asegúrate de que tu función de envío de correo (como mail() o PHPMailer)
    // también especifique el encabezado Content-Type como 'text/html; charset=UTF-8'.

    $url_base = "http://localhost/stockpilot/"; // <<-- ¡MODIFICA ESTA URL CON TU RUTA REAL!
    $url_destino = $url_base . "index.php?pg=reset"; 

    // Inicio del HTML con codificación UTF-8
    $txt = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Recuperación de Contraseña - StockPilot</title>
        </head>
        <body>
            <div style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; background-color: #f0f2f5; padding: 20px; text-align: center;">
                
                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08); overflow: hidden;">
                    
                    <div style="background-color: #2c3e50; color: #ffffff; padding: 30px 20px;">
                        <h1 style="margin: 0; font-size: 32px; font-weight: 300; letter-spacing: 1px;">StockPilot</h1>
                        <p style="margin-top: 5px; font-size: 18px; opacity: 0.9;">Recuperación de Contraseña</p>
                    </div>

                    <div style="padding: 40px 30px; text-align: center;"> 
                        
                        <h2 style="color: #34495e; margin-top: 0; font-size: 24px; font-weight: 500; margin-bottom: 30px;">Solicitud de Recuperación</h2>
                        
                        <p style="color: #7f8c8d; line-height: 1.7; margin-bottom: 25px;">
                            Estimado/a <strong>' . $nomusu . '</strong>:
                        </p>
                        
                        <p style="color: #7f8c8d; line-height: 1.7; margin-bottom: 25px;">
                            Hemos recibido una solicitud para restablecer la contraseña de tu cuenta asociada al correo <strong>' . $emausu . '</strong>.
                        </p>

                        <p style="color: #7f8c8d; line-height: 1.7;">
                            Para continuar, haz clic en el botón de abajo. Si no fuiste tú quien solicitó este cambio, puedes ignorar este correo sin problemas.
                        </p>
                        
                        <div style="margin: 40px 0;">
                            <form action="' . $url_destino . '" method="POST" target="_blank">
                                <button type="submit" style="cursor: pointer; background-color: #16a085; color: #fff; padding: 15px 35px; border-radius: 6px; font-weight: bold; border: none; font-size: 18px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 3px 6px rgba(22, 160, 133, 0.4);">
                                    Recuperar Contraseña
                                </button>
                                
                                <input type="hidden" name="ko" value="' . $keyolv . '">
                                <input type="hidden" name="m1" value="' . $emausu . '">
                            </form>
                        </div>

                        <div style="background-color: #fcf8e3; border: 1px solid #fbeed5; border-left: 5px solid #f0ad4e; padding: 15px; border-radius: 5px; font-size: 14px; color: #8a6d3b; margin-top: 30px; display: inline-block; text-align: left;">
                            <strong>Aviso Importante:</strong> Este enlace de restablecimiento expira en **24 horas**.
                            <br>Si no realizaste esta solicitud, por favor, ignora este correo.
                        </div>

                    </div>
                    
                    <div style="padding: 20px 30px; border-top: 1px solid #ecf0f1; font-size: 13px; color: #bdc3c7; background-color: #f7f9fa;">
                        <p style="margin: 0;">Atentamente,</p>
                        <p style="margin: 5px 0 0 0;">Equipo de Desarrollo **StockPilot**.</p>
                    </div>

                </div>
                
                <div style="margin-top: 20px; font-size: 11px; color: #b0b0b0;">
                    <p style="margin: 0;">Este es un mensaje generado automáticamente. Por favor, no responda a esta dirección de correo.</p>
                </div>

            </div>
        </body>
        </html>
    ';
    
    return $txt;
}
// ... otras funciones como envmail() irán aquí ...
?>