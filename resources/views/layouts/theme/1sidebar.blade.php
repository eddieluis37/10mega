 <div class="sidebar-wrapper sidebar-theme">
     <nav id="compactSidebar">

         <ul class="menu-categories">

             @role('Admin')
             <li class="active">
                 <a href="{{url('categories')}}" class="menu-toggle" data-active="true">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid">
                                 <rect x="3" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="14" width="7" height="7"></rect>
                                 <rect x="3" y="14" width="7" height="7"></rect>
                             </svg>
                         </div>
                         <span>CATEGORÍAS</span>
                     </div>
                 </a>
             </li>
             @endcan


             <!--   @role('Admin')
            <li class="">
                <a href="{{url('faster')}}" class="menu-toggle" data-active="false">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.25 7h13.5l2.25-7h4"></path>
                            </svg>
                        </div>
                        <span>Faster</span>
                    </div>
                </a>
            </li>
            @endcan        -->


             <!-- 
            @can('Cashout_Create')
            <li class="">
                <a href="{{url('cashout')}}" class="menu-toggle" data-active="false">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <span>Arqueos</span>
                    </div>
                </a>
            </li>
            @endcan
 -->

             <!--    @can('Admin_Menu')
            <li class="menu">
                <a href="#documentos" data-active="false" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                            </svg>
                        </div>
                        <span>Documentos</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>
            @endcan
            
               
            @can('Alistamiento_Menu')
            <li class="">
                <a href="{{url('alistamiento')}}" class="menu-toggle" data-active="false">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart">
                                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                                <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                            </svg>
                        </div>
                        <span>Alistamiento</span>
                    </div>
                </a>
            </li>
            @endcan

            @role('Admin')
            <li class="">
                <a href="{{url('workshop')}}" class="menu-toggle" data-active="false">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tools">
                                <path d="M21 13v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8m2 0V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v8m-6 0h4"></path>
                                <line x1="8" y1="3" x2="8" y2="3"></line>
                                <line x1="12" y1="3" x2="12" y2="3"></line>
                                <line x1="16" y1="3" x2="16" y2="3"></line>
                            </svg>
                        </div>
                        <span>Taller</span>
                    </div>
                </a>
            </li>
            @endcan

              @can('Report_Create')
            <li class="menu">
                <a href="#reports" data-active="true" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2">
                                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                <line x1="12" y1="2" x2="12" y2="22"></line>
                                <line x1="6" y1="6" x2="6" y2="6"></line>
                                <line x1="6" y1="12" x2="6" y2="12"></line>
                                <line x1="6" y1="18" x2="6" y2="18"></line>
                                <line x1="18" y1="16" x2="18" y2="16"></line>
                                <line x1="18" y1="8" x2="18" y2="8"></line>
                            </svg>
                        </div>
                        <span>Reportes</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>
            @endcan 
            
            -->


             <div class="submenu" id="app">
                 <ul class="submenu-list" data-parent-element="#app">
                     <!-- <li>
                    <a href="{{ url('inventory/diary') }}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg> Diario</a>
                </li> -->
                     <li>
                         <a href="{{ url('inventario/cierre') }}" style="display: flex; align-items: center">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square ">
                                 <polyline points="9 11 12 14 22 4"></polyline>
                                 <path d="M22 4v16H6"></path>
                             </svg> Cierre de Inv KG</a>
                     </li>
                     <!-- <li>
                    <a href="{{ url('inventory/consolidado') }}" style="display: flex; align-items: center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square ">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M22 4v16H6"></path>
                        </svg> V1 Ci Inv KG</a>
                </li> -->
                     <li>
                         <a href="{{ url('inventory/utilidad') }}" style="display: flex; align-items: center">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square">
                                 <line x1="12" y1="1" x2="12" y2="23"></line>
                                 <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                             </svg> Cierre de Utilidad</a>
                     </li>
                     <li>
                         <a href="{{ url('inventory/consolidado_historico') }}" style="display: flex; align-items: center">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                 <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                 <line x1="16" y1="2" x2="16" y2="6"></line>
                                 <line x1="8" y1="2" x2="8" y2="6"></line>
                                 <line x1="3" y1="10" x2="21" y2="10"></line>
                             </svg> Históricos KG</a>
                     </li>
                     <li>
                         <a href="{{ url('inventory/consolidado_histutilidad') }}" style="display: flex; align-items: center">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                 <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                 <line x1="16" y1="2" x2="16" y2="6"></line>
                                 <line x1="8" y1="2" x2="8" y2="6"></line>
                                 <line x1="3" y1="10" x2="21" y2="10"></line>
                             </svg> Históricos de Utilidad</a>
                     </li>
                     <!--   <li>
                    <a href="{{ url('transfer') }}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg> Traslado</a>
                </li> -->
                     <!--    <li>
                    <a href="apps_contacts.html"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                            <path d="M23 4v6h-6"></path>
                            <path d="M1 20v-6h6"></path>
                            <path d="M15 3l-3 3 3 3"></path>
                            <path d="M8 14l3 3 3-3"></path>
                            <path d="M3 10h18"></path>
                        </svg> Devoluciones</a>
                </li> -->
                     <!-- <li>
                    <a href="apps_scrumboard.html"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-down-circle">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="8 12 12 16 16 12"></polyline>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                        </svg> Producto de baja</a>
                </li> -->
                     <!-- <li>
                    <a href="{{ url('inventory/cargar_ventas') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.25 6h13.5l2.25-6h4"></path>
                            <path d="M15 17H5.2l-.4-2H15v2zm-4 4h10v-2H11v2zM8 17h2v-3H8v3zm4 0h2v-3h-2v3z"></path>
                        </svg>
                        Cargar Ventas
                    </a>
                </li> -->


                     <!-- <li>
                         <a href="{{ url('inventory/centro_costo_products') }}" style="display: flex; align-items: center">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-plus">
                                 <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                 <polyline points="14 2 14 8 20 8"></polyline>
                                 <line x1="12" y1="18" x2="12" y2="12"></line>
                                 <line x1="9" y1="15" x2="15" y2="15"></line>
                             </svg> Stock Fisico Real</a>
                     </li>
                     @can('ver_cargue_productos_term')
                     <li>
                         <a href="{{ url('inventory/cargue_products_terminados') }}" style="display: flex; align-items: center">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-plus">
                                 <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                 <polyline points="14 2 14 8 20 8"></polyline>
                                 <line x1="12" y1="18" x2="12" y2="12"></line>
                                 <line x1="9" y1="15" x2="15" y2="15"></line>
                             </svg> Cargue Prod Term</a>
                     </li>
                     @endcan
                     <!--   <li>
                    <a href="{{ url('') }}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg> </a>
                </li> -->
                 </ul>
             </div>
 -->

             <li class="">
                 <a href="{{ url('products') }}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag">
                                 <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                 <line x1="7" y1="7" x2="7.01" y2="7"></line>
                             </svg>
                         </div>
                         <span>PRODUCTOS</span>
                     </div>
                 </a>
             </li>
             <li class="">
                 <a href="{{ url('pos') }}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                                 <circle cx="9" cy="21" r="1"></circle>
                                 <circle cx="20" cy="21" r="1"></circle>
                                 <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                             </svg>
                         </div>
                         <span>VENTAS</span>
                     </div>
                 </a>
             </li>

             <li class="">
                 <a href="{{ url('roles') }}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-key">
                                 <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                             </svg>
                         </div>
                         <span>ROLES</span>
                     </div>
                 </a>
             </li>


             <li class="">
                 <a href="{{ url('permisos') }}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-square">
                                 <polyline points="9 11 12 14 22 4"></polyline>
                                 <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                             </svg>
                         </div>
                         <span>PERMISOS</span>
                     </div>
                 </a>
             </li>
             <li class="">
                 <a href="{{ url('asignar') }}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                 <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                 <circle cx="12" cy="12" r="3"></circle>
                             </svg>
                         </div>
                         <span>ASIGNAR</span>
                     </div>
                 </a>
             </li>
             <li class="">
                 <a href="{{ url('users') }}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                 <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                 <circle cx="9" cy="7" r="4"></circle>
                                 <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                 <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                             </svg>
                         </div>
                         <span>USUARIOS</span>
                     </div>
                 </a>
             </li>
             <li class="">
                 <a href="{{ url('coins') }}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-stop-circle">
                                 <circle cx="12" cy="12" r="10"></circle>
                                 <rect x="9" y="9" width="6" height="6"></rect>
                             </svg>
                         </div>
                         <span>MONEDAS</span>
                     </div>
                 </a>
             </li>
             <li class="">
                 <a href="{{url('cashout')}}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                                 <line x1="12" y1="1" x2="12" y2="23"></line>
                                 <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                             </svg>
                         </div>
                         <span>ARQUEOS</span>
                     </div>
                 </a>
             </li>
             <li class="">
                 <a href="{{url('reports')}}" class="menu-toggle" data-active="false">
                     <div class="base-menu">
                         <div class="base-icons">
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart">
                                 <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                                 <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                             </svg>
                         </div>
                         <span>REPORTES</span>
                     </div>
                 </a>
             </li>

         </ul>



     </nav>
 </div>

 <div id="compact_submenuSidebar" class="submenu-sidebar" style="display: none!important">
 </div>