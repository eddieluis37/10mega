<style>
    .custom-dropdown-right {
        right: 0 !important;
        left: auto !important;
    }
</style>
<div class="header-container fixed-top">
    <header class="header navbar navbar-expand-sm">
        <!-- Logo en la parte izquierda -->
        <ul class="navbar-item flex-row">
            <li class="nav-item theme-logo">
                <a href="{{ route('login') }}">
                    <img src="{{ asset('assets/img/mega-carnes-frias.svg') }}" class="navbar-logo" alt="logo">
                </a>
            </li>
        </ul>

        <!-- Sidebar Toggle -->
        <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-list">
                <line x1="8" y1="6" x2="21" y2="6"></line>
                <line x1="8" y1="12" x2="21" y2="12"></line>
                <line x1="8" y1="18" x2="21" y2="18"></line>
                <line x1="3" y1="6" x2="3" y2="6"></line>
                <line x1="3" y1="12" x2="3" y2="12"></line>
                <line x1="3" y1="18" x2="3" y2="18"></line>
            </svg>
        </a>

        <!-- Aquí se ubica el bloque que queremos mover a la derecha.
             Usamos "ml-auto" para empujar este <ul> al extremo derecho. -->
        <ul class="navbar-item flex-row ml-auto">
            <li class="nav-item dropdown user-profile-dropdown order-lg-0 order-1">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle user d-flex align-items-center"
                    id="userProfileDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <!-- Sección de info en línea: tienda y usuario -->
                    <div class="d-flex align-items-center user-info mr-2 text-right text-white" style="font-size:0.75rem;">
                        <div class="header-info d-flex align-items-center">
                            @if(Auth::check())
                            @php
                            // Obtenemos la primera tienda asociada al usuario
                            $firstStore = Auth::user()->stores->first();
                            // Del objeto Store, obtenemos el centro de costo asociado (si existe)
                            $centroCosto = $firstStore ? $firstStore->centroCosto : null;
                            @endphp

                            @if($centroCosto)
                            <div class="cost-center d-flex align-items-center mr-3">
                                <!-- Ícono de tienda reducido -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-shopping-bag">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                                <span class="ml-1">{{ $centroCosto->name }}</span>
                            </div>
                            @endif

                            <div class="user-info d-flex align-items-center">
                                <!-- Ícono de usuario reducido -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ml-1">{{ Auth::user()->name }}</span>
                            </div>
                            @else
                            <div class="guest-info d-flex align-items-center">
                                <!-- Ícono de usuario reducido -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ml-1">Invitado</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- Imagen fija (mega-carnes-frias.svg) -->
                    <!--  <img src="{{ asset('assets/img/mega-carnes-frias.svg') }}" alt="admin-profile" class="img-fluid"> -->
                </a>
                <!-- Dropdown -->
                <div class="dropdown-menu dropdown-menu-right custom-dropdown-right position-absolute animated fadeInUp"
                    aria-labelledby="userProfileDropdown">
                    <div class="user-profile-section">
                        <div class="media mx-auto">
                            @if(Auth::check() && Auth::user()->foto_perfil)
                            <img src="{{ asset('assets/img/avartar/1avatar.png') }}" class="img-fluid mr-2" alt="{{ Auth::user()->name }}">
                            @else
                            <img src="{{ asset('assets/img/avatar/1avatar.png') }}" class="img-fluid mr-2" alt="">
                            @endif
                            <div class="media-body" style="font-size:0.75rem;">
                                <h5 class="mb-0">@guest Usuario @else {{ Auth::user()->name }} @endguest</h5>
                                <p class="mb-0">@guest Perfil @else {{ Auth::user()->profile }} @endguest</p>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <a href="{{ route('users.profile') }}" class="d-flex align-items-center text-decoration-none text-dark" style="font-size:0.75rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-user mr-2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Mi Perfil</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit()"
                            class="d-flex align-items-center text-decoration-none text-dark" style="font-size:0.75rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out mr-2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Salir</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </li>
        </ul>
    </header>
</div>