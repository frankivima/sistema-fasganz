$(document).ready(function() {
    $('#nombre').prop('disabled', true);
    $('#apellido').prop('disabled', true);
    $('#cedula_beneficiario').prop('disabled', true);
    $('#parentesco').prop('disabled', true);
    $('#fecha_nac').prop('disabled', true);
    $('#genero').prop('disabled', true);
    $('#register').prop('disabled', true);

    $('#verificar_cedula').click(function() {
        var cedulaEmpleado = $('#cedula_empleado').val();

        $.ajax({
            url: '../includes/verificar_cedula.php',
            method: 'POST',
            data: {
                cedulaEmpleado: cedulaEmpleado
            },
            success: function(response) {
                $('#verificacion_resultado').html(response);

                if (response.includes('Cédula válida')) {
                    $('#nombre').prop('disabled', false);
                    $('#apellido').prop('disabled', false);
                    $('#cedula_beneficiario').prop('disabled', false);
                    $('#parentesco').prop('disabled', false);
                    $('#fecha_nac').prop('disabled', false);
                    $('#genero').prop('disabled', false);
                    $('#register').prop('disabled', false);
                } else {
                    $('#nombre').prop('disabled', true);
                    $('#apellido').prop('disabled', true);
                    $('#cedula_beneficiario').prop('disabled', true);
                    $('#parentesco').prop('disabled', true);
                    $('#fecha_nac').prop('disabled', true);
                    $('#genero').prop('disabled', true);
                    $('#register').prop('disabled', true);
                }
            }
        });
    });
});
