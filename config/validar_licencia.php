<?php
// Función para validar la licencia
function validarLicencia() {
    // URL de la API de WorldTimeAPI
    $url = "http://worldtimeapi.org/api/ip";

    // Intentar obtener la fecha desde la API
    $response = @file_get_contents($url);

    // Verificar si se pudo obtener la fecha desde la API
    if ($response !== false) {
        // Decodificar la respuesta JSON
        $data = json_decode($response, true);
        $fecha_actual = new DateTime($data['datetime']);
    } else {
        // Si falla, obtener la fecha actual del servidor
        $fecha_actual = new DateTime(date('Y-m-d H:i:s'));
    }

    // Leer el archivo de texto con la cadena encriptada y el vector de inicialización
    $archivo = 'C:\\xampp\\htdocs\\sistema-fasganz\\config\\licencia.txt'; 
    $contenido = file_get_contents($archivo);

    // Separar la cadena encriptada y el vector de inicialización por líneas
    $lineas = explode("\n", $contenido);

    // Obtener la cadena encriptada y el vector de inicialización
    $cadena_encriptada = trim(str_replace('Cadena encriptada:', '', $lineas[0]));
    $iv = trim(str_replace('Vector de inicialización (IV):', '', $lineas[1]));

    // Clave de encriptación
    $clave = 'LUVV';

    // Desencriptar la cadena utilizando el IV
    $fecha_desencriptada = openssl_decrypt(base64_decode($cadena_encriptada), 'aes-128-cbc', $clave, OPENSSL_RAW_DATA, base64_decode($iv));

    // Convertir la fecha desencriptada en un objeto DateTime
    $fecha_limite = new DateTime($fecha_desencriptada);

    return $fecha_actual <= $fecha_limite; 
}
?>
