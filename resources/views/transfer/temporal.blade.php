<script>
									$(document).ready(function() {

									});
								</script>
								<div class="col-md-10">
									<div class="task-header">
										<div class="form-group">
											<label for="producto" class="form-label">Buscar producto</label>
											<input type="hidden" id="centrocosto" name="centrocosto" value="{{ $dataTransfer[0]->bodega_origen_id }}">																			

											<!-- Campos ocultos para enviar datos adicionales -->
											<input type="hidden" id="lote_id" name="lote_id" value="">
											<input type="hidden" id="inventario_id" name="inventario_id" value="">
											<input type="hidden" id="stock_ideal" name="stock_ideal" value="">
											<input type="hidden" id="store_id" name="store" value="">
											<input type="hidden" id="store_name" name="store_name" value="">

											<select class="form-control form-control-sm select2Prod" name="producto" id="producto" required>
												<option value="">Seleccione el producto</option>
												@foreach ($results as $result)
												<option value="{{ $result['inventario_id'] }}"
													data-product-id="{{ $result['product_id'] }}"
													data-lote-id="{{ $result['lote_id'] }}"
													data-inventario-id="{{ $result['inventario_id'] }}"
													data-stock-ideal="{{ $result['stock_ideal'] }}"
													data-store-id="{{ $result['store_id'] }}"
													data-store-name="{{ $result['store_name'] }}"
													data-info="{{ $result['text'] }}">
													{{ $result['text'] }}
												</option>
												@endforeach
											</select>

											<span class="text-danger error-message"></span>
										</div>
									</div>
								</div>
