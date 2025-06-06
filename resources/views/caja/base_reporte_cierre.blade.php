<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Reporte Validación del Cierre de Caja</title>
  <style>
    /* Estilos Generales */
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f7f8;
      color: #333;
    }

    .container {
      width: 95%;
      max-width: 1200px;
      margin: 2rem auto;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 8px;
      overflow: hidden;
    }

    /* Encabezado Principal */
    .header {
      background: linear-gradient(to right, #007bff, #00c8ff);
      color: #fff;
      padding: 1.5rem;
      text-align: center;
    }

    .header h1 {
      margin: 0;
      font-size: 1.5rem;
      letter-spacing: 1px;
    }

    /* Sección de Información General */
    .info-section {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      padding: 1rem;
      background: #fafafa;
      border-bottom: 1px solid #ddd;
    }

    .info-item {
      flex: 1 1 200px;
      margin: 0.5rem 0;
      padding: 0.5rem;
      font-size: 0.95rem;
    }

    .info-item strong {
      color: #007bff;
    }

    /* Tabla */
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem;
    }

    thead {
      background: #007bff;
      color: #fff;
    }

    thead th {
      padding: 0.75rem;
      text-align: left;
      border-right: 1px solid #0060c0;
    }

    thead th:last-child {
      border-right: none;
    }

    tbody tr {
      border-bottom: 1px solid #ddd;
    }

    tbody tr:nth-child(even) {
      background: #f9f9f9;
    }

    tbody td {
      padding: 0.75rem;
      border-right: 1px solid #eee;
    }

    tbody td:last-child {
      border-right: none;
    }

    /* Fila de Totales */
    tfoot tr {
      background: #ffeb99;
      font-weight: bold;
    }

    tfoot td {
      padding: 0.75rem;
      border-right: 1px solid #eee;
    }

    tfoot td:last-child {
      border-right: none;
    }
  </style>
</head>
<body>

  <div class="container">
    <!-- Encabezado Principal -->
    <div class="header">
      <h1>REPORTE PARA VALIDACIÓN DEL CIERRE DE CAJA</h1>
    </div>

    <!-- Sección de Información General Caja-->
    <div class="info-section">
      <div class="info-item"><strong>Centro Costo:</strong> PlANTA</div>
      <div class="info-item"><strong>Cajero:</strong>MARIA</div>
      <div class="info-item"><strong>Cantidad facturas:</strong> caja->antidad_facturas </div>
      <div class="info-item"><strong>Fecha:</strong>caja->fecha_hora_inicio</div>
      <div class="info-item"><strong>Turno:</strong> caja->id</div>
      <div class="info-item"><strong>Diferencia:</strong> caja-> diferencia </div>
    </div>

    <!-- Tabla de Detalle de Facturas de la tabla sales -->
    <table>
      <thead>
        <tr>
          <th>NOMBRE CLIENTE</th>
          <th>#FACTURA</th>
          <th>TOTAL FACTURA</th>
          <th>EFECTIVO</th>
          <th>NEQUI</th>
          <th>DAVIPLATA</th>
          <th>CODIGO QR</th>
          <th>BANCOLOMBIA</th>
          <th>BBVA</th>
          <th>WOMPI</th>
          <th>DATAFONO</th>
          <th>BOLD</th>
          <th>CREDITO</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td> third_id name </td>
          <td> sale->consecutivo </td>
          <td> sale->total_valor_a_pagar </td>
          <td> sale->valor_a_pagar_efectivo </td>
          <td> sale->forma_pago_tarjeta_id->NEQUI</td>
          <td> sale->forma_pago_tarjeta_id->DAVIPLATA</td>
          <td> sale->forma_pago_tarjeta_id->CODIGO QR</td>
          <td> sale->forma_pago_tarjeta_id->BANCOLOMBIA</td>
          <td> sale->forma_pago_tarjeta_id->BBVA</td>
          <td> sale->forma_pago_tarjeta_id->WOMPI</td>
          <td> sale->forma_pago_tarjeta_id->DATAFONO</td>
          <td> sale->forma_pago_tarjeta_id->BOLD</td>
          <td> sale->forma_pago_credito_id->CREDITO</td>                   
        </tr>        
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2">TOTALES</td>
          <td>$4,000.00</td>
          <td>$1,000.00</td>
          <td>$500.00</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$1,000.00</td>
        </tr>
      </tfoot>
    </table>
  </div>

</body>
</html>
