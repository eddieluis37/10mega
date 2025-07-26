<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Compra - Megachorizos</title>
    <style>       

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 10mm;
            background: white;
        }

        .purchase-order-template {
            max-width: 21cm;
            margin: 0 auto;
        }

        /* Header Section */
        .header-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .logo-section {
            display: table-cell;
            width: 120px;
            vertical-align: top;
        }

        .mega-logo {
          /*   background-color: #cc0000; */
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            width: 100px;
        }

        .mega-text {
            display: block;
            font-size: 24px;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 2px;
        }

        .carnes-frias {
            display: block;
            font-size: 10px;
            font-weight: normal;
            line-height: 1;
        }

        .company-info {
            display: table-cell;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }

        .company-details p {
            margin: 2px 0;
            font-size: 10px;
        }

        .order-info {
            display: table-cell;
            width: 160px;
            text-align: center;
            vertical-align: top;
        }

        .order-header h2 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 8px 0;
            border: 2px solid #000;
            padding: 8px;
            background-color: #f0f0f0;
        }

        .order-number {
            font-size: 14px;
            font-weight: bold;
        }

        .order-number .number {
            border: 1px solid #000;
            padding: 4px 8px;
            display: inline-block;
            min-width: 80px;
            text-align: center;
            margin-left: 10px;
        }

        /* Supplier and Date Section */
        .supplier-date-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .supplier-section {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }

        .supplier-section h3 {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 8px 0;
        }

        .supplier-row {
            margin-bottom: 3px;
            clear: both;
        }

        .supplier-row .label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
            float: left;
        }

        .supplier-row .value {
            display: inline-block;
            margin-left: 85px;
        }

        .date-contact-section {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            text-align: right;
        }

        .date-info h3 {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 8px 0;
        }

        .contact-row {
            margin-bottom: 3px;
            text-align: right;
        }

        .contact-row .label {
            font-weight: bold;
            margin-right: 8px;
        }

        /* Delivery Section */
        .delivery-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            padding: 8px;
        }

        .delivery-left {
            display: table-cell;
            width: 65%;
            vertical-align: top;
        }

        .delivery-row {
            margin-bottom: 3px;
            clear: both;
        }

        .delivery-row .label {
            display: inline-block;
            width: 140px;
            font-weight: bold;
            float: left;
        }

        .delivery-row .value {
            display: inline-block;
            margin-left: 145px;
        }

        .delivery-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: top;
        }

        .schedule-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
            font-size: 9px;
        }

        .schedule-header {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .schedule-note {
            text-align: center;
        }

        .page-info {
            font-size: 10px;
            font-weight: bold;
        }

        /* Products Table */
        .products-table {
            margin-bottom: 15px;
        }

        .products-table table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .products-table th,
        .products-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        .products-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }

        .col-no {
            width: 5%;
        }

        .col-description {
            width: 40%;
            text-align: left;
        }

        .col-ref {
            width: 10%;
        }

        .col-cant {
            width: 10%;
        }

        .col-valor {
            width: 12%;
        }

        .col-iva {
            width: 8%;
        }

        .col-total {
            width: 15%;
        }

        .products-table td {
            font-size: 10px;
            height: 120px;
        }

        /* Bottom Section */
        .bottom-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .observations {
            display: table-cell;
            width: 60%;
            border: 1px solid #000;
            padding: 8px;
            height: 60px;
            vertical-align: top;
        }

        .observations h4 {
            margin: 0;
            font-size: 11px;
            font-weight: bold;
        }

        .totals-section {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            padding-left: 20px;
        }

        .totals-row {
            border: 1px solid #000;
            margin-bottom: 2px;
            padding: 4px 8px;
            display: table;
            width: 100%;
        }

        .totals-row .label {
            display: table-cell;
            font-weight: bold;
            text-align: left;
            width: 60%;
        }

        .totals-row .value {
            display: table-cell;
            text-align: right;
            width: 40%;
        }

        .total-final {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        /* Notes Section */
        .notes-section {
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        .notes-section h4 {
            margin: 0 0 8px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .notes-section ol {
            margin: 0;
            padding-left: 20px;
            font-size: 9px;
        }

        .notes-section li {
            margin-bottom: 3px;
            line-height: 1.3;
        }

        /* Clear floats */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        @page {
            size: letter;
            margin: 10mm;
        }
    </style>
</head>

<body>
    <div class="purchase-order-template">
        <!-- Header Section -->
        <div class="header-section">
            <div class="logo-section">
                <div class="mega-logo">
                    <!-- <img src="{{ public_path('assets/img/logo/logo-mega.jpg') }}" alt="" class="invoice-logo" width="100%" style="vertical-align: top; padding-top: -38px; position: relative"> -->
                    <img src="{{ public_path('assets/img/logo/logo-mega.png') }}" alt="" class="invoice-logo" width="100%" style="vertical-align: top; padding-top: -38px; position: relative">
                </div>
            </div>

            <div class="company-info">
                <h1 class="company-name">MEGACHORIZOS S.A.S.</h1>
                <div class="company-details">
                    <p>NIT 900.490.684-3</p>
                    <p>CL 35 SUR N° 70 B - 79</p>
                    <p>Contacto : (601) 461 42 66</p>
                    <p>Celular 320 943 21 89</p>
                    <p>www.megachorizos.co</p>
                </div>
            </div>

            <div class="order-info">
                <div class="order-header">
                    <h2>FACTURA COMPRA</h2>
                    <div class="order-number">
                        <span>Nro.</span>
                        <span class="number">{{ $comp->id ?? '–' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier and Date Section -->
        <div class="supplier-date-section">
            <div class="supplier-section">
                <h3>Datos del Proveedor:</h3>
                <div class="supplier-details">
                    <div class="supplier-row clearfix">
                        <span class="label">Nombre :</span>
                        <span class="value">{{ $comp->third?->name ?? '–' }}</span>
                    </div>
                    <div class="supplier-row clearfix">
                        <span class="label">Nit :</span>
                        <span class="value">{{$comp->third?->identification ?? '–'}}</span>
                    </div>
                    <div class="supplier-row clearfix">
                        <span class="label">Dirección :</span>
                        <span class="value">{{$comp->third?->direccion ?? '–'}}</span>
                    </div>
                    <div class="supplier-row clearfix">
                        <span class="label">Ciudad :</span>
                        <span class="value">{{ $supplier['city'] ?? 'BOGOTÁ' }}</span>
                    </div>
                </div>
            </div>

            <div class="date-contact-section">
                <div class="date-info">
                    <h3>FECHA: {{ strtoupper($fechaCierre) }}</h3>
                </div>
                <div class="contact-info">
                    <div class="contact-row">
                        <span class="label">Teléfonos :</span>
                        <span class="value">{{ $comp->third?->celular ?? '–' }}</span>
                    </div>
                    <div class="contact-row">
                        <span class="label">Contacto:</span>
                        <span class="value">{{ $comp->third?->nombre_contacto ?? '–' }}</span>
                    </div>
                    <div class="contact-row">
                        <span class="label">E-mail:</span>
                        <span class="value">{{ $comp->third?->correo ?? '–' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Information -->
        <div class="delivery-section">
            <div class="delivery-left">
                <div class="delivery-row clearfix">
                    <span class="label">Fecha de Entrega:</span>
                    <span class="value">{{ $comp->fecha_ingreso ?? '–' }}</span>
                </div>
                <div class="delivery-row clearfix">
                    <span class="label">Dirección de entrega:</span>
                    <span class="value">{{ $comp->centroCosto?->direccion ?? '–' }}</span>
                </div>
                <div class="delivery-row clearfix">
                    <span class="label">Elaborado por:</span>
                    <span class="value">{{ $comp->user?->name ?? '–' }}</span>
                </div>
                <div class="delivery-row clearfix">
                    <span class="label">Forma de Pago:</span>
                    <span class="value">{{ $comp->formapago?->nombre ?? '–' }}</span>
                </div>
                <div class="delivery-row clearfix">
                    <span class="label">Área de Solicitud:</span>
                    <span class="value">{{ $comp->centroCosto?->name ?? '–' }}</span>
                </div>
            </div>

            <div class="delivery-right">
                <div class="schedule-box">
                    <div class="schedule-header">Horario de recepción: 6:00 am a 1:00 pm.</div>
                    <div class="schedule-note">Fuera de este horario no se recibirá ningún pedido</div>
                </div>
                <div class="page-info">Página 1 de 1</div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="products-table">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">N°</th>
                        <th class="col-description">DESCRIPCION PRODUCTO</th>
                        <th class="col-ref">CODE</th>
                        <th class="col-cant">CANT</th>
                        <th class="col-valor">$.UNID</th>
                        <th class="col-valor">$.DESC</th>
                        <th class="col-iva">$.IVA</th>
                        <th class="col-iva">$.I.S</th>
                        <th class="col-iva">$.I.C</th>
                        <th class="col-total">$.TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($compDetails) && count($compDetails) > 0)
                    @foreach($compDetails as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: left;">{{$item->nameprod}}</td>
                        <td>{{$item->code}}</td>
                        <td>{{$item->peso}}</td>
                        <td>{{number_format($item->pcompra ,0, ',', '.' )}}</td>
                        <td>{{number_format($item->descuento ,0, ',', '.' )}}</td>
                        <td>{{number_format($item->iva ,0, ',', '.' )}}</td>
                        <td>{{number_format($item->otro_imp ,0, ',', '.' )}}</td>
                        <td>{{number_format($item->impoconsumo ,0, ',', '.' )}}</td>
                        <td>{{number_format($item->subtotal ,0, ',', '.' )}}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td>1</td>
                        <td style="text-align: left;">CAJA DE CARTON REF CHORIZO CON</td>
                        <td></td>
                        <td>2,000.00</td>
                        <td>3,185.00</td>
                        <td>19.00</td>
                        <td>6,370,000.00</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <div class="observations">
                <h4>OBSERVACIONES:</h4>
                <span class="value">{{ $comp->observacion ?? '–' }}</span>
            </div>

            <div class="totals-section">
                <div class="totals-row">
                    <span class="label">SUBTOTAL</span>
                    <span class="value">{{ number_format($total_subtotal ,0, ',', '.' )}}</span>
                </div>
                <div class="totals-row">
                    <span class="label">DESCUENTO</span>
                    <span class="value">{{ number_format($total_descuento ,0, ',', '.' )}}</span>
                </div>
                <div class="totals-row">
                    <span class="label">I.V.A.</span>
                    <span class="value">{{ number_format($total_iva ,0, ',', '.' )}}</span>
                </div>
                <div class="totals-row">
                    <span class="label">I.S</span>
                    <span class="value">{{ number_format($total_otro_impuesto ,0, ',', '.' )}}</span>
                </div>
                <div class="totals-row">
                    <span class="label">I.C</span>
                    <span class="value">{{ number_format($total_impoconsumo ,0, ',', '.' )}}</span>
                </div>
                <div class="totals-row total-final">
                    <span class="label">T O T A L E S</span>
                    <span class="value">{{ number_format($total ,0, ',', '.' )}}</span>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="notes-section">
            <h4>NOTA AL PROVEEDOR:</h4>
            <ol>
                <li>Garantizar el cumplimiento de la normatividad HSEQ</li>
                <li>Todos los proveedores deben traer copia de parafiscales vigentes, cofia, botas punta de acero, tapaboca.</li>
                <li>Traer orden de compra, remisión o factura como soporte de entrega.</li>
                <li>Esta orden de compra está sujeta a las condiciones comerciales acordadas entre las partes</li>
                <li>Para consultas comunicarse con el área de compras</li>
                <li>Traer certificado de calidad o trazabilidad de la materia prima, concepto sanitario del vehículo, manipulación del transporte</li>
            </ol>
        </div>
    </div>
</body>

</html>