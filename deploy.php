<?php
// deploy.php - Webhook para GitHub
$secret = 'PIEB_2025_SECRETO_DEPLOY';
$log_file = '/home/vianeyma/deploy.log';

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Método no permitido');
}

// Verificar firma de GitHub
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');
$computed_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($signature, $computed_signature)) {
    http_response_code(403);
    file_put_contents($log_file, date('[Y-m-d H:i:s]') . " Intento no autorizado\n", FILE_APPEND);
    die('Firma inválida');
}

// Ejecutar despliegue en segundo plano (para que GitHub no espere)
shell_exec('nohup /home/vianeyma/deploy.sh > /dev/null 2>&1 &');

echo "Despliegue iniciado correctamente";
file_put_contents($log_file, date('[Y-m-d H:i:s]') . " Webhook recibido - Despliegue iniciado\n", FILE_APPEND);
?>
