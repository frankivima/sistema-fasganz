<?php
// Fecha a encriptar
$fecha = "2025/04/01";

// Clave de encriptación
$clave = 'LUVV';

// Algoritmo y modo de encriptación
$algoritmo = 'AES-128-CBC';

// Vector de inicialización (IV)
$iv = random_bytes(16); // Genera un vector de inicialización aleatorio de 16 bytes

// Encriptar la fecha
$fecha_encriptada = openssl_encrypt($fecha, $algoritmo, $clave, 0, $iv);

// Mostrar la fecha encriptada
echo "Fecha encriptada: " . $fecha_encriptada . PHP_EOL;

// Mostrar el vector de inicialización
echo "Vector de inicialización (IV): " . base64_encode($iv) . PHP_EOL;
?>
