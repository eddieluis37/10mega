<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\NotaCredito;
use App\Models\NotaCreditoDetalle;
use App\Models\MovimientoInventario;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;
use Exception;

class PartialReturn extends Component
{
    public $saleId;
    public $sale;
    public $saleDetails;
    public $returns = []; // Arreglo para almacenar las cantidades a devolver por cada detalle
    public $store_id;     // Se utiliza para actualizar el inventario

    // Se recibe el id de la venta al montar el componente
    public function mount($saleId)
    {
        $this->saleId = $saleId;
        $this->sale = Sale::findOrFail($saleId);
        $this->saleDetails = SaleDetail::where('sale_id', $saleId)->get();

        // Inicializamos el arreglo returns para cada detalle
        foreach ($this->saleDetails as $detail) {
            $this->returns[$detail->id] = 0;
        }
        $this->store_id = $this->sale->store_id;
    }

    // Método para procesar la devolución parcial
    public function submitReturn()
    {
        DB::beginTransaction();
        try {
            $totalNota = 0;
            // Recorrer cada detalle para validar la cantidad a devolver y calcular el total de la nota
            foreach ($this->returns as $saleDetailId => $returnQuantity) {
                if ($returnQuantity > 0) {
                    $detail = SaleDetail::findOrFail($saleDetailId);
                    if ($returnQuantity > $detail->quantity) {
                        throw new Exception("La cantidad a devolver para el producto {$detail->nameprod} supera la cantidad vendida.");
                    }
                    $totalNota += $detail->price * $returnQuantity;
                }
            }

            if ($totalNota == 0) {
                session()->flash('error', 'No se ha indicado ninguna cantidad a devolver.');
                return;
            }

            // Crear la cabecera de la Nota de Crédito
            $notaCredito = NotaCredito::create([
                'sale_id' => $this->saleId,
                'user_id' => auth()->id(),
                'total'   => $totalNota,
                'status'  => 'active',
            ]);

            // Procesar cada detalle para crear la nota de crédito, actualizar el inventario y ajustar la venta
            foreach ($this->returns as $saleDetailId => $returnQuantity) {
                if ($returnQuantity > 0) {
                    $detail = SaleDetail::findOrFail($saleDetailId);

                    // Crear detalle de la Nota de Crédito
                    NotaCreditoDetalle::create([
                        'notacredito_id' => $notaCredito->id,
                        'product_id'     => $detail->product_id,
                        'quantity'       => $returnQuantity,
                        'price'          => $detail->price,
                    ]);

                    // Registrar movimiento en inventario (tipo 'notacredito')
                    MovimientoInventario::create([
                        'tipo'           => 'notacredito',
                        'sale_id'        => $this->saleId,
                        'lote_id'        => $detail->lote_id,
                        'product_id'     => $detail->product_id,
                        'cantidad'       => $returnQuantity,
                        'costo_unitario' => $detail->price,
                        'total'          => $detail->price * $returnQuantity,
                        'fecha'          => now(),
                    ]);

                    // Actualizar el inventario incrementando 'cantidad_notacredito'
                    $inventario = Inventario::where('product_id', $detail->product_id)
                        ->where('lote_id', $detail->lote_id)
                        ->where('store_id', $this->store_id)
                        ->first();
                    if ($inventario) {
                        $inventario->cantidad_notacredito += $returnQuantity;
                        $inventario->save();
                    }

                    // Actualizar el detalle de la venta (restar la cantidad devuelta)
                    $detail->quantity -= $returnQuantity;
                    $detail->save();
                }
            }

            // Opcional: Actualizar el estado de la venta a "devuelta" (status 3)
            $this->sale->status = 3;
            $this->sale->save();

            DB::commit();

            session()->flash('message', 'Devolución parcial procesada correctamente.');
            return redirect()->to('/sales');
        } catch (Exception $e) {
            DB::rollback();
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.partial-return');
    }
}
