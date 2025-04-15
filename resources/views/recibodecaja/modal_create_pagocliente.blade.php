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
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="cliente" class="form-label">Buscar cliente</label>
                                    <select class="form-control form-control-sm select2Cliente" name="cliente" id="cliente" required>
                                        <option value="">Seleccione el cliente</option>
                                        @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">
                                            {{ $cliente->name }} (Deuda: ${{ number_format($cliente->deuda_x_cobrar, 0, ',', '.') }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="task-header">
                                <div class="form-group">
                                    <label for="formaPago" class="form-label">Buscar forma de pago</label>
                                    <select class="form-control form-control-sm select2FormaPago" name="formaPago" id="formaPago" required>
                                        <option value="">Seleccione la forma pago</option>
                                        @foreach ($formapagos as $p)
                                        <option value="{{$p->id}}">{{$p->nombre}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4"></div>
                        <div class="table-responsive mt-3">
                            <form method="POST" action="/customerPayment">
                                @csrf
                                <table id="tablePagoCliente" class="table table-striped mt-1">
                                    <thead class="text-white" style="background: #3B3F5C">
                                        <tr>
                                            <th class="table-th text-white">ID</th>
                                            <th class="table-th text-white">F_VENTA</th>
                                            <th class="table-th text-white">IDENTIDAD</th>
                                            <th class="table-th text-white">DOCUMENTO</th>
                                            <th class="table-th text-white">F_VENCE</th>
                                            <th class="table-th text-white">DIAS_MORA</th>
                                            <th class="table-th text-white">VR.DEUDA</th>
                                            <th class="table-th text-white">VALOR.PAGO</th>
                                            <th class="table-th text-white">NVO.SALDO</th>
                                            <th class="table-th text-white">ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Las filas se cargarán dinámicamente -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <!-- Se generan 10 celdas; en este ejemplo se muestran totales en columnas 7 (VR.DEUDA), 8 (VR.PAGO) y 9 (NVO.SALDO) -->
                                            <th>Totales</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td id="totalDeuda">$ 0</td>
                                            <td id="totalPago">$ 0</td>
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
