<style>
    .table-responsive {
        margin: 0 auto;
        /* Center the table */
    }

    .table {
        border-collapse: collapse;
        /* Optional: For better table appearance */
    }

    .table th {      
        text-align: center;
    }
    .table td {
        padding: 8px;
        /* Adjust padding for table cells */
        text-align: right;
        /* Align text in table cells */
        border: 1px solid #ddd;
        /* Optional: Add borders to cells */
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="connect-sorting-content">
            <div class="card simple-title-task ui-sortable-handle">
                <div class="card-body">
                    <div class="btn-toolbar justify-content-between">
                        <div>
                            <input type="hidden" value="0" name="productloteId" id="productloteId">
                            <input type="hidden" value="1" name="store_id" id="store_id">
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="cliente" class="form-label">Buscar cliente</label>
                                    <select class="form-control form-control-sm select2Cliente" name="cliente" id="cliente" required>
                                        <option value="">Seleccione el cliente</option>
                                        @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">
                                            {{ $cliente->name }} (Deuda: ${{ number_format($cliente->total_deuda, 0, ',', '.') }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6"></div>
                        <div class="table-responsive mt-3" style="overflow-x: auto;">
                            <form method="POST">
                                @csrf
                                <table id="tablePagoCliente" class="table table-striped mt-1" style="width: 100%;">
                                    <thead class="text-white" style="background: #3B3F5C">
                                        <tr>
                                            <th class="table-th text-white">ID</th>
                                            <th class="table-th text-white">F_VENTA</th>
                                            <th class="table-th text-white">IDENTY</th>
                                            <th class="table-th text-white">DOCUMENT</th>
                                            <th class="table-th text-white">F_VENCE</th>
                                            <th class="table-th text-white">D.M</th>
                                            <th class="table-th text-white">VR.DEUDA</th>
                                            <th class="table-th text-white">FORMA.PAGO</th>
                                            <th class="table-th text-white">VALORES.PAGADO</th>
                                            <th class="table-th text-white">NVO.SALDO</th>
                                            <th class="table-th text-white">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Las filas se cargarán dinámicamente -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Totales</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td id="totalDeuda">$ 0</td>
                                            <td></td>
                                            <td id="totalPago" align="right">$ 0</td>
                                            <td id="totalSaldo">$ 0</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div>
                    </div><!-- /.btn-toolbar -->
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div><!-- /.connect-sorting-content -->
    </div><!-- /.col-sm-12 -->
</div>