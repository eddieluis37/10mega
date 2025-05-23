console.log("Starting");
const btnAddAlistamiento = document.querySelector("#btnAddalistamiento");
const formAlistamiento = document.querySelector("#form-alistamiento");
const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const btnClose = document.querySelector("#btnModalClose");

const selectCategory = document.querySelector("#categoria");
const selectCentrocosto = document.querySelector("#centrocosto");
const alistamiento_id = document.querySelector("#alistamientoId");
const contentform = document.querySelector("#contentDisable");
const selectCortePadre = document.querySelector("#selectCortePadre");
const fechaalistamiento = document.querySelector("#fecha");


$(document).ready(function () {
    $(function () {
        $("#tableAlistamiento").DataTable({
            paging: true,
            pageLength: 5,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/showcaja",
                type: "GET",
            },
            columns: [
                { data: "id", name: "id" },
                { data: "namecentrocosto", name: "namecentrocosto" },
                { data: "namecajero", name: "namecajero" },                
                {
                    data: "base",
                    name: "base",
                    render: function (data, type, row) {
                        return "$" + formatCantidadSinCero(data);
                    },
                },
                { data: "inventory", name: "inventory" },
                { data: "fecha1", name: "fecha1" },
                { data: "fecha2", name: "fecha2" },
                { data: "action", name: "action" },
            ],
            order: [[0, "DESC"]],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Mostrando del _START_ al _END_ de total _TOTAL_ registros",
                infoEmpty:
                    "Mostrando registros del 0 al 0 de un total de 0 registros",
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
    });   
   
});

  /* document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.open-report').forEach(function(button) {
          button.addEventListener('click', function(e) {
              e.preventDefault();
              var cajaId = this.getAttribute('data-id');
              var reportUrl = '/reporte-cierre-caja/' + cajaId;
              document.getElementById('reportIframe').setAttribute('src', reportUrl);
              var reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
              reportModal.show();
          });
      });
  }); */

  async function openReport(id) {
    try {
        // Realiza la petición al endpoint del reporte
        const response = await fetch(`/reporte-cierre-caja/${id}`);
        if (!response.ok) {
            throw new Error('Error al cargar el reporte');
        }
        const html = await response.text();
        // Inserta el contenido obtenido en el contenedor del modal
        document.getElementById('reportContent').innerHTML = html;
        // Muestra el modal utilizando Bootstrap 5
        const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
        reportModal.show();
    } catch (error) {
        console.error(error);
        alert("Hubo un error al cargar el reporte.");
    }
}


const showModalcreate = () => {
    if (contentform.hasAttribute("disabled")) {
        contentform.removeAttribute("disabled");
        $(".select2corte").prop("disabled", false);
    }
    $(".select2corte").val("").trigger("change");
    selectCortePadre.innerHTML = "";
    formAlistamiento.reset();
    alistamiento_id.value = 0;
};

//const editAlistamiento = (id) => {
//console.log(id);
//const dataform = new FormData();
//dataform.append('id', id);
//send(dataform,'/alistamientoById').then((resp) => {
//console.log(resp);
//console.log(resp.reg);
//showData(resp);
//if(contentform.hasAttribute('disabled')){
//contentform.removeAttribute('disabled');
//$('#provider').prop('disabled', false);
//}
//});
//}

const showDataForm = (id) => {
    console.log(id);
    const dataform = new FormData();
    dataform.append("id", id);
    send(dataform, "/alistamientoById").then((resp) => {
        console.log(resp);
        console.log(resp.reg);
        showData(resp);
        setTimeout(() => {
            $(".select2corte").val(resp.reg.meatcut_id).trigger("change");
        }, 1000);
        $(".select2corte").prop("disabled", true);
        contentform.setAttribute("disabled", "disabled");
    });
};

const showData = (resp) => {
    let register = resp.reg;
    //alistamiento_id.value = register.id;
   
    selectCentrocosto.value = register.centrocosto_id;
    fechaalistamiento.value = register.fecha_hora_inicio;
    getCortes(register.categoria_id);

    const modal = new bootstrap.Modal(
        document.getElementById("modal-create-alistamiento")
    );
    modal.show();
};

const send = async (dataform, ruta) => {
    let response = await fetch(ruta, {
        headers: {
            "X-CSRF-TOKEN": token,
        },
        method: "POST",
        body: dataform,
    });
    let data = await response.json();
    //console.log(data);
    return data;
};

selectCategory.addEventListener("change", function () {
    const selectedValue = this.value;
    console.log("Selected value:", selectedValue);
    getCortes(selectedValue);
});

getCortes = (categoryId) => {
    const dataform = new FormData();
    dataform.append("categoriaId", Number(categoryId));
    send(dataform, "/getproductospadre").then((result) => {
        console.log(result);
        let prod = result.products;
        console.log(prod);
        //showDataTable(result);
        selectCortePadre.innerHTML = "";
        selectCortePadre.innerHTML += `<option value="">Seleccione el producto</option>`;
        // Create and append options to the select element
        prod.forEach((option) => {
            const optionElement = document.createElement("option");
            optionElement.value = option.id;
            optionElement.text = option.name;
            selectCortePadre.appendChild(optionElement);
        });
    });
};

const downAlistamiento = (id) => {
    swal({
        title: "CONFIRMAR",
        text: "¿CONFIRMAS ELIMINAR EL REGISTRO?",
        type: "warning",
        showCancelButton: true,
        cancelButtonText: "Cerrar",
        cancelButtonColor: "#fff",
        confirmButtonColor: "#3B3F5C",
        confirmButtonText: "Aceptar",
    }).then(function (result) {
        if (result.value) {
            console.log(id);
            const dataform = new FormData();
            dataform.append("id", id);
            send(dataform, "/downmmainalistamiento").then((resp) => {
                console.log(resp);
                refresh_table();
            });
        }
    });
};

const refresh_table = () => {
    let table = $("#tableAlistamiento").dataTable();
    table.fnDraw(false);
};


