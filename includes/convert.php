<?php

include "../includes/db.php";

// Consulta para contar los registros en la tabla pacientes
$sqlContarPacientes = "SELECT COUNT(*) as total FROM pacientes";
$resultadoContarPacientes = mysqli_query($conexion, $sqlContarPacientes);
$filaPacientes = mysqli_fetch_assoc($resultadoContarPacientes);
$totalPacientes = $filaPacientes['total'];

///////////////////////////////////////////////////////////////////////////

// Consulta para contar los registros en la tabla beneficiarios
$sqlContarBeneficiarios = "SELECT COUNT(*) as total FROM beneficiarios";
$resultadoContarBeneficiarios = mysqli_query($conexion, $sqlContarBeneficiarios);
$fila = mysqli_fetch_assoc($resultadoContarBeneficiarios);
$totalBeneficiarios = $fila['total'];

///////////////////////////////////////////////////////////////////////////

function formatCedula($cedula)
{
    // Verificar si la cédula es numérica y no es igual a "N/T"
    if (ctype_digit($cedula) && $cedula !== 'N/T') {
        // Agregar puntos cada 3 dígitos desde el final
        $len = strlen($cedula);
        $formatted = '';
        $count = 0;
        for ($i = $len - 1; $i >= 0; $i--) {
            $formatted = $cedula[$i] . $formatted;
            $count++;
            if ($count % 3 == 0 && $i != 0) {
                $formatted = '.' . $formatted;
            }
        }
        return $formatted;
    }
    // Devolver la cédula tal como está si no es numérica o es "N/T"
    return $cedula;
}

?>