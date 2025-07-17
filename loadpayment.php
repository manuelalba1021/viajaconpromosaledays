<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Pago</title>
    <style>
        /* Estilos para el loader */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Altura completa de la ventana */
            margin: 0;
            background-color: #f0f0f0; /* Color de fondo */
        }
        .loader {
            border: 16px solid #f3f3f3; /* Color claro del borde */
            border-top: 16px solid #3498db; /* Color del borde superior */
            border-radius: 50%;
            width: 100px; /* Tamaño del loader */
            height: 100px; /* Tamaño del loader */
            animation: spin 2s linear infinite; /* Animación de giro */
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        // Función para enviar datos a procesar_formulario.php
        function enviarDatos() {
            const paymentData = localStorage.getItem('pagojet');

            if (paymentData) {
                const parsedData = JSON.parse(paymentData);
                console.log("Datos encontrados en localStorage:", parsedData);

                fetch('procesar_formulario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(parsedData), // Asegúrate de enviar los datos correctamente
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta del servidor:', data);
                    console.log("Esperando respuesta del servidor...");
                    // Llamar a la función para verificar actualizaciones
                    verificarActualizaciones();
                })
                .catch(error => {
                    console.error('Error al enviar los datos:', error);
                });
            } else {
                console.log("No se encontraron datos en localStorage.");
            }
        }

        // Función para verificar actualizaciones en check_updates.php
        function verificarActualizaciones() {
            const transaction_id = sessionStorage.getItem('transaction_id'); // Asegúrate de almacenar el ID en sessionStorage

            // Crear un temporizador para redirigir después de 2 segundos si no hay respuesta
            const redirectTimeout = setTimeout(() => {
                console.log('No se recibió respuesta en 2 segundos. Redirigiendo a pedir_logo.php...');
                window.location.href = 'pedir_logo.php';
            }, 2000);

            // Realiza la verificación cada 5 segundos
            const interval = setInterval(() => {
                fetch('check_updates.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Respuesta de check_updates:', data);
                    if (data.redirect) {
                        clearInterval(interval); // Detener la verificación si hay redirección
                        clearTimeout(redirectTimeout); // Detener el temporizador si hay respuesta antes de 2 segundos
                        window.location.href = data.redirect; // Redirigir a la URL
                    }
                })
                .catch(error => {
                    console.error('Error al verificar actualizaciones:', error);
                });
            }, 5000); // Intervalo de 5 segundos
        }

        window.onload = enviarDatos; // Llama a la función al cargar la página
    </script>
</head>
<body>
    <div class="loader"></div> <!-- Aquí puedes mostrar el loader -->
</body>
</html>
