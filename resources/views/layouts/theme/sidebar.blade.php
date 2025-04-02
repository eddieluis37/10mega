<style>
    .base-icons svg {
        transition: fill 0.3s ease, transform 0.3s ease;
    }

    .icon-active svg {
        fill: #007bff !important;
        /* Color de resaltado */
        transform: scale(1.1);
        /* Aumenta un poco el tamaño para remarcar */
    }

    /* Transición base para suavizar los cambios */
    .icon-active {
        fill: #007bff !important;
        /* Color de resaltado */
        transform: scale(1.1);
        /* Aumenta ligeramente el tamaño */
        transition: fill 0.3s ease, transform 0.3s ease;
    }
</style>

<div class="sidebar-wrapper sidebar-theme">
    <nav id="compactSidebar">
        <ul class="menu-categories">

            @can('ver_administracion')
            <li class="menu">
                <a href="#users" data-active="false" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <span>Administración</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>
            @endcan

            @can('ver_productos')

            <li class="menu {{ Request::is('producto*') ? 'active' : '' }}">
                <a href="#products" data-active="{{ Request::is('producto*') ? 'true' : 'false' }}" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons {{ Request::is('producto*') ? 'icon-active' : '' }}">
                            <!-- Icono SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                        </div>
                        <span>Productos</span>
                    </div>
                </a>
                <!-- ... -->
            </li>

            @endcan

            @can('ver_compras')
            <li class="menu">
                <a href="#more" data-active="false" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                        </div>
                        <span class="">Compras</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>
            @endcan

            @can('Pos_Create')
            <li class="">
                <a href="{{ url('home') }}" class="menu-toggle" data-active="false">
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
                        <span>Traza</span>
                    </div>
                </a>
            </li>
            @endcan

            @can('ver_orders')
            <li class="menu">
                <a href="#comercial" data-active="false" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 3.5a2.5 2.5 0 0 1 2.5 2.5v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2v-2a2.5 2.5 0 0 1 2.5-2.5"></path>
                            </svg>
                        </div>
                        <span>Comercial</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>
            @endcan

            @can('ver_ventas')
            <li class="menu">
                <a href="#sales" data-active="false" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                        </div>
                        <span>Ventas</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>

            @endcan

            @can('Pos_Create')
            <li class="">
                <a href="{{url('caja')}}" class="menu-toggle" data-active="false">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cash-register">
                                <path d="M17 10a4 4 0 0 1 4 4v4a4 4 0 0 1-4 4H5a4 4 0 0 1-4-4V5a4 4 0 0 1 4-4h4m0 10a2 2 0 0 0 2 2v4a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-4m-6 0a2 2 0 0 1-2-2H5a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h4a2 2 0 0 1 2-2v-4"></path>
                            </svg>
                        </div>
                        <span>Caja</span>
                    </div>
                </a>
            </li>
            @endcan

           
           <!--  <li class="">
                <a href="{{url('caja')}}" class="menu-toggle" data-active="false">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.25 7h13.5l2.25-7h4"></path>
                            </svg>
                        </div>
                        <span>Logistica</span>
                    </div>
                </a>
            </li> -->
        

            @can('ver_traslado')
            <li class="">
                <a href="{{url('transfer')}}" class="menu-toggle" data-active="false">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M19 12h2M3 12h2M18 4.06l1.06 1.06M5.94 19.94L4.88 18"></path>
                            </svg>
                        </div>
                        <span>Traslados</span>
                    </div>
                </a>
            </li>
            @endcan

            @can('ver_inventario')
            <li class="menu">
                <a href="#app" data-active="false" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cpu">
                                <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                                <rect x="9" y="9" width="6" height="6"></rect>
                                <line x1="9" y1="1" x2="9" y2="4"></line>
                                <line x1="15" y1="1" x2="15" y2="4"></line>
                                <line x1="9" y1="20" x2="9" y2="23"></line>
                                <line x1="15" y1="20" x2="15" y2="23"></line>
                                <line x1="20" y1="9" x2="23" y2="9"></line>
                                <line x1="20" y1="14" x2="23" y2="14"></line>
                                <line x1="1" y1="9" x2="4" y2="9"></line>
                                <line x1="1" y1="14" x2="4" y2="14"></line>
                            </svg>
                        </div>
                        <span>Inventario</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>
            @endcan

            @can('Admin_Menu')
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
                        <span>Contabilidad</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>
            @endcan

            @role('Admin')
            <li class="">
                <a href="{{url('faster')}}" class="menu-toggle" data-active="false">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <span>Financiera</span>
                    </div>
                </a>
            </li>
            @endcan
            @can('Report_Create')
            <li class="menu active">
                <a href="#dashboard" data-active="true" class="menu-toggle">
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


            @can('Parametros_Menu')
            <li class="menu">
                <a href="#tables" data-active="false" class="menu-toggle">
                    <div class="base-menu">
                        <div class="base-icons">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layout">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                        </div>
                        <span>Parametros</span>
                    </div>
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </li>
            @endcan

        </ul>
    </nav>

    <div id="compact_submenuSidebar" class="submenu-sidebar">

        <div class="submenu" id="sales">
            <ul class="submenu-list" data-parent-element="#sales">
                @can('ver_venta_autoservicio')
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#submenuauto" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> AUTOSERVICIO </div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="submenuauto" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('sales') }}"> VENTAS </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> CIERRE DE CAJA </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> MANEJO DE EFECTIVO </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> HISTORICO DE VENTAS </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> REMISIONES </a>
                        </li>
                        <li>
                            <a href="{{ url('notacredito') }}"> NOTAS CREDITOS </a>
                        </li>
                    </ul>
                </li>
                @endcan
                @can('ver_venta_bar')
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#submenubar" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> BAR </div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="submenubar" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('sales') }}"> VENTAS </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> CIERRE DE CAJA </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> MANEJO DE EFECTIVO </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> HISTORICO DE VENTAS </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> REMISIONES </a>
                        </li>
                        <li>
                            <a href="{{ url('notacredito') }}"> NOTAS CREDITOS </a>
                        </li>
                    </ul>
                </li>
                @endcan
                @can('ver_venta_domicilio')
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#submenudomicilio" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> DOMICILIO </div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="submenudomicilio" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('sales') }}"> VENTAS </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> CIERRE DE CAJA </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> MANEJO DE EFECTIVO </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> HISTORICO DE VENTAS </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> REMISIONES </a>
                        </li>
                        <li>
                            <a href="{{ url('notacredito') }}"> NOTAS CREDITOS </a>
                        </li>
                    </ul>
                </li>
                @endcan
                @can('ver_venta_parrilla')
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#submenuparrilla" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> PARRILLA </div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="submenuparrilla" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('sales') }}"> VENTAS </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> CIERRE DE CAJA </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> MANEJO DE EFECTIVO </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> HISTORICO DE VENTAS </a>
                        </li>
                        <li>
                            <a href="{{ url('sales') }}"> REMISIONES </a>
                        </li>
                        <li>
                            <a href="{{ url('notacredito') }}"> NOTAS CREDITOS </a>
                        </li>
                    </ul>
                </li>
                @endcan
        </div>


        <div class="submenu" id="dashboard">
            <ul class="submenu-list" data-parent-element="#dashboard">
                <li class="active">
                    <a href="{{ url('home') }}" style="display: flex; align-items: center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pie-chart" style="margin-right: 8px;">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                            <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                        </svg>
                        Generales
                    </a>
                </li>
                <li>
                    <a href="{{ url('reports') }}" style="display: flex; align-items: center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag" style="margin-right: 8px;">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        Reporte de Ventas
                    </a>
                </li>
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#compra" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Compras</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="compra" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('reportes/compras_requeridas') }}"> Requeridas </a>
                        </li>
                        <li>
                            <a href="{{ url('reportes/compras_por_productos') }}"> Por productos </a>
                        </li>
                        <li>
                            <a href="{{ url('reportes/compras_por_proveedores') }}"> Por prov & prod </a>
                        </li>
                    </ul>
                </li>

                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#sale" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Ventas</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="sale" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('report_sales_x_sc') }}"> Por sub-centro costo </a>
                        </li>
                        <!-- <li>
                            <a href="{{ url('report_sales_x_prod') }}"> Por productos </a>
                        </li> -->
                        <li>
                            <a href="{{ url('reportes/ventas_por_productos') }}"> Por productos </a>
                        </li>
                        <li>
                            <a href="{{ url('reportes/ventas_por_productos_clientes') }}"> Por prod & cliente </a>
                        </li>
                        <li>
                            <a href="{{ url('excel-consolidado-ventas') }}"> Consolidado ventas </a>
                        </li>
                        <li>
                            <a href="{{ url('descargar-reporte') }}"> Stock fisico real </a>
                        </li>
                    </ul>
                </li>
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#inventory" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Inventarios</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="inventory" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('excel-analisis-kg') }}"> Análisis de KG </a>
                        </li>
                        <li>
                            <a href="{{ url('excel-analisis-utilidad') }}"> Análisis de Utilidad </a>
                        </li>
                    </ul>
                </li>
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#order" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Order de pedidos</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="order" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('reports_orders') }}"> Consolidado Orders </a>
                        </li>
                        <li>
                            <a href="{{ url('excel-analisis-utilidad') }}"> </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="submenu" id="app">
            <ul class="submenu-list" data-parent-element="#app">
                <li>
                    <a href="{{ url('inventario/cierre') }}" style="display: flex; align-items: center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-check-square">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M22 4v16H6"></path>
                        </svg>
                        Cierre de Inv KG
                    </a>
                </li>
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

                <li>
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

            </ul>
        </div>

        <div class="submenu" id="uiKit">
            <ul class="submenu-list" data-parent-element="#uiKit">
                <li>
                    <a href="ui_alerts.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Alerts </a>
                </li>
                <li>
                    <a href="ui_avatar.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Avatar </a>
                </li>
                <li>
                    <a href="ui_badges.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Badges </a>
                </li>
                <li>
                    <a href="ui_breadcrumbs.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Breadcrumbs </a>
                </li>
                <li>
                    <a href="ui_buttons.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Buttons </a>
                </li>
                <li>
                    <a href="ui_buttons_group.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Button Groups </a>
                </li>
                <li>
                    <a href="ui_color_library.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Color Library </a>
                </li>
                <li>
                    <a href="ui_dropdown.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Dropdown </a>
                </li>
                <li>
                    <a href="ui_infobox.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Infobox </a>
                </li>
                <li>
                    <a href="ui_jumbotron.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Jumbotron </a>
                </li>
                <li>
                    <a href="ui_loader.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Loader </a>
                </li>
                <li>
                    <a href="ui_pagination.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Pagination </a>
                </li>
                <li>
                    <a href="ui_popovers.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Popovers </a>
                </li>
                <li>
                    <a href="ui_progress_bar.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Progress Bar </a>
                </li>
                <li>
                    <a href="ui_search.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Search </a>
                </li>
                <li>
                    <a href="ui_tooltips.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Tooltips </a>
                </li>
                <li>
                    <a href="ui_treeview.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Treeview </a>
                </li>
                <li>
                    <a href="ui_typography.html"> <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Typography </a>
                </li>
            </ul>
        </div>

        <div class="submenu" id="components">
            <ul class="submenu-list" data-parent-element="#components">
                <li>
                    <a href="component_tabs.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Tabs </a>
                </li>
                <li>
                    <a href="component_accordion.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Accordions </a>
                </li>
                <li>
                    <a href="component_modal.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Modals </a>
                </li>
                <li>
                    <a href="component_cards.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Cards </a>
                </li>
                <li>
                    <a href="component_bootstrap_carousel.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span>Carousel</a>
                </li>
                <li>
                    <a href="component_blockui.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Block UI </a>
                </li>
                <li>
                    <a href="component_countdown.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Countdown </a>
                </li>
                <li>
                    <a href="component_counter.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Counter </a>
                </li>
                <li>
                    <a href="component_sweetalert.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Sweet Alerts </a>
                </li>
                <li>
                    <a href="component_timeline.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Timeline </a>
                </li>
                <li>
                    <a href="component_snackbar.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Notifications </a>
                </li>
                <li>
                    <a href="component_session_timeout.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Session Timeout </a>
                </li>
                <li>
                    <a href="component_media_object.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Media Object </a>
                </li>
                <li>
                    <a href="component_list_group.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> List Group </a>
                </li>
                <li>
                    <a href="component_pricing_table.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Pricing Tables </a>
                </li>
                <li>
                    <a href="component_lightbox.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Lightbox </a>
                </li>
            </ul>
        </div>

        <div class="submenu" id="forms">
            <ul class="submenu-list" data-parent-element="#forms">
                <li>
                    <a href="form_bootstrap_basic.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Basic </a>
                </li>
                <li>
                    <a href="form_input_group_basic.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Input Group </a>
                </li>
                <li>
                    <a href="form_layouts.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Layouts </a>
                </li>
                <li>
                    <a href="form_validation.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Validation </a>
                </li>
                <li>
                    <a href="form_input_mask.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Input Mask </a>
                </li>
                <li>
                    <a href="form_bootstrap_select.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Bootstrap Select </a>
                </li>
                <li>
                    <a href="form_select2.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Select2 </a>
                </li>
                <li>
                    <a href="form_bootstrap_touchspin.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> TouchSpin </a>
                </li>
                <li>
                    <a href="form_maxlength.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Maxlength </a>
                </li>
                <li>
                    <a href="form_checkbox_radio.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Checkbox &amp; Radio </a>
                </li>
                <li>
                    <a href="form_switches.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Switches </a>
                </li>
                <li>
                    <a href="form_wizard.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Wizards </a>
                </li>
                <li>
                    <a href="form_fileupload.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> File Upload </a>
                </li>
                <li>
                    <a href="form_quill.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Quill Editor </a>
                </li>
                <li>
                    <a href="form_markdown.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Markdown Editor </a>
                </li>
                <li>
                    <a href="form_date_range_picker.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Date &amp; Range Picker </a>
                </li>
                <li>
                    <a href="form_clipboard.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Clipboard </a>
                </li>
                <li>
                    <a href="form_typeahead.html"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Typeahead </a>
                </li>
            </ul>
        </div>


        <div class="submenu" id="tables">
            <ul class="submenu-list" data-parent-element="#tables">
                <li>
                    <a href="coins"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Efectivo </a>
                </li>
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#datatables" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Terceros</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="datatables" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('categories') }}""> Categorías </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class=" submenu" id="comercial">
                                <ul class="submenu-list" data-parent-element="#comercial">
                                    <li>
                                        <a href="{{ url('orders') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                </svg></span> Ordenes de pedidos </a>
                                    </li>
                                </ul>
        </div>

        <div class=" submenu" id="documentos">
            <ul class="submenu-list" data-parent-element="#documentos">
                <li>
                    <a href="{{ url('notacredito') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Nota crédito </a>
                </li>
                <li>
                    <a href="{{ url('notadebito') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Nota débito </a>
                </li>
                <li>
                    <a href="{{ url('recibodecajas') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Recibo de caja </a>
                </li>
                <li>
                    <a href="{{ url('cuentasporcobrars') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Cuentas X cobrar </a>
                </li>



            </ul>
        </div>


        <div class=" submenu" id="users">
            <ul class="submenu-list" data-parent-element="#users">
                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#admin" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Admin</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="admin" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('meatcuts') }}"> Cortes principales </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ url('roles') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Roles </a>
                </li>
                <li>
                    <a href="{{ url('users') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Usuarios </a>
                </li>
                <li>
                    <a href="{{ url('permisos') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Permisos </a>
                </li>
                <li>
                    <a href="{{ url('asignar') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Asignar Permisos </a>
                </li>
                <li>
                    <a href="{{url('coins')}}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Denominaciones </a>
                </li>
                @can('ver_terceros')
                <li>
                    <a href="{{url('thirds')}}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Terceros </a>
                </li>
                @endcan
                <!--   <li>
                    <a href="{{url('precio_agreements')}}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Precios Acuerdos </a>
                </li> -->
                <li>
                    <a href="{{url('formapago')}}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Formas de Pago </a>
                </li>
                <li>
                    <a href="{{url('parametrocontable')}}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Parámetros Contables </a>
                </li>
                <li>
                    <a href="{{ url('categories') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Categorías </a>
                </li>
                @can('ver_brand')
                <li>
                    <a href="{{ route('brand-crud.index') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Marcas </a>
                </li>
                <li>
                    <a href="{{ route('brands.index') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Relacionar Marcas </a>
                </li>
                @endcan                
            </ul>
        </div>
        <div class="submenu" id="pages">
            <ul class="submenu-list" data-parent-element="#pages">
                <li>
                    <a href="{{ url('categories') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Categorías </a>
                </li>

                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#admin" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Admin</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="admin" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('meatcuts') }}"> Cortes principales </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="submenu" id="reports">
            <ul class="submenu-list" data-parent-element="#reports">
                <li>
                    <a href="{{ url('reports') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Reporte de ventas </a>
                </li>

                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#sale" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Ventas</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="sale" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('excel-consolidado-ventas') }}"> Consolidado Ventas </a>
                        </li>
                        <li>
                            <a href="{{ url('descargar-reporte') }}"> Stock fisico real</a>
                        </li>
                    </ul>
                </li>

                <li class="sub-submenu">
                    <a role="menu" class="collapsed" data-toggle="collapse" data-target="#inventory" aria-expanded="false">
                        <div><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg></span> Inventarios</div> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <ul id="inventory" class="collapse" data-parent="#compact_submenuSidebar">
                        <li>
                            <a href="{{ url('excel-analisis-kg') }}"> Análisis de KG </a>
                        </li>
                        <li>
                            <a href="{{ url('excel-analisis-utilidad') }}"> Análisis de Utilidad </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class=" submenu" id="products">
            <ul class="submenu-list" data-parent-element="#products">
                <li>
                    <a href="{{ url('producto') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Productos </a>
                </li>
                <li>
                    <a href="{{ url('meatcuts') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Productos Basicos </a>
                </li>             
                <li>
                    <a href="{{ url('centro_costo_prod') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Asignar precios mínimos </a>
                </li>
                <li>
                    <a href="{{ url('lista_de_precio') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Lista de precios clientes </a>
                </li>
                <li>
                    <a href="{{ url('asignar_precios_prod') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Asignar precios clientes</a>
                </li>
        </div>

        <div class="submenu" id="more">
            <ul class="submenu-list" data-parent-element="#more">
                @can('ver_compra_lote')
                <li>
                    <a href="{{ url('beneficiores') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Lote Res </a>
                </li>
                <li>
                    <a href="{{ url('beneficiocerdo') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Lote Cerdo </a>
                </li>
                <li>
                    <a href="{{ url('beneficioaves') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Lote Pollos </a>
                </li>
                @endcan
                @can('ver_compra_productos')
                <li>
                    <a href="{{ url('compensado') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Productos </a>
                </li>
                @endcan

                @can('ver_alistamiento')
                <li>
                    <a href="{{ url('alistamiento') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Alistamiento </a>
                </li>
                @endcan
                @can('Workshop')
                <li>
                    <a href="{{ url('workshop') }}"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg></span> Taller </a>
                </li>
                @endcan
            </ul>
        </div>

    </div>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const currentUrl = window.location.href;
        const sidebar = document.getElementById('compactSidebar');

        // Procesar elementos del menú principal: si la URL actual contiene el href, se resalta el icono.
        document.querySelectorAll('a.menu-toggle').forEach(function(menuLink) {
            const href = menuLink.getAttribute('href');
            if (currentUrl.indexOf(href) > -1) {
                let iconContainer = menuLink.querySelector('.base-icons');
                if (iconContainer) {
                    iconContainer.classList.add('icon-active');
                }
            }
        });

        // Procesar elementos del submenú.
        document.querySelectorAll('.submenu-list a').forEach(function(submenuLink) {
            const href = submenuLink.getAttribute('href');
            if (currentUrl.indexOf(href) > -1) {
                // Resalta el icono del submenú
                let svgIcon = submenuLink.querySelector('svg');
                if (svgIcon) {
                    svgIcon.classList.add('icon-active');
                }

                // Obtener el contenedor del submenú (por ejemplo, el div con id="app")
                let submenuContainer = submenuLink.closest('.submenu');
                if (submenuContainer) {
                    let submenuId = submenuContainer.getAttribute('id');
                    // Buscar el enlace del menú principal cuyo href coincida con "#" + id del submenú
                    let mainMenuLink = document.querySelector('a.menu-toggle[href="#' + submenuId + '"]');
                    if (mainMenuLink) {
                        // Resalta el icono del menú principal
                        let mainIconContainer = mainMenuLink.querySelector('.base-icons');
                        if (mainIconContainer) {
                            mainIconContainer.classList.add('icon-active');
                        }
                        // Opcional: marcar el menú principal como activo
                        mainMenuLink.parentElement.classList.add('active');

                        // Ajustar el scroll para que el icono del menú principal activo quede posicionado en la vista.
                        if (sidebar && mainIconContainer) {
                            // Obtenemos las posiciones relativas del icono y del contenedor
                            const iconRect = mainIconContainer.getBoundingClientRect();
                            const sidebarRect = sidebar.getBoundingClientRect();

                            // Si el icono no está completamente visible en el contenedor, ajustamos el scroll.
                            if (iconRect.top < sidebarRect.top || iconRect.bottom > sidebarRect.bottom) {
                                // Calcula un nuevo scrollTop para centrar el icono en el contenedor.
                                const newScrollTop = mainIconContainer.offsetTop - (sidebar.offsetHeight / 2) + (mainIconContainer.offsetHeight / 2);
                                sidebar.scrollTo({
                                    top: newScrollTop,
                                    behavior: 'smooth'
                                });
                            }
                        }
                    }
                }
            }
        });
    });
</script>