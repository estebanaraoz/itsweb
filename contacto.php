<?php
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

function render_page(string $title, string $message, int $status = 200): void {
    http_response_code($status);
    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"es\">\n<head>\n<meta charset=\"UTF-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n<title>{$title}</title>\n<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n<link href=\"https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap\" rel=\"stylesheet\">\n<style>body{font-family:'Poppins',sans-serif;background:#f3f4f6;color:#1f2937;margin:0;padding:0;display:flex;align-items:center;justify-content:center;min-height:100vh;}main{background:#fff;padding:2.5rem;border-radius:1rem;box-shadow:0 10px 25px rgba(15,23,42,0.12);max-width:420px;text-align:center;}h1{font-size:1.75rem;margin-bottom:1rem;color:#C8102E;}p{font-size:1rem;line-height:1.6;margin-bottom:1.5rem;}a{display:inline-block;padding:0.75rem 1.5rem;border-radius:0.75rem;text-decoration:none;font-weight:600;background:#C8102E;color:#fff;}a:hover{background:#A50F1D;}</style>\n</head>\n<body>\n<main>\n<h1>{$title}</h1>\n<p>{$message}</p>\n<a href=\"index.html\">Volver al sitio</a>\n</main>\n</body>\n</html>";
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    render_page('Solicitud inválida', 'Para contactarnos completá el formulario del sitio.', 405);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    render_page('Dato faltante', 'Necesitamos un correo válido para ponernos en contacto. Volvé e intentá nuevamente.', 400);
    exit;
}

$to = 'administracion@itstech.com.ar';
$subject = 'Nuevo contacto desde itstech.com.ar';
$date = (new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->format('d/m/Y H:i');
$body = "Se registró un nuevo pedido de información desde el sitio web.\n\nCorreo: {$email}\nFecha y hora: {$date} (America/Argentina/Buenos_Aires)\n\nRecordatorio: respondé al contacto a la brevedad.";
$headers = [
    'From: notificaciones@itstech.com.ar',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . PHP_VERSION,
    'Content-Type: text/plain; charset=UTF-8'
];

$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

if ($sent) {
    render_page('¡Gracias por escribirnos!', 'Recibimos tu correo y en breve nos pondremos en contacto.');
} else {
    render_page('No pudimos enviar tu solicitud', 'Intentá nuevamente más tarde o escribinos directamente a administracion@itstech.com.ar.', 500);
}
