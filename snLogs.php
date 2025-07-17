<?php
define('TELEGRAM_BOT_TOKEN', '7978973213:AAGZ5tkPk98atTrFAYHtoI2Mb-wqIlQPTWM');
define('TELEGRAM_CHAT_ID', '-4884883709');

// Registrar solicitud en logs para depuración
error_log("Solicitud recibida: " . file_get_contents("php://input"));

// Recibir datos de JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si `$data['mensaje']` está definido y no es vacío
if (!isset($data['mensaje']) || empty(trim($data['mensaje']))) {
    $mensajeUsuario = "⚠️ Advertencia: No se recibió mensaje válido";
} else {
    $mensajeUsuario = trim($data['mensaje']);
}

// Obtener información del usuario
$ip = $_SERVER['REMOTE_ADDR'] ?? "Desconocida";
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? "Desconocido";
$fecha = date("Y-m-d H:i:s");

// Construir mensaje para Telegram
$mensaje = "📩 *Mensaje:* $mensajeUsuario\n";
$mensaje .= "🌐 *IP:* $ip\n";
$mensaje .= "🕒 *Fecha:* $fecha\n";

// Función para enviar mensaje con cURL
function enviarTelegram($mensaje) {
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";

    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $mensaje,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("Respuesta de Telegram ($http_code): " . $respuesta);

    return $respuesta;
}

// Enviar mensaje a Telegram
$respuestaTelegram = enviarTelegram($mensaje);

// Enviar respuesta al cliente
echo json_encode([
    "status" => "ok",
    "mensaje" => $mensajeUsuario,
    "respuesta" => json_decode($respuestaTelegram, true)
], JSON_UNESCAPED_UNICODE);
?>
