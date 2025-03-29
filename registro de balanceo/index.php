
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
    <title>Creacion de balanceo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .data-section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <h1>Creacion de balanceo</h1>
    <form method="POST" action="">
        <input type="hidden" name="form_token" value="<?= $form_token ?>">

        <!--estos son los datos principales de el balanceo-->

        <label for="modulo">Seleccionar Módulo:</label>
        <select id="modulo" name="modulo" required>
            <option value="Modulo 1">Modulo 1</option>
            <option value="Modulo 2">Modulo 2</option>
            <option value="Modulo 3">Modulo 3</option>
            <option value="Modulo 4">Modulo 4</option>
            <option value="Modulo 5">Modulo 5</option>
            <option value="Modulo 6">Modulo 6</option>
        </select><br><br>

        <label for="modulo">Seleccionar el Cliente:</label>
        <select id="modulo" name="modulo" required>
            <option value="Modulo 1">cliente 1</option>
            <option value="Modulo 2">cliente 2</option>
            <option value="Modulo 3">cliente 3</option>
            <option value="Modulo 4">cliente 4</option>
            <option value="Modulo 5">cliente 5</option>
            <option value="Modulo 6">cliente 6</option>
        </select><br><br>

        <label for="modulo">tipo de Prenda:</label>
        <select id="modulo" name="modulo" required>
            <option value="Modulo 1">camisa 1</option>
            <option value="Modulo 2">zapato 2</option>
            <option value="Modulo 3">nose 3</option>
        </select><br><br>

        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" value="<?= $_POST['fecha'] ?? '' ?>" required><br><br>

        <label for="sam_referencia">SAM de la Referencia:</label>
        <input type="number" id="sam_referencia" name="sam_referencia" step="0.001" value="<?= number_format((float)($_POST['sam_referencia'] ?? 0), 3, '.', '') ?>" required><br><br>

        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" value="<?= $_POST['cantidad'] ?? '' ?>" required><br><br>




        <label for="num_operadoras">Número de Operadoras:</label>
        <input type="number" id="num_operadoras" name="num_operadoras" value="<?= $_POST['num_operadoras'] ?? '' ?>" required><br><br>

        <label for="paqueteo">Paqueteo:</label>
        <input type="number" id="paqueteo" name="paqueteo" value="<?= $_POST['paqueteo'] ?? '' ?>" required><br><br>

        <label for="Pedido">Pedido:</label>
        <input type="number" id="Pedido" name="Pedido" value="<?= $_POST['Pedido'] ?? '' ?>" required><br><br>


        <button type="submit">Calcular Eficiencia</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Regenerar el token después de un envío exitoso
        $_SESSION['form_token'] = bin2hex(random_bytes(32));

        // Capturar el módulo seleccionado
        $modulo = $_POST['modulo'] ?? 'Modulo 1';

        // Validar y capturar los datos principales del balanceo
        $cliente = $_POST['cliente'] ?? '';
        $referencia = $_POST['referencia'] ?? '';
        $tipo_prenda = $_POST['tipo_prenda'] ?? '';
        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $sam_referencia = isset($_POST['sam_referencia']) ? round((float)$_POST['sam_referencia'], 3) : 0;
        $cant = isset($_POST['cant']) ? (int)$_POST['cant'] : 0;



        /* $num_operadoras = isset($_POST['num_operadoras']) ? (int)$_POST['num_operadoras'] : 0;
        $paqueteo = isset($_POST['paqueteo']) ? (int)$_POST['paqueteo'] : 0;
        $confiabilidad = isset($_POST['confiabilidad']) ? (int)$_POST['confiabilidad'] : 0;
        $pedido = isset($_POST['pedido']) ? (float)$_POST['pédido'] : 0 ; */

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
                        <th>Operari@</th>
                        <th>Operaciones</th>
                        <th>Maquinas</th>
                        <th>Sam de las operacions</th>
                        <th>Min necesarios</th>
                        <th>Sumatoria</th>
                        <th>Carga</th>
                        <th>Estado del tiempo</th>
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