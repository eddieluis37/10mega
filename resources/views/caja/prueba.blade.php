<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Cobros y Caja</title>
</head>
<body>

    <button id="btnCustomerPayment">Registrar Pago Cliente</button>
    <button id="btnSupplierPayment">Registrar Pago Proveedor</button>
    <button id="btnOpenCaja">Abrir Caja</button>
    <button id="btnCloseCaja">Cerrar Caja</button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para registrar pago de clientes (Entrada de Dinero)
            async function registerCustomerPayment() {
                const data = {
                    sale_ids: [1, 2], // IDs de las ventas a crédito
                    amount: 150000,
                    details: [
                        {
                            third_id: 5,
                            quantity: 1,
                            price: 150000,
                            porc_desc: 0,
                            descuento: 0,
                            porc_iva: 19,
                            iva: 28500,
                            total_bruto: 150000,
                            total: 178500
                        }
                    ],
                    formapagos_id: 1,
                    caja_id: 1,
                    observations: "Pago total del abono"
                };

                try {
                    const response = await fetch('/api/customer-payment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    });
                    const resData = await response.json();
                    console.log('Respuesta Pago Cliente:', resData);
                } catch (err) {
                    console.error('Error registrando pago de cliente:', err);
                }
            }

            // Función para registrar pago a proveedor (Salida de Dinero)
            async function registerSupplierPayment() {
                const data = {
                    cuenta_por_pagars_id: 1,
                    amount: 200000,
                    details: [
                        {
                            third_id: 6,
                            quantity: 1,
                            price: 200000,
                            porc_desc: 0,
                            descuento: 0,
                            porc_iva: 19,
                            iva: 38000,
                            total_bruto: 200000,
                            total: 238000
                        }
                    ],
                    formapagos_id: 2,
                    caja_id: 1,
                    observations: "Pago total a proveedor"
                };

                try {
                    const response = await fetch('/api/supplier-payment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    });
                    const resData = await response.json();
                    console.log('Respuesta Pago Proveedor:', resData);
                } catch (err) {
                    console.error('Error registrando pago de proveedor:', err);
                }
            }

            // Función para abrir caja
            async function openCaja() {
                const data = {
                    centrocosto_id: 1,
                };

                try {
                    const response = await fetch('/api/caja/open', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    });
                    const resData = await response.json();
                    console.log('Respuesta Apertura de Caja:', resData);
                } catch (err) {
                    console.error('Error al abrir caja:', err);
                }
            }

            // Función para cerrar caja
            async function closeCaja() {
                const data = {
                    caja_id: 1,
                };

                try {
                    const response = await fetch('/api/caja/close', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    });
                    const resData = await response.json();
                    console.log('Respuesta Cierre de Caja:', resData);
                } catch (err) {
                    console.error('Error al cerrar caja:', err);
                }
            }

            // Vincular funciones a los botones
            document.getElementById('btnCustomerPayment').addEventListener('click', registerCustomerPayment);
            document.getElementById('btnSupplierPayment').addEventListener('click', registerSupplierPayment);
            document.getElementById('btnOpenCaja').addEventListener('click', openCaja);
            document.getElementById('btnCloseCaja').addEventListener('click', closeCaja);
        });
    </script>

</body>
</html>
