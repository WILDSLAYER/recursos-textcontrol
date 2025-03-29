<?php include 'conexion.php'; ?>

<?php
session_start();

// Generar un token único para prevenir duplicados
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}
$form_token = $_SESSION['form_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Eficiencia</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Calculadora de Eficiencia</h1>
    <form method="POST" action="">
        <input type="hidden" name="form_token" value="<?= $form_token ?>">
        <label for="modulo">Seleccionar Módulo:</label>
        <select id="modulo" name="modulo" required>
            <option value="Modulo 1">Modulo 1</option>
            <option value="Modulo 2">Modulo 2</option>
            <option value="Modulo 3">Modulo 3</option>
            <option value="Modulo 4">Modulo 4</option>
            <option value="Modulo 5">Modulo 5</option>
            <option value="Modulo 6">Modulo 6</option>
        </select><br><br>

        <label for="trabajadoras">Cantidad de trabajadoras en el módulo:</label>
        <input type="number" id="trabajadoras" name="trabajadoras" value="<?= $_POST['trabajadoras'] ?? '' ?>" required><br><br>

        <label for="sam">SAM (Standard Allowed Minutes):</label>
        <input type="number" id="sam" name="sam" step="0.001" value="<?= number_format((float)($_POST['sam'] ?? 0), 3, '.', '') ?>" required><br><br>

        <label for="prendas">Cantidad de prendas hechas en este lapso:</label>
        <input type="number" id="prendas" name="prendas" required><br><br>

        <label for="lapso">Lapso de tiempo:</label>
        <select id="lapso" name="lapso" required>
            <option value="6-7am">6:00 - 7:00 am</option>
            <option value="7-8am">7:00 - 8:00 am</option>
            <option value="8-9am">8:00 - 9:00 am</option>
            <option value="9-10am">9:00 - 10:00 am</option>
            <option value="10-11am">10:00 - 11:00 am</option>
            <option value="11-12pm">11:00 - 12:00 pm</option>
            <option value="12-1pm">12:00 - 1:00 pm</option>
            <option value="1-2pm">1:00 - 2:00 pm</option>
        </select><br><br>

        <label for="observacion">Observación (opcional):</label>
        <textarea id="observacion" name="observacion"></textarea><br><br>

        <button type="submit">Calcular Eficiencia</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Regenerar el token después de un envío exitoso
        $_SESSION['form_token'] = bin2hex(random_bytes(32));

        // Capturar el módulo seleccionado
        $modulo = $_POST['modulo'] ?? 'Modulo 1';

        // Validar y capturar los datos del formulario
        $trabajadoras = isset($_POST['trabajadoras']) ? (int)$_POST['trabajadoras'] : 0;
        $sam = isset($_POST['sam']) ? round((float)$_POST['sam'], 3) : 0;
        $prendas = isset($_POST['prendas']) ? (int)$_POST['prendas'] : 0;
        $lapso = $_POST['lapso'] ?? '';
        $observacion = $_POST['observacion'] ?? '';

        // Inicializar datos del módulo si no existen
        if (!isset($_SESSION['modulos'][$modulo])) {
            $_SESSION['modulos'][$modulo] = [
                'datos' => [],
                'unidadesRealesAcumuladas' => 0
            ];
        }

        // Validar que no se repita el lapso en el módulo seleccionado
        $isDuplicateLapso = false;
        foreach ($_SESSION['modulos'][$modulo]['datos'] as $dato) {
            if ($dato['lapso'] === $lapso) {
                $isDuplicateLapso = true;
                break;
            }
        }

        if ($isDuplicateLapso) {
            echo "<p style='color: red;'>Error: Ya existe un registro para el lapso de tiempo seleccionado en $modulo.</p>";
        } else {
            // Validar que SAM no sea 0 para evitar división por cero
            if ($sam > 0) {
                // Calcular las unidades reales (UR) para este lapso
                $unidadesReales = ceil(($trabajadoras * 60) / $sam + 2);

                // Hacer acumulativo el cálculo de unidades reales solo a partir del primer registro
                if (!empty($_SESSION['modulos'][$modulo]['datos'])) {
                    $unidadesReales += $_SESSION['modulos'][$modulo]['unidadesRealesAcumuladas'];
                }

                // Actualizar las acumulaciones
                $_SESSION['modulos'][$modulo]['unidadesRealesAcumuladas'] = $unidadesReales;

                // Calcular la eficiencia para este lapso
                $eficiencia = intval(($prendas / $unidadesReales) * 100);

                // Guardar los datos en el módulo seleccionado
                $_SESSION['modulos'][$modulo]['datos'][] = [
                    'lapso' => $lapso,
                    'trabajadoras' => $trabajadoras,
                    'sam' => $sam,
                    'unidadesReales' => $unidadesReales,
                    'prendas' => $prendas,
                    'eficiencia' => $eficiencia,
                    'observacion' => $observacion
                ];

                // Mostrar los resultados
                echo "<h2>Resultados para $modulo</h2>";
                echo "<p>Unidades Reales (UR) acumuladas hasta este lapso: " . $unidadesReales . "</p>";
            } else {
                echo "<p style='color: red;'>Error: El valor de SAM debe ser mayor a 0.</p>";
            }
        }
    }

    // Mostrar los datos en una tabla para cada módulo
    if (isset($_SESSION['modulos'])) {
        foreach ($_SESSION['modulos'] as $modulo => $data) {
            if (count($data['datos']) > 0) {
                echo "<h2>Resultados Acumulados para $modulo</h2>";
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                echo "<tr>
                        <th>Lapso</th>
                        <th>Trabajadoras</th>
                        <th>SAM</th>
                        <th>Unidades Reales</th>
                        <th>Prendas</th>
                        <th>Eficiencia (%)</th>
                        <th>Observación</th>
                        <th>Acciones</th>
                      </tr>";
                foreach ($data['datos'] as $index => $dato) {
                    echo "<tr>
                            <td>{$dato['lapso']}</td>
                            <td>{$dato['trabajadoras']}</td>
                            <td>" . number_format((float)$dato['sam'], 3, '.', '') . "</td>
                            <td>" . $dato['unidadesReales'] . "</td>
                            <td>{$dato['prendas']}</td>
                            <td>" . intval($dato['eficiencia']) . "%</td>
                            <td>" . htmlspecialchars($dato['observacion']) . "</td>
                            <td>
                                <form method='POST' action=''>
                                    <input type='hidden' name='modulo' value='$modulo'>
                                    <input type='hidden' name='index' value='$index'>
                                    <button type='submit' name='delete'>Eliminar</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            }
        }
    }
    // Procesar la eliminación del registro
if (isset($_POST['delete'])) {
    $modulo = $_POST['modulo'];
    $index = $_POST['index'];

    // Eliminar el registro del módulo correspondiente
    if (isset($_SESSION['modulos'][$modulo]['datos'][$index])) {
        unset($_SESSION['modulos'][$modulo]['datos'][$index]);

        // Reindexar el arreglo para evitar problemas con índices desordenados
        $_SESSION['modulos'][$modulo]['datos'] = array_values($_SESSION['modulos'][$modulo]['datos']);
    }

    // Recargar la página para reflejar los cambios
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
    
</body>
</html>