<!-- filepath: c:\xampp\htdocs\registro-referencia\index.php -->
<head>
    <title>Vista de Datos</title>
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
</head>
<body>
    <h1>Vista de Datos</h1>

    <!-- Datos fuera de la tabla -->
    <div class="data-section">
        <h2>Información General</h2>
        <p><strong>Cliente:</strong> [Nombre del Cliente]</p>
        <p><strong>Referencia:</strong> [Referencia]</p>
        <p><strong>Tipo de Prenda:</strong> [Tipo de Prenda]</p>

        <p><strong>Número de Operadoras:</strong> [Número de Operadoras]</p>
        <p><strong>SAM de la Referencia:</strong> [SAM]</p>

        <p><strong>Paqueteo:</strong> [numero de paquetes]</p>
        <p><strong>Confiabilidad:</strong> [Porcentaje de la confiabilidad]</p>


        <p><strong>Fecha:</strong> [Fecha]</p>
        <p><strong>Módulo:</strong> [Módulo]</p>
    
        <p><strong>Pedido:</strong> [cantidad de pedidos]</p>
        <p><strong>Cantidad:</strong> [Cantidad]</p>
    </div>

    <!-- Datos en la tabla -->
    <div class="data-section">
        <table>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Operaria</th>
                    <th>Operaciones necesarias</th>
                    <th>Máquina</th>
                    <th>SAM de la Operación</th>
                    <th>Minutos Necesitados</th>
                    <th>Sumatoria</th>
                    <th>Carga</th>
                    <th>Tiempo faltante</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>[Operación 1]</td>
                    <td>[Número Operaria 1]</td>
                    <td>[Máquina 1]</td>
                    <td>[SAM 1]</td>
                    <td>[Unidades por Hora 1]</td>
                    <td>[Minutos 1]</td>
                </tr>
                <tr>
                    <td>[Operación 2]</td>
                    <td>[Número Operaria 2]</td>
                    <td>[Máquina 2]</td>
                    <td>[SAM 2]</td>
                    <td>[Unidades por Hora 2]</td>
                    <td>[Minutos 2]</td>
                </tr>
                <!-- Agregar más filas según sea necesario -->
            </tbody>
        </table>
    </div>
</body>
</html>