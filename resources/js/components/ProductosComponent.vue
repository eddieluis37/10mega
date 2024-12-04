<template>
    <div class="row sales layout-top-spacing">
        <div class="col-sm-12">
            <div class="widget widget-chart-one">
                <div class="widget-heading">
                    <h4 class="card-title"><b>Productos | Listado</b></h4>
                    <ul class="tabs tab-pills">
                        <li>
                            <a href="javascript:void(0)" @click="showModalCreate" class="tabmenu bg-dark" title="Crear nuevo producto">Crear Productos</a>
                        </li>
                    </ul>
                </div>
                <div class="widget-content">
                    <div class="table-responsive">
                        <table id="tableProducto" class="table table-striped mt-1">
                            <thead class="text-white" style="background: #3B3F5C">
                                <tr>
                                    <th class="table-th text-white">ID</th>
                                    <th class="table-th text-white">CAT</th>
                                    <th class="table-th text-white">FAMILIA</th>
                                    <th class="table-th text-white">SUBFAMILIA</th>
                                    <th class="table-th text-white">CODE</th>
                                    <th class="table-th text-white">PRECIO_M</th>
                                    <th class="table-th text-white">IVA</th>
                                    <th class="table-th text-white">ISA</th>
                                    <th class="table-th text-white text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí puedes usar v-for para iterar sobre los productos -->
                                <tr v-for="producto in productos" :key="producto.id">
                                    <td>{{ producto.id }}</td>
                                    <td>{{ producto.cat }}</td>
                                    <td>{{ producto.familia }}</td>
                                    <td>{{ producto.subfamilia }}</td>
                                    <td>{{ producto.code }}</td>
                                    <td>{{ producto.precio_m }}</td>
                                    <td>{{ producto.iva }}</td>
                                    <td>{{ producto.isa }}</td>
                                    <td>
                                        <!-- Acciones -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <modal-create-producto @close="closeModal" v-if="isModalOpen"></modal-create-producto>
</template>

<script>
import ModalCreateProducto from './ModalCreateProducto.vue';

export default {
    components: {
        ModalCreateProducto,
    },
    data() {
        return {
            productos: [],
            isModalOpen: false,
        };
    },
    methods: {
        fetchProductos() {
            // Aquí puedes hacer una solicitud AJAX para obtener los productos
            axios.get('/api/productos').then(response => {
                this.productos = response.data;
            });
        },
        showModalCreate() {
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.fetchProductos(); // Refrescar la lista de productos
        }
    },
    mounted() {
        this.fetchProductos();
    }
};
</script>