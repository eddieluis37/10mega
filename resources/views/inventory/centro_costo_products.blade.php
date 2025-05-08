@extends('layouts.theme.app')
@section('content')
<style>
  .table-totales {
    /*border: 2px solid red;*/
  }

  .table-totales,
  th,
  td {
    border: 1px solid #DCDCDC;
  }

  .table-inventario,
  th,
  td {
    border: 1px solid #DCDCDC;
  }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row sales layout-top-spacing">
  <div class="col-sm-12">
    <div class="widget widget-chart-one">
      <div class="card text-center" style="background: #3B3F5C">
        <div class="m-2">
          <h4 style="color:white;"><strong>Conteo de Inventario</s></strong></h3>
        </div>
      </div>
      <div class="row g-3 mt-3">

        <div class="col-md-4">
          <div class="form-group">
            <label for="centrocosto" class="form-label">Bodega</label>
            <select class="form-control form-control-sm select2" name="centrocosto" id="centrocosto" required>
              <option value="">Seleccione el centro de costo</option>
              @foreach($centros as $option)
              <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}">{{ $option['name'] }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="categoria" class="form-label">Categoría</label>
            <select class="form-control form-control-sm select2" name="categoria" id="categoria" required>
              <option value="">Seleccione la categoría</option>
              @foreach($category as $option)
              <option value="{{ $option['id'] }}" data-name="{{ $option['name'] }}">{{ $option['name'] }}</option>
              @endforeach
            </select>
            <span class="text-danger error-message"></span>
          </div>
        </div>

        <div class="col-md-4 d-flex align-items-end">
          <form method="GET" action="/descargar-reporte">
            @csrf
            <button type="submit" class="btn btn-primary">Descargar Reporte en Excel</button>
          </form>
        </div>

      </div>

      <div class="table-responsive mt-3">
        <form method="POST" action="/updateCcpInventory">
          @csrf
          <table id="tableInventory" class="table table-striped mt-1">
            <thead class="text-white" style="background: #3B3F5C">
              <tr>
                <th class="table-th text-white">CAT</th>
                <th class="table-th text-white">ID</th>
                <th class="table-th text-white">PRODUCTO</th>
                <th class="table-th text-white">STK°I</th>
                <th class="table-th text-white">LOTE</th>
                <th class="table-th text-white">FEC_VENC</th>
                <th class="table-th text-white">STK°F</th>
                <th class="table-th text-white">DIF</th>
                <th class="table-th text-white">COSTO</th>
                <th class="table-th text-white">SUBTOTAL</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>Totales</th>
                <td></td>
                <td>0.00</td>
              </tr>
            </tfoot>
          </table>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@section('script')

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    // Inicializar Select2 con tema Bootstrap 5
    $('.select2').select2({
      theme: 'bootstrap-5',
      width: '100%'
    });

    const token = $('meta[name="csrf-token"]').attr('content');
    let dataTable = initializeDataTable($('#centrocosto').val(), $('#categoria').val());

    // Re-inicializar DataTable al cambiar filtro Select2
    $('#centrocosto, #categoria').on('change', function() {
      dataTable.destroy();
      dataTable = initializeDataTable($('#centrocosto').val(), $('#categoria').val());
    });
  });

  // Funciones para DataTable y actualización 

  console.log("Comenzando");
  const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

  $(document).ready(function() {
    let dataTable = initializeDataTable("-1", "-1");

    function initializeDataTable(centrocostoId, categoriaId) {
      return $("#tableInventory").DataTable({
        paging: true,
        pageLength: 15,
        autoWidth: false,
        processing: true,
        serverSide: true,
        lengthMenu: [
          [10, 15, 25, 50, -1],
          [10, 15, 25, 50, "Todos"],
        ],
        ajax: {
          url: "/showCcpInventory",
          type: "GET",
          data: {
            centrocostoId,
            categoriaId
          },
          dataSrc: function(resp) {
            return resp.data.map((item) => ({
              namecategoria: item.namecategoria,
              productId: item.productId,
              nameproducto: item.nameproducto,
              stockideal: item.stockideal,
              lotecodigo: item.lotecodigo,
              lotevence: item.lotevence,
              fisico: `<input type="text" class="edit-fisico" value="${item.fisico}" size="4" />`,
              diferencia: item.diferencia,
              costo: item.costo,
              subtotal: item.fisico * item.costo,
              loteId: item.loteId, // <-- nuevo campo
            }));
          },
        },
        columns: [{
            data: "namecategoria"
          },
          {
            data: "productId"
          },
          {
            data: "nameproducto"
          },
          {
            data: "stockideal"
          },
          {
            data: "lotecodigo"
          },
          {
            data: "lotevence"
          },
          {
            data: "fisico"
          },
          {
            data: "diferencia"
          },
          {
            data: "costo",
            render: (d) => "$ " + formatCantidadSinCero(d),
          },
          {
            data: "subtotal",
            render: (d) => "$ " + formatCantidadSinCero(d),
          },
          {
            data: "loteId", // columna oculta
            visible: false,
            searchable: false,
          },
        ],
        createdRow: function(row, data) {
          // agrego atributo para fácil acceso
          $(row).attr("data-lote-id", data.loteId);
        },
        order: [
          [2, "ASC"]
        ],
        language: {
          processing: "Procesando...",
          lengthMenu: "Mostrar _MENU_ registros",
          zeroRecords: "No se encontraron resultados",
          emptyTable: "Ningún dato disponible en esta tabla",
          sInfo: "Mostrando del _START_ al _END_ de total _TOTAL_ registros",
          infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
          infoFiltered: "(filtrado de un total de _MAX_ registros)",
          search: "Buscar:",
          infoThousands: ",",
          loadingRecords: "Cargando...",
          paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior",
          },
        },
      });
    }

    function updateCcpInventory(productId, fisico, centrocostoId, loteId) {
      console.log("Ajuste:", {
        productId,
        fisico,
        centrocostoId,
        loteId
      });
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": token
        },
        url: "/updateCcpInventory",
        type: "POST",
        data: {
          productId,
          fisico,
          centrocostoId,
          loteId
        },
        success: (resp) => {
          Swal.fire({
            icon: "success",
            title: "Ajuste registrado",
            text: resp.message,
          });
          dataTable.ajax.reload(null, false);
        },
        error: (xhr, status, err) => {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo registrar el ajuste.",
          });
          console.error(err);
        },
      });
    }

    // atajo para capturar ENTER o TAB en los inputs de fisico
    $(document).on("keydown", ".edit-fisico", function(e) {
      if (e.which === 13 || e.which === 9) {
        e.preventDefault();
        let $input = $(this);
        let val = $input.val().replace(",", ".");
        let regex = /^[0-9]+(?:\.[0-9]{1,2})?$/;
        if (!regex.test(val)) {
          return Swal.fire({
            icon: "error",
            title: "Número inválido",
            text: "Ingrese un número con hasta 2 decimales.",
          });
        }
        let $row = $input.closest("tr");
        let productId = $row.find("td").eq(1).text().trim();
        let centrocostoId = $("#centrocosto").val();
        let loteId = $row.data("lote-id");
        updateCcpInventory(
          productId,
          parseFloat(val),
          centrocostoId,
          loteId
        );
        // paso al siguiente
        $row.next().find(".edit-fisico").focus().select();
      }
    });

    // cambio de filtros
    $("#centrocosto, #categoria").on("change", function() {
      dataTable.destroy();
      dataTable = initializeDataTable(
        $("#centrocosto").val(),
        $("#categoria").val()
      );
    });
  });
</script>
@endsection